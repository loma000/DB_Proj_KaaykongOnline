<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class,'bestSellers'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('review.create');
    Route::post('products/{product}/cart', [CartController::class, 'store'])->name('cart.create');
    Route::get('cart', [CartController::class, 'index'])->name('cart');
    Route::post('cart/discount/none', [CartController::class, 'clearDiscount'])->name('cart.discount.clear');
    Route::post('cart/discount/{discount}', [CartController::class, 'useDiscount'])->name('cart.discount');
    Route::patch('cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{product}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::get('checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('checkout', [OrderController::class, 'store'])->name('checkout.place');
    Route::get('orders/{order}/summary', [OrderController::class, 'orderSummary'])->name('orders.summary');
});

require __DIR__.'/settings.php';
require __DIR__.'/ui.php';
