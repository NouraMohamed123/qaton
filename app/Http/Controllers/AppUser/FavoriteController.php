<?php

namespace App\Http\Controllers\AppUser;

use App\Models\Favorit;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ApartmentResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ApartmentResourceMobile;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Favorit $favorit)
    // {
    //     $user = Auth::guard('app_users')->user();
    //     $favorites = $user->favorit;
    //     return response()->json($favorites);
    // }
    public function index()
    {
        $user = Auth::guard('app_users')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }


        $apartments = Apartment::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();


        return response()->json(['data'=> ApartmentResourceMobile::collection( $apartments) ], 200);
    }


    public function store(Request $request)
    {
        $user = Auth::guard('app_users')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $validator = Validator::make($request->all(), [
            'apartment_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => 'Empty fields'], 422);
        }
        $favorit =  Favorit::create([
            'user_id' => Auth::guard('app_users')->user()->id,
            'apartment_id' => $request->apartment_id,

        ]);
        return response()->json(['isSuccess' => true,'data' => $favorit], 200);

    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Empty fields'],422);
        }

        $favorit =  favorit::where('user_id', Auth::guard('app_users')->user()->id)->first();
        if( $favorit){
            $favorit->delete();
        }else{
            $favorit =  Favorit::create([
                'user_id' => Auth::guard('app_users')->user()->id,
                'apartment_id' => $request->apartment_id,

            ]);
        }

        return response()->json(['isSuccess' => true,'data' => $favorit], 200);
    }


}
