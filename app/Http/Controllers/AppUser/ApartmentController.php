<?php

namespace App\Http\Controllers\AppUser;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApartmentRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ApartmentResource;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {

        $area_id= checkPoints($request->lat, $request->lon);

       if( $area_id > 0 ){
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);
        $adults = $request->adults;
        $childs = $request->childs;

        $apartments = \App\Models\Apartment::where('status', 1)->where('area_id', $area_id)->with(['BookedApartments'=>function($BookedApartments) use ($checkInDate,$checkOutDate){
            $BookedApartments->where(function($q) use ($checkInDate,$checkOutDate){
                $q->where(function($qq) use ($checkInDate,$checkOutDate){
                    $qq->where('date_from','<=',$checkInDate)->where('date_to','>',$checkInDate);
                })->orWhere(function($qqq) use ($checkInDate,$checkOutDate){
                    $qqq->where('date_from','<=',$checkOutDate)->where('date_to','>=',$checkOutDate);
                });
            });
        }])->get();
        // dd( $apartments);
        foreach ($apartments as $apartment) {
            if($apartment->BookedApartments->count() > 0){
                return response()->json(['error' => 'Some apartment has already booked'],403 );
            }
        }
        if ($apartments->count() <= 0) {
            return response()->json(['error' => 'There is no apartment found'],403 );
        }else{

          $available_apartments = $apartments->filter(function ($apartment) use ($adults, $childs) {
            // dd($apartment->rooms);
                return $apartment->rooms->where('adult', '>=', $adults)->where('child', '>=', $childs)->isNotEmpty();
            });


            return response()->json(['isSuccess' => true,'data'=>   $available_apartments], 200);

        }
       }else{
        return response()->json(['error' => 'locton not found'],403 );
       }


    }


}
