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
            ->withAvg('reviews', 'score')->with('productImages')
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
    public function bestSellers()
    {
        $bestSellers = Product::query()->withAvg('reviews', 'score')
            ->withCount('reviews')->with('productImages')->orderByDesc('reviews_avg_score')->limit(5)->get();
        $catagories = Category::withCount('products')->get();
        return view('shop.home', compact('bestSellers', 'catagories'));
    }

    public function showProduct(Product $product)
    {
        $product = Product::withAvg('reviews', 'score')
            ->withCount('reviews')->with('productImages')
            ->with('reviews', 'stock')
            ->findOrFail($product->id);

        $reviews = $product->reviews;
        $stock = $product->stock;
        return view('shop.product', compact('product', 'reviews', 'stock'));
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
        $validated = $request->validate([
            'product_name'   => ['required', 'string', 'max:255'],
            'price'          => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description'    => ['required', 'string'],
            'image'          => ['nullable', 'string', 'max:255'],
            'category_ids'   => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);

        $product = Product::create($validated);
        $product->categories()->attach($categoryIds);

        return redirect()
            ->route('products.show', $product)
            ->with('status', 'create product successful');
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
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name'   => ['required', 'string', 'max:255'],
            'price'          => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description'    => ['required', 'string'],
            'image'          => ['nullable', 'string', 'max:255'],
            'category_ids'   => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);
        $category_id = $validated['category_ids'];
        unset($validated['category_ids']);
        $product->update($validated);
        $product->categories()->sync($category_id);
        return redirect()->route('admin.products')->with(['status' => 'update product success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with(['status' => 'delete successfull']);
    }
}
