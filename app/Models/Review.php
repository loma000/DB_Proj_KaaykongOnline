<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'score', 'comment'];
   
    function user() {
        return $this -> belongsTo (User::class);
    }
    function product () {
        return $this -> belongsTo (Product::class);
    }
}