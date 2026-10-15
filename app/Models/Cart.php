<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     

    function user()  {
        return $this->belongsTo(User::class);
    }
    function products(){
        return $this->belongsToMany(Product::class, 'cart_products')->withPivot('quantity')->withTimestamps();
    }
 
}
