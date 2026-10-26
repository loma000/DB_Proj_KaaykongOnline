<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_requested_quantity_is_added_to_the_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'product_name' => 'Test product',
            'price' => 10,
            'description' => 'Test description',
        ]);
        Stock::create(['product_id' => $product->id, 'quantity' => 10]);

        $this->actingAs($user)
            ->post(route('cart.create', $product), ['quantity' => 3])
            ->assertRedirect();

        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $this->assertDatabaseHas('cart_products', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->actingAs($user)
            ->post(route('cart.create', $product), ['quantity' => 2])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_products', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_quantity_cannot_exceed_available_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'product_name' => 'Test product',
            'price' => 10,
            'description' => 'Test description',
        ]);
        Stock::create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('cart.create', $product), ['quantity' => 3])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('cart_products', 0);
    }

    public function test_discount_can_be_applied_from_the_discount_list(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $discount = Discount::create([
            'coupon_name' => 'SAVE10',
            'discount_rate' => 10,
            'start_date' => today()->toDateString(),
            'end_date' => today()->addDay()->toDateString(),
        ]);

        $this->actingAs($user)
            ->post(route('cart.discount', $discount))
            ->assertRedirect();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'discount_id' => $discount->id,
        ]);

        $this->actingAs($user)
            ->post(route('cart.discount.clear'))
            ->assertRedirect();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'discount_id' => null,
        ]);
    }
}
