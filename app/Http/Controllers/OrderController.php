<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all()->sortBy('created_at');
        return view('admin.orders', compact('orders'));
    }

    public function orderByUser(Request $request){
        $orders = $request->user()->orders()->get();
        return view('shop.orders', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'discount_id' => ['nullable', 'integer', 'exists:discounts,id'],
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $data) {
            $cart = Cart::with('products')->where('user_id', $user->id)->firstOrFail();

            abort_if($cart->products->isEmpty(), 422, 'Cart is empty');

            $subtotal = $cart->products->sum(fn ($product) => $product->price * $product->pivot->quantity);
            $discount = null;

            if (! empty($data['discount_id'])) {
                $discount = Discount::query()
                    ->whereKey($data['discount_id'])
                    ->whereKey($cart->discount_id)
                    ->whereDate('start_date', '<=', today())
                    ->whereDate('end_date', '>=', today())
                    ->firstOrFail();
            }

            $discountAmount = $discount ? round($subtotal * $discount->discount_rate / 100) : 0;
            $total = $subtotal - $discountAmount;

            $order = Order::create([
                'user_id' => $user->id,
                'discount_id' => $data['discount_id'] ?? null,
                'total_price' => $total,
            ]);

            foreach ($cart->products as $product) {
                $order->products()->attach($product->id, [
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->price,
                ]);
            }

            $cart->products()->detach();

            return $order;
        });

        return redirect()->route('orders.summary', $order)->with('success', 'Order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function orderSummary(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['products', 'discount']);

        return view('shop.orderSummary', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
