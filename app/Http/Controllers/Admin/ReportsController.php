<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\Booked_apartment;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookedResource;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function all_orders()
    {
        $booked =  Booked_apartment::with('Apartment')->get();
        return BookedResource::collection($booked);
    }
    public function all_payments()
    {
        $payments = OrderPayment::with('booked')->latest()->get();
        return response()->json(['data'=> $payments], 200);
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
    public function reservation_request()
    {
        $booked =  Booked_apartment::where('status','pending')->with('Apartment')->get();
        return BookedResource::collection($booked);
    }
}
