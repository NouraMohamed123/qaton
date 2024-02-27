<?php

namespace App\Http\Controllers\AppUser;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Room;
use App\Models\AppUsers;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ApartmentRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ApartmentResource;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'check_in_date'=> 'required|date',
            'check_out_date'=> 'required|date|after:check_in_date',
            'adults'=>'required',
            'childs'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $areas_id  =  Area::where('city_id', $request->city_id)->pluck('id');
       if( $areas_id  ){
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);
        $adults = $request->adults;
        $childs = $request->childs;

        $apartments = \App\Models\Apartment::with('reviews')->where('status', 1)->whereIn('area_id',  $areas_id )->with(['BookedApartments'=>function($BookedApartments) use ($checkInDate,$checkOutDate){
            $BookedApartments->where(function($q) use ($checkInDate,$checkOutDate){
                $q->where(function($qq) use ($checkInDate,$checkOutDate){
                    $qq->where('date_from','<=',$checkInDate)->where('date_to','>',$checkInDate);
                })->orWhere(function($qqq) use ($checkInDate,$checkOutDate){
                    $qqq->where('date_from','<=',$checkOutDate)->where('date_to','>=',$checkOutDate);
                });
            });
        }])->get();
       //  dd( $apartments);
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
            if ( $available_apartments->count() > 0 ) {
                return response()->json(['isSuccess' => true,'data'=> ApartmentResource::collection($available_apartments)  ], 200);

            }
            return response()->json(['error' => 'There is no rooms suitable for you '],403 );


        }
       }else{
        return response()->json(['error' => 'locton not found'],403 );
       }


    }
   public function allApartments(Request $request){


    $apartments = \App\Models\Apartment::with('reviews')->where('status', 1)->get();

          return response()->json(['isSuccess' => true,'data'=> ApartmentResource::collection( $apartments )  ], 200);
    if ($apartments->count() <= 0) {
        return response()->json(['error' => 'There is no apartment found'],403 );
    }


   }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'price' => 'required|numeric',
                'bathrooms' => 'required|integer',
                'lounges' => 'required|integer',
                'view' => 'required|string',
                'area_id' => 'required|exists:areas,id',
                'max_rooms' => 'nullable|integer',
            ]);
            DB::beginTransaction();

          $user =  AppUsers::where('id',Auth::guard('app_users')->user()->id)->first();
          if($user){
            $user->update([
                'type'=>1,
              ]);
          }
            $data = [
                'name' => $request->name,
                'price' => $request->price,
                'bathrooms' => $request->bathrooms,
                'lounges' => $request->lounges,
                'view' => $request->view,
                'area_id' => $request->area_id,
                'max_rooms' => $request->max_rooms,
                'owner_id'=>Auth::guard('app_users')->user()->id,
                'status'=>0,
            ];


            $apartment =  Apartment::create($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $uploadedImage = upload($image, public_path('uploads/apartments'));
                        $apartment->images()->create(['image' => $uploadedImage]);
                    }
                }
            }
           DB::commit();
           return response()->json(['isSuccess' => true], 200);
        } catch (\Exception $e) {
             DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
