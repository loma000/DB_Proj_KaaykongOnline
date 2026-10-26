<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_shows_clickable_payment_options_and_cart_summary(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $product = Product::create([
            'product_name' => 'Test product',
            'price' => 125,
            'description' => 'Test description',
        ]);
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $this->actingAs($user)
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Shipping address')
            ->assertSee('Credit / debit card')
            ->assertSee('PromptPay QR')
            ->assertSee('Cash on delivery')
            ->assertSee('Payment options are for display only and are not processed.')
            ->assertSee('฿250');
    }

    public function test_checkout_does_not_persist_shipping_address_or_payment_choice(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $product = Product::create([
            'product_name' => 'Test product',
            'price' => 100,
            'description' => 'Test description',
        ]);
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('checkout.place'), [
                'discount_id' => '',
                'shipping_address' => 'Address is UI only',
                'payment_method' => 'promptpay',
            ])
            ->assertOk();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'discount_id' => null,
            'total_price' => 200,
        ]);
        $this->assertFalse(Schema::hasColumn('orders', 'shipping_address'));
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);
        $this->assertDatabaseCount('cart_products', 0);
    }

    public function test_placing_order_redirects_to_its_summary(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $product = Product::create([
            'product_name' => 'Summary product',
            'price' => 125,
            'description' => 'Test description',
        ]);
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $response = $this->actingAs($user)->post(route('checkout.place'), ['discount_id' => null]);
        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.summary', $order));
        $this->actingAs($user)
            ->get(route('orders.summary', $order))
            ->assertOk()
            ->assertSee('Order placed successfully')
            ->assertSee('Summary product')
            ->assertSee('฿250.00');
    }

    public function test_order_summary_is_not_visible_to_another_user(): void
    {
        $owner = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'total_price' => 100,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('orders.summary', $order))
            ->assertNotFound();
    }

    public function test_order_total_uses_the_cart_coupon_discount_rate(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $discount = Discount::create([
            'coupon_name' => 'SAVE10',
            'discount_rate' => 10,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        $cart->update(['discount_id' => $discount->id]);
        $product = Product::create([
            'product_name' => 'Discounted product',
            'price' => 100,
            'description' => 'Test description',
        ]);
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $this->actingAs($user)
            ->post(route('checkout.place'), ['discount_id' => $discount->id])
            ->assertRedirect(route('orders.summary', Order::firstOrFail()));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'discount_id' => $discount->id,
            'total_price' => 180,
        ]);
    }
}
