<?php

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('products', [ProductController::class,'index'])->name('products.index');
Route::get('products/{product}', [ProductController::class,'showProduct'])->name('products.show');

Route::view('cart', 'shop.cart')->name('cart');
Route::view('checkout', 'shop.checkout')->name('checkout');
Route::view('account', 'shop.account')->name('account');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::view('/', 'pages.admin.dashboard')->name('dashboard');
    Route::view('users', 'admin.users')->name('users');
    Route::view('products', 'admin.products')->name('products');
    Route::view('orders', 'admin.orders')->name('orders');
    Route::view('coupons', 'admin.coupons')->name('coupons');
});