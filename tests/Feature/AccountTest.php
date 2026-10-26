<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_requires_authentication(): void
    {
        $this->get(route('account'))
            ->assertRedirect(route('login'));
    }

    public function test_account_page_shows_only_the_signed_in_users_data(): void
    {
        $user = User::factory()->create(['name' => 'Current Member']);
        $otherUser = User::factory()->create(['name' => 'Other Member']);
        $product = Product::create([
            'product_name' => 'Chef knife',
            'price' => 1200,
            'description' => 'A kitchen knife.',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 1200,
        ]);
        $order->products()->attach($product, ['quantity' => 1, 'price' => 1200]);

        $otherOrder = Order::create([
            'user_id' => $otherUser->id,
            'total_price' => 1200,
        ]);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'score' => 5,
            'comment' => 'Excellent knife.',
        ]);

        $this->actingAs($user)
            ->get(route('account'))
            ->assertOk()
            ->assertSee('Current Member')
            ->assertSee('Chef knife')
            ->assertSee('Excellent knife.')
            ->assertSee('#'.$order->id)
            ->assertDontSee('#'.$otherOrder->id);
    }
}