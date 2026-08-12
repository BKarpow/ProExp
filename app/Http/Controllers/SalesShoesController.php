<?php

namespace App\Http\Controllers;

use App\Models\SalesShoes;
use App\Http\Requests\StoreSalesShoesRequest;
use App\Http\Requests\UpdateSalesShoesRequest;

class SalesShoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreSalesShoesRequest $request)
    {
        $s = new SalesShoes();
        $s->user_id = $request->user()->id;
        $s->models_id = $request->models_id;
        $s->size = $request->size;
        $s->price = $request->price;
        $s->save();
        return response()->json([
            'status' => true,
            'salId' => $s->id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesShoes $salesShoes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesShoes $salesShoes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesShoesRequest $request, SalesShoes $salesShoes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesShoes $salesShoes)
    {
        //
    }
}
