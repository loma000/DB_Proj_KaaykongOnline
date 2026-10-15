<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name', 'price', 'description', 'image'];
  public  function categories()
    {
        return $this->belongsToMany(Category::class, 'products_categories')->withTimestamps();
    }
   public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_products')->withPivot('quantity')->withTimestamps();
    }
   public function stock()
    {
        return $this->hasOne(Stock::class);
    }
   public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')->withPivot(['quantity', 'price'])->withTimestamps();
    }
   public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
