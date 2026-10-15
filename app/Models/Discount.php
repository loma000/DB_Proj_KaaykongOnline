<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $timestamps = false;
    protected $fillable = ['coupon_name', 'discount_rate', 'start_date', 'end_date'];

    function orders()
    {
        return $this->hasMany(Order::class);
    }
}
