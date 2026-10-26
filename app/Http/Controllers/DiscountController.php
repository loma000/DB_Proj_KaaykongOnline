<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::all();
        return view('admin.discount', compact('discounts'));
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
        $validate = $request->validate([
            'coupon_name'   => 'required|string|max:255|unique:discounts,coupon_name',
            'discount_rate' => 'required|integer|min:1|max:100',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        abort_unless($request->user()?->role === 'ADMIN', 403);
        Discount::create($validate);
        return redirect()->route('admin.discount')->with(['status' => 'create discount successful']);
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
    public function update(Request $request, Discount $discount)
    {
        $validate = $request->validate([
            'coupon_name'   => 'required|string|max:255|unique:discounts,coupon_name',
            'discount_rate' => 'required|integer|min:1|max:100',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        abort_unless($request->user()?->role === 'ADMIN', 403);
        $discount->update($validate);
        return redirect()->route('admin.discount')->with(['status' => 'update discount successful']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        abort_unless(Auth::user()?->role === 'ADMIN', 403);
        $discount->delete();
        return redirect()->route('admin.discount')->with(['status' => 'delete discount successful']);
    }
}
