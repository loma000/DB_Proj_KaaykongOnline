<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('pages.admin.dashboard', [
            'usersCount'       => User::where('role', '!=', 'ADMIN')->count(),
            'productsCount'    => Product::count(),
            'ordersCount'      => Order::count(),
            'revenue'          => Order::sum('total_price') ?? 0,
            'recentOrders'     => Order::with('user')->latest()->take(5)->get(),
            'lowStockProducts' => Product::whereHas('stock', fn ($q) => $q->where('quantity', '<=', 10))->with('stock')->take(5)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $users = User::withCount('orders')
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(15);

        return view('pages.admin.users', compact('users'));
    }

    public function products(Request $request)
    {
        $activeCategory = $request->query('category');
        $categories = Category::pluck('category_name');
        $products = Product::with(['categories', 'stock'])
            ->when($activeCategory, function ($query, $category) {
                $query->whereHas('categories', fn ($q) => $q->where('category_name', $category));
            })
            ->when($request->query('q'), function ($query, $term) {
                $query->where('product_name', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return view('pages.admin.products', compact('products', 'categories', 'activeCategory'));
    }

    public function orders(Request $request)
    {
        $orders = Order::with(['user', 'products', 'discount'])
            ->when($request->q, fn ($q, $term) => $q->where('id', 'like', "%{$term}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")))
            ->when($request->period === 'today', fn ($q) => $q->whereDate('created_at', today()))
            ->when($request->period === 'week', fn ($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->period === 'month', fn ($q) => $q->whereMonth('created_at', now()->month))
            ->latest()
            ->paginate(15);
        $activePeriod = $request->query('period', 'all');

        return view('pages.admin.orders', compact('orders', 'activePeriod'));
    }

    public function coupons(Request $request)
    {
        $coupons = Discount::withCount('orders')
            ->when($request->q, fn ($q, $term) => $q->where('coupon_name', 'like', "%{$term}%"))
            ->when($request->status === 'active', fn ($q) => $q->whereDate('end_date', '>=', today()))
            ->when($request->status === 'expired', fn ($q) => $q->whereDate('end_date', '<', today()))
            ->paginate(15);
        $activeStatus = $request->query('status', 'all');

        return view('pages.admin.coupons', compact('coupons', 'activeStatus'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:5120',
        ]);
        $updateData = [
            'product_name' => $validated['product_name'],
            'price'        => $validated['price'],
            'description'  => $validated['description'] ?? '',
        ];
        // If a replacement image was uploaded, save it and remove old image if present
        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $updateData['image'] = $request->file('image')->store('products', 'public');
        }
        // 1. Update product details
        $product->update($updateData);
        // 2. Sync category
        $product->categories()->sync([$validated['category_id']]);
        // 3. Update stock quantity
        $product->stock()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $validated['stock']]
        );
        return back()->with('status', 'Product updated successfully.');
    }

    public function destroyProduct(Product $product)
    {
        // 1. Delete product image from storage if present
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }
        // 2. Delete product record (cascades to stock, categories, etc.)
        $product->delete();
        return back()->with('status', 'Product deleted successfully.');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:5120', // Up to 5MB
        ]);
        // Store the uploaded picture into storage/app/public/products
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        // 1. Create product with picture path
        $product = Product::create([
            'product_name' => $validated['product_name'],
            'price'        => $validated['price'],
            'description'  => !empty($validated['description']) ? $validated['description'] : $validated['product_name'],
            'image'        => $imagePath,
        ]);
        // 2. Attach category
        $product->categories()->sync([$validated['category_id']]);
        // 3. Create initial stock
        $product->stock()->create([
            'quantity' => $validated['stock'],
        ]);
        return back()->with('status', 'Product created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role'     => 'nullable|string|in:USER,ADMIN',
            'password' => 'nullable|string|min:6',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['role'])) {
            $user->role = $validated['role'];
        }
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();
        return back()->with('status', 'User updated successfully.');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'nullable|string|in:USER,ADMIN',
        ]);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->role = $validated['role'] ?? 'USER';
        $user->save();
        return back()->with('status', 'User created successfully.');
    }

    public function destroyUser(User $user)
    {
        // Prevent deleting the currently logged-in admin account
        if (Auth::id() === $user->id) {
            return back()->with('status', 'You cannot delete your own account.');
        }
        $user->delete();
        return back()->with('status', 'User deleted successfully.');
    }

    public function destroyOrder(Order $order)
    {
        $order->delete();
        return back()->with('status', 'Order deleted successfully.');
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_name'   => 'required|string|max:255|unique:discounts,coupon_name',
            'discount_rate' => 'required|integer|min:1|max:100',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        Discount::create([
            'coupon_name'   => strtoupper(trim($validated['coupon_name'])),
            'discount_rate' => $validated['discount_rate'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
        ]);
        return back()->with('status', 'Coupon created successfully.');
    }
    
    public function updateCoupon(Request $request, $id)
    {
        $coupon = Discount::findOrFail($id);
        $validated = $request->validate([
            'coupon_name'   => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('discounts', 'coupon_name')->ignore($coupon->id)],
            'discount_rate' => 'required|integer|min:1|max:100',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        $coupon->update([
            'coupon_name'   => strtoupper(trim($validated['coupon_name'])),
            'discount_rate' => $validated['discount_rate'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
        ]);
        return back()->with('status', 'Coupon updated successfully.');
    }

    public function destroyCoupon($id)
    {
        $coupon = Discount::findOrFail($id);
        $coupon->delete();
        return back()->with('status', 'Coupon deleted successfully.');
    }
}