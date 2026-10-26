<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->cart;
        $products = $cart?->products()->withPivot('quantity')->get() ?? collect();

        $count = $products->sum(fn($product) => $product->pivot->quantity);
        $subtotal = $products->sum(fn($p) => $p->price * $p->pivot->quantity);
        $coupon = $cart?->discount;
        if ($coupon && ($coupon->start_date > today()->toDateString() || $coupon->end_date < today()->toDateString())) {
            $coupon = null;
        }
        $discount = $coupon ? round($subtotal * $coupon->discount_rate / 100) : 0;
        $shipping = 0;
        $total = $subtotal - $discount + $shipping;
        $discounts = Discount::query()
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->get();

        return view('shop.cart', compact('products', 'count', 'subtotal', 'shipping', 'coupon', 'discount', 'total', 'discounts'));
    }

    public function checkout(Request $request)
    {
        $cart = $request->user()->cart;
        $products = $cart?->products()->withPivot('quantity')->get() ?? collect();

        if ($products->isEmpty()) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        $count = $products->sum(fn($product) => $product->pivot->quantity);
        $subtotal = $products->sum(fn($product) => $product->price * $product->pivot->quantity);
        $coupon = $cart?->discount;
        if ($coupon && ($coupon->start_date > today()->toDateString() || $coupon->end_date < today()->toDateString())) {
            $coupon = null;
        }
        $discount = $coupon ? round($subtotal * $coupon->discount_rate / 100) : 0;
        $shipping = 0;
        $total = $subtotal - $discount + $shipping;

        return view('shop.checkout', compact('products', 'count', 'subtotal', 'coupon', 'discount', 'shipping', 'total'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $existing = $cart->products()->where('product_id', $product->id)->first();
        $currentQuantity = $existing?->pivot->quantity ?? 0;
        $availableQuantity = $product->stock()->value('quantity') ?? 0;

        if ($currentQuantity + $validated['quantity'] > $availableQuantity) {
            throw ValidationException::withMessages([
                'quantity' => 'The requested quantity exceeds available stock.',
            ]);
        }

        if ($existing) {
            $cart->products()->updateExistingPivot($product->id, ['quantity' => $existing->pivot->quantity + $validated['quantity']]);
        } else {
            $cart->products()->attach($product->id, ['quantity' => $validated['quantity']]);
        }

        return back()->with('status', 'เพิ่มลงตะกร้าแล้ว');
    }

    public function useDiscount(Request $request, Discount $discount)
    {
        if ($discount->start_date > today()->toDateString() || $discount->end_date < today()->toDateString()) {
            return back()->withErrors(['discount_id' => 'This coupon is not currently available.']);
        }

        $cart = $request->user()->cart()->firstOrFail();
        $cart->update(['discount_id' => $discount->id]);

        return back()->with('status', 'Coupon applied.');
    }

    public function clearDiscount(Request $request)
    {
        $request->user()->cart?->update(['discount_id' => null]);

        return back()->with('status', 'Coupon removed.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:0']);
        $cart = $request->user()->cart()->firstOrFail();
        abort_unless($cart->products()->whereKey($product->id)->exists(), 404);
        $availableQuantity = $product->stock()->value('quantity') ?? 0;

        if ((int)$validated['quantity'] === 0) {
            $cart->products()->detach($product->id);
            return back();
        }

        if ($validated['quantity'] > $availableQuantity) {
            throw ValidationException::withMessages([
                'quantity' => 'The requested quantity exceeds available stock.',
            ]);
        }
        $cart->products()->updateExistingPivot($product->id, ['quantity' => $validated['quantity']]);

        return back();
    }

    public function destroy(Request $request, Product $product)
    {
        $cart = $request->user()->cart()->firstOrFail();
        $cart->products()->detach($product->id);

        return back();
    }
}
