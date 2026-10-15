<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('categories')
            ->withAvg('reviews', 'score')
            ->withCount('reviews')
            ->when($request->query('category'), function ($query, $category) {
                $query->whereHas('categories', fn($q) => $q->where('category_name', $category));
            })
            ->when($request->query('q'), function ($query, $term) {
                $query->where('product_name', 'like', "%{$term}%");
            })
            ->when($request->query('sort'), function ($query, $sort) {
                match ($sort) {
                    'price_asc'  => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'rating'     => $query->orderByDesc('reviews_avg_score'),
                    default      => $query->latest(),
                };
            }, fn($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::pluck('category_name'); // ['Cookware', 'Knives', ...] เหมือน mock
        $activeCategory = $request->query('category');

        return view('shop.products', compact('products', 'categories', 'activeCategory'));
    }


   public function showProduct(Product $product)
{
    $product = Product::withAvg('reviews', 'score')
        ->withCount('reviews')
        ->with('reviews')
        ->findOrFail($product->id);

    $reviews = $product->reviews;

    return view('shop.product', compact('product', 'reviews'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
