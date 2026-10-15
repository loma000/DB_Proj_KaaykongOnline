<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $timestamps = false;
   protected $fillable = ['quantity', 'product_id'];
    function product()
    {
        return $this->belongsTo(Product::class);
    }
}
