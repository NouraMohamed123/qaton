<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Models\Booked_apartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookedResource;

class BookedApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $booked =  Booked_apartment::with('Apartment')->get();
      return BookedResource::collection($booked);
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

        $apartment_list = explode(',', $request->apartment_list);
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        $totalDays = $checkInDate->diffInDays($checkOutDate);

        if($checkInDate == $checkOutDate){
            return response()->json(['error' => 'Check in and Check out date should not be same'],403 );
        }
        $apartments = Apartment::where('status',1)->whereIn('id',$apartment_list)->with(['BookedApartments'=>function($BookedApartments) use ($checkInDate,$checkOutDate){
            $BookedApartments->where(function($q) use ($checkInDate,$checkOutDate){
                $q->where(function($qq) use ($checkInDate,$checkOutDate){
                    $qq->where('date_from','<=',$checkInDate)->where('date_to','>',$checkInDate);
                })->orWhere(function($qqq) use ($checkInDate,$checkOutDate){
                    $qqq->where('date_from','<=',$checkOutDate)->where('date_to','>=',$checkOutDate);
                });
            });
        }])->get();
        foreach ($apartments as $apartment) {
            if($apartment->BookedApartments->count() > 0){
                return response()->json(['error' => 'Some apartment has already booked'],403 );
            }
        }
        if ($apartments->count() <= 0) {
            return response()->json(['error' => 'There is no apartment found'],403 );
        }
        $totalPrice = $apartments->sum('price');
        $totalPrice = $totalPrice * $totalDays;
        foreach ($apartments as $apartment) {
        Booked_apartment::create([
            // 'user_id'=>Auth::guard('app_users')->user()->id,
            'user_id'=>1,
            'apartment_id'=>$apartment->id,
            'total_price'=>$totalPrice,
            'date_from'=>$checkInDate,
            'date_to'=>$checkOutDate
        ]);
    }
    return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booked_apartment $booked)
    {
        return  new BookedResource($booked);
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
    public function update(Request $request, Booked_apartment $booked)
    {
        $apartment_list = explode(',', $request->apartment_list);
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        $totalDays = $checkInDate->diffInDays($checkOutDate);

        if($checkInDate == $checkOutDate){
            return response()->json(['error' => 'Check in and Check out date should not be same'],403 );
        }
        $apartments = Apartment::where('status',1)->whereIn('id',$apartment_list)->with(['BookedApartments'=>function($BookedApartments) use ($checkInDate,$checkOutDate){
            $BookedApartments->where(function($q) use ($checkInDate,$checkOutDate){
                $q->where(function($qq) use ($checkInDate,$checkOutDate){
                    $qq->where('date_from','<=',$checkInDate)->where('date_to','>',$checkInDate);
                })->orWhere(function($qqq) use ($checkInDate,$checkOutDate){
                    $qqq->where('date_from','<=',$checkOutDate)->where('date_to','>=',$checkOutDate);
                });
            });
        }])->get();
        foreach ($apartments as $apartment) {
            if($apartment->BookedApartments->count() > 0){
                return response()->json(['error' => 'Some apartment has already booked'],403 );
            }
        }
        if ($apartments->count() <= 0) {
            return response()->json(['error' => 'There is no apartment found'],403 );
        }
        $totalPrice = $apartments->sum('price');
        $totalPrice = $totalPrice * $totalDays;
        foreach ($apartments as $apartment) {

            $booked->update([
            // 'user_id'=>Auth::guard('app_users')->user()->id,
            'user_id'=>1,
            'apartment_id'=>$apartment->id,
            'total_price'=>$totalPrice,
            'date_from'=>$checkInDate,
            'date_to'=>$checkOutDate
        ]);
    }
    return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booked_apartment $booked)
    {
        $booked->delete();
        return response()->json(['isSuccess' => true], 200);

    }
}
