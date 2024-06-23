<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\price;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceController extends Controller
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
    public function store(Request $request)
    {
       $priceData = $request->all();
        $startDate = Carbon::parse($priceData['start_date']);
        $endDate = Carbon::parse($priceData['end_date']);

        $currentDate = $startDate;
        while ($currentDate->lte($endDate)) {
            $existingPrice = Price::where('apartment_id', $priceData['apartment_id'])
                ->where('date', $currentDate->format('Y-m-d'))
                ->first();

            if ($existingPrice) {
                $existingPrice->price = $priceData['price'];
                $existingPrice->save();
            } else {
                $price = new Price();
                $price->apartment_id = $priceData['apartment_id'];
                $price->price = $priceData['price'];
                $price->date = $currentDate->format('Y-m-d');
                $price->save();
            }

            $currentDate->addDay();
        }

        return response()->json(['isSuccess' => true , 'data'=> $price], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $prices =  Price::where('apartment_id', $id)->get();
      return response()->json(['isSuccess' => true,'data'=>$prices], 200);
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
