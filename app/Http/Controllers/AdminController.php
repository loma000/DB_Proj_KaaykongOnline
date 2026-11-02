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
            'lowStockProducts' => Product::whereHas('stock', fn($q) => $q->where('quantity', '<=', 10))->with('stock')->take(5)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $users = User::withCount('orders')
            ->when($request->q, fn($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(15);

        return view('pages.admin.users', compact('users'));
    }

    public function products(Request $request)
    {
        $activeCategory = $request->query('category');
        $categories = Category::pluck('category_name');
        $products = Product::with(['categories', 'stock', 'productImages'])
            ->when($activeCategory, function ($query, $category) {
                $query->whereHas('categories', fn($q) => $q->where('category_name', $category));
            })
            ->when($request->query('q'), function ($query, $term) {
                $query->where('product_name', 'like', "%{$term}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();
        return view('pages.admin.products', compact('products', 'categories', 'activeCategory'));
    }

    public function categories(Request $request)
    {
        $categories = Category::withCount('products')
            ->when($request->q, fn($q, $term) => $q->where('category_name', 'like', "%{$term}%"))
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();
        return view('pages.admin.categories', compact('categories'));
    }

    public function orders(Request $request)
    {
        $orders = Order::with(['user', 'products.productImages', 'discount'])
            ->when($request->q, fn($q, $term) => $q->where('id', 'like', "%{$term}%")->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%")))
            ->when($request->period === 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($request->period === 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->period === 'month', fn($q) => $q->whereMonth('created_at', now()->month))
            ->latest()
            ->paginate(15);
        $activePeriod = $request->query('period', 'all');

        return view('pages.admin.orders', compact('orders', 'activePeriod'));
    }

    public function coupons(Request $request)
    {
        $coupons = Discount::withCount('orders')
            ->when($request->q, fn($q, $term) => $q->where('coupon_name', 'like', "%{$term}%"))
            ->when($request->status === 'active', fn($q) => $q->whereDate('start_date', '<=', today())->whereDate('end_date', '>=', today()))
            ->when($request->status === 'expired', fn($q) => $q->whereDate('end_date', '<', today()))
            ->paginate(15);
        $activeStatus = $request->query('status', 'all');

        return view('pages.admin.coupons', compact('coupons', 'activeStatus'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name'     => 'required|string|max:255',
            'category_ids'     => 'required|array|min:1',
            'category_ids.*'   => 'exists:categories,id',
            'price'            => 'required|numeric|min:0|max:10000000',
            'stock'            => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'thumbnail'        => 'nullable|image|max:5120',
            'images'           => 'nullable|array|max:10',
            'images.*'         => 'image|max:5120',
            'deleted_images'   => 'nullable|array',
            'deleted_images.*' => 'string|max:255',
        ], [
            'category_ids.required' => 'Please select at least one category.',
            'category_ids.min'      => 'Please select at least one category.',
        ]);
        // 1. Update product basic details
        $product->update([
            'product_name' => $validated['product_name'],
            'price'        => $validated['price'],
            'description'  => $validated['description'] ?? '',
        ]);
        // 2. Sync multiple categories
        $categoryIds = $request->input('category_ids') ?? ($request->filled('category_id') ? [$request->input('category_id')] : []);
        $product->categories()->sync($categoryIds);
        // 3. Update stock quantity
        $product->stock()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $validated['stock']]
        );
        // 4. Remove selected existing pictures
        $imagesToDelete = $product->productImages()
            ->whereIn('image', $validated['deleted_images'] ?? [])
            ->get();
        foreach ($imagesToDelete as $productImage) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($productImage->image);
        }
        $product->productImages()
            ->whereIn('image', $imagesToDelete->pluck('image'))
            ->delete();
        // 5. If a new thumbnail was uploaded, replace the old thumbnail
        if ($request->hasFile('thumbnail')) {
            // Find and delete the current thumbnail from disk and database
            $oldThumbnail = $product->productImages()->orderBy('image', 'asc')->first();
            if ($oldThumbnail) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldThumbnail->image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldThumbnail->image);
                }
                $product->productImages()->where('image', $oldThumbnail->image)->delete();
            }
            // Save the new thumbnail with '0_thumb_' prefix so it is ordered first
            $thumb = $request->file('thumbnail');
            $ext = $thumb->getClientOriginalExtension() ?: 'jpg';
            $thumbName = '0_thumb_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $ext;
            $thumbPath = $thumb->storeAs('products', $thumbName, 'public');
            $product->productImages()->create([
                'image' => $thumbPath,
            ]);
        }
        // 6. Add any new gallery pictures with '1_img_' prefix
        foreach ($request->file('images', []) as $index => $image) {
            $ext = $image->getClientOriginalExtension() ?: 'jpg';
            $imgName = '1_img_' . time() . '_' . $index . '_' . \Illuminate\Support\Str::random(8) . '.' . $ext;
            $newPath = $image->storeAs('products', $imgName, 'public');
            $product->productImages()->create([
                'image' => $newPath,
            ]);
        }
        return back()->with('status', 'Product updated successfully.');
    }
    public function destroyProduct(Product $product)
    {
        // Delete all images for this product from disk
        foreach ($product->productImages as $img) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($img->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image);
            }
        }
        // Delete product record (cascades to product_images, stock, categories)
        $product->delete();
        return back()->with('status', 'Product deleted successfully.');
    }
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name'     => 'required|string|max:255',
            'category_ids'     => 'required|array|min:1',
            'category_ids.*'   => 'exists:categories,id',
            'price'            => 'required|numeric|min:0|max:10000000',
            'stock'            => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'thumbnail'        => 'nullable|image|max:5120',
            'images'           => 'nullable|array|max:10',
            'images.*'         => 'image|max:5120',
        ], [
            'category_ids.required' => 'Please select at least one category.',
            'category_ids.min'      => 'Please select at least one category.',
        ]);
        // 1. Create product
        $product = Product::create([
            'product_name' => $validated['product_name'],
            'price'        => $validated['price'],
            'description'  => !empty($validated['description']) ? $validated['description'] : $validated['product_name'],
        ]);
        // 2. Attach categories
        $categoryIds = $request->input('category_ids') ?? ($request->filled('category_id') ? [$request->input('category_id')] : []);
        $product->categories()->sync($categoryIds);
        // 3. Create initial stock
        $product->stock()->create([
            'quantity' => $validated['stock'],
        ]);
        // 4. Save Thumbnail Picture first (prefixed with 0_thumb_ so it is always ordered first in MySQL)
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $ext = $thumb->getClientOriginalExtension() ?: 'jpg';
            $thumbName = '0_thumb_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $ext;
            $thumbPath = $thumb->storeAs('products', $thumbName, 'public');
            $product->productImages()->create([
                'image' => $thumbPath,
            ]);
        }
        // 5. Save Product Pictures (prefixed with 1_img_ so they come after the thumbnail)
        $images = $request->file('images', []);
        foreach ($images as $index => $image) {
            $ext = $image->getClientOriginalExtension() ?: 'jpg';
            // If no separate thumbnail was uploaded, treat the first picture as the thumbnail
            $prefix = (!$request->hasFile('thumbnail') && $index === 0) ? '0_thumb_' : '1_img_';
            $imgName = $prefix . time() . '_' . $index . '_' . \Illuminate\Support\Str::random(8) . '.' . $ext;
            $imagePath = $image->storeAs('products', $imgName, 'public');
            $product->productImages()->create([
                'image' => $imagePath,
            ]);
        }
        return back()->with('status', 'Product created successfully.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'image'         => 'nullable|image|max:5120', // Up to 5MB
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }
        Category::create([
            'category_name' => trim($validated['category_name']),
            'image'         => $imagePath,
        ]);
        return back()->with('status', 'Category created successfully.');
    }
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('categories', 'category_name')->ignore($category->id)],
            'image'         => 'nullable|image|max:5120',
        ]);
        $updateData = [
            'category_name' => trim($validated['category_name']),
        ];
        // If a new replacement picture is uploaded, remove old image and save new one
        if ($request->hasFile('image')) {
            if ($category->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }
            $updateData['image'] = $request->file('image')->store('categories', 'public');
        }
        $category->update($updateData);
        return back()->with('status', 'Category updated successfully.');
    }
    public function destroyCategory(Category $category)
    {
        // Delete image from disk if present
        if ($category->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return back()->with('status', 'Category deleted successfully.');
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
