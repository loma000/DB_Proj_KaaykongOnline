<?php
namespace App\Models;

 
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;
    protected $table = 'categories';
    protected $fillable = ['category_name'];
    function products()
    {
        return $this->belongsToMany (Product::class, 'products_categories')->withTimestamps();
    }
}
