<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\Favorit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Favorit $favorit)
    {
        $user = Auth::guard('app_users')->user();
        $favorites = $user->favorit;
        return response()->json($favorites);
    }


    public function store(Request $request)
    {
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