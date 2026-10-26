<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [ProductController::class, 'showProduct'])->name('products.show');

Route::get('account', function () {
    $user = auth()->user();

    $orders = $user->orders()
        ->with(['products', 'discount'])
        ->latest()
        ->take(3)
        ->get();

    $reviews = $user->reviews() 
        ->with('product')
        ->latest()
        ->take(3)
        ->get();

    return view('shop.account', compact('user', 'orders', 'reviews'));
})->middleware('auth')->name('account');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->controller(AdminController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('users', 'users')->name('users');
    Route::get('products', 'products')->name('products');
    Route::get('orders', 'orders')->name('orders');
    Route::get('coupons', 'coupons')->name('coupons');
    Route::patch('products/{product}', 'updateProduct')->name('products.update');
    Route::post('products', 'storeProduct')->name('products.store');
    Route::delete('products/{product}', 'destroyProduct')->name('products.destroy');
    Route::post('users', 'storeUser')->name('users.store');
    Route::patch('users/{user}', 'updateUser')->name('users.update');
    Route::delete('users/{user}', 'destroyUser')->name('users.destroy');
    Route::delete('orders/{order}', 'destroyOrder')->name('orders.destroy');
    Route::post('coupons', 'storeCoupon')->name('coupons.store');
    Route::patch('coupons/{coupon}', 'updateCoupon')->name('coupons.update');
    Route::delete('coupons/{coupon}', 'destroyCoupon')->name('coupons.destroy');
});
