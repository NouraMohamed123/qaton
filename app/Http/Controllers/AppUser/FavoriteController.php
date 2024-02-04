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
            'user_id' => ['required'],
            'apartment_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => 'Empty fields'], 200);
        }
        Favorit::create([
            // 'user_id' => Auth::guard('app_users')->user()->id,
            'user_id' => $request->user_id,
            'apartment_id' => $request->apartment_id,
            
        ]);
        return response()->json(['isSuccess' => true], 200);

    }

    
    public function show(string $id)
    {
        //
    }

        
    public function update(Request $request, Favorit $favorit)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'apartment_id' => ['required'],
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Empty fields'], 200);
        }
    
        $favorit->update([
            'user_id' => $request->user_id,
            'apartment_id' => $request->apartment_id,
        ]);
    
        return response()->json(['isSuccess' => true], 200);
    }

    public function destroy(Favorit $favorit)
    {
        $favorit->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}
