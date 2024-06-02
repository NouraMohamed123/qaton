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
use App\Http\Resources\ApartmentResourceMobile;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function search(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'city_id' => 'required|exists:cities,id',
    //         'check_in_date' => 'required|date',
    //         'check_out_date' => 'required|date|after:check_in_date',
    //         'max_guests' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors(),
    //         ], 422);
    //     }
    //     $areas_id  =  Area::where('city_id', $request->city_id)->pluck('id');
    //     if ($areas_id) {
    //         $checkInDate = Carbon::parse($request->check_in_date);
    //         $checkOutDate = Carbon::parse($request->check_out_date);

    //         //////booked apartment booked
    //         $apartments = \App\Models\Apartment::with('reviews')->where('status', 1)->whereIn('area_id',  $areas_id)->with(['BookedApartments' => function ($BookedApartments) use ($checkInDate, $checkOutDate) {
    //             $BookedApartments->where(function ($q) use ($checkInDate, $checkOutDate) {
    //                 $q->where(function ($qq) use ($checkInDate, $checkOutDate) {
    //                     $qq->where('date_from', '<=', $checkInDate)->where('date_to', '>', $checkInDate);
    //                 })->orWhere(function ($qqq) use ($checkInDate, $checkOutDate) {
    //                     $qqq->where('date_from', '<=', $checkOutDate)->where('date_to', '>=', $checkOutDate);
    //                 });
    //             });
    //         }])->get();

    //         foreach ($apartments as $apartment) {

    //             if ($apartment->BookedApartments->count() > 0)
    //             {
    //                 return response()->json(['error' => 'لقد تم حجز  الشقق بالفعل'], 403);
    //             }
    //         }
    //         if ($apartments->count() <= 0) {
    //             return response()->json(['error' => 'لم يتم العثور على شقة'], 403);
    //         } else {

    //             $available_apartments = $apartments->where('max_guests', '>=', $request->max_guests);
    //             if ($available_apartments->count() > 0) {
    //                 return response()->json(['isSuccess' => true, 'data' => ApartmentResource::collection($available_apartments)], 200);
    //             }
    //             return response()->json(['error' => 'لا توجد غرف مناسبة لك '], 403);
    //         }
    //     } else {
    //         return response()->json(['error' => 'الموقع غير موجود'], 403);
    //     }
    // }
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'max_guests' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $areas_id = Area::where('city_id', $request->city_id)->pluck('id');

        if ($areas_id->isEmpty()) {
            return response()->json(['error' => 'الموقع غير موجود'], 403);
        }

        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);


        $diffInDays = $checkOutDate->diffInDays($checkInDate);

        // Fetch apartments with potential bookings to check against
        $apartments = \App\Models\Apartment::with(['reviews','prices','rooms','BookedApartments' => function ($query) use ($checkInDate, $checkOutDate) {
            $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->whereBetween('date_from', [$checkInDate, $checkOutDate])
                  ->orWhereBetween('date_to', [$checkInDate, $checkOutDate]);
            });
        }])
        ->where('status', 1)
        ->whereIn('area_id', $areas_id)
        ->get();

        $apartments->each(function ($apartment) use ($checkInDate, $checkOutDate) {
            $booked = $apartment->BookedApartments->contains(function ($booking) use ($checkInDate, $checkOutDate) {
                $dateFrom = Carbon::parse($booking->date_from);
                $dateTo = Carbon::parse($booking->date_to);
                return $dateFrom->lessThanOrEqualTo($checkOutDate)
                    && $dateTo->greaterThanOrEqualTo($checkInDate);

                });

                $apartment->available = $booked ? 0 : 1;
                $apartment->save() ;
            });

        $available_apartments = $apartments->where('max_guests', '>=', $request->max_guests);

        if ($available_apartments->isEmpty()) {
            return response()->json(['error' => 'لم يتم العثور على شقة مناسبة'], 403);
        }
        $available_apartments->each(function ($apartment) use ($checkInDate, $checkOutDate) {
            $apartment->nights = $checkOutDate->diffInDays($checkInDate);
        });
        $userId = Auth::guard('app_users')->user()->id;
        $available_apartments->each(function ($apartment) use ($userId) {
            $apartment->favorited_by_user = $apartment->favorites->contains('user_id', $userId);
        });
        return response()->json(['isSuccess' => true,
        'data' => ApartmentResourceMobile::collection($available_apartments),

    ], 200);
    }


    public function allApartments(Request $request)
    {


        $apartments = \App\Models\Apartment::with('reviews')->where('status', 1)->get();

        return response()->json(['isSuccess' => true, 'data' => ApartmentResourceMobile::collection($apartments)], 200);
        if ($apartments->count() <= 0) {
            return response()->json(['error' => 'There is no apartment found'], 403);
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'bathrooms' => 'required|integer',
                'lounges' => 'required|integer',
                'view' => 'required|string',
                'area_id' => 'required|exists:areas,id',
                'max_rooms' => 'nullable|integer',
            ]);
            DB::beginTransaction();

            $user =  AppUsers::where('id', Auth::guard('app_users')->user()->id)->first();
            if ($user) {
                $user->update([
                    'type' => 1,
                ]);
            }
            $data = [
                'name' => $request->name,
                'bathrooms' => $request->bathrooms,
                'lounges' => $request->lounges,
                'view' => $request->view,
                'area_id' => $request->area_id,
                'max_rooms' => $request->max_rooms,
                'owner_id' => Auth::guard('app_users')->user()->id,
                'status' => 0,
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
