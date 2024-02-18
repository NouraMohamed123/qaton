<?php

namespace App\Http\Controllers\AppUser;

use App\Models\Review;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ApartmentResource;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
     {

         $user = Auth::guard('app_users')->user();

         if (!$user) {
             return response()->json(['error' => 'User not authenticated'], 401);
         }


         $apartments = Apartment::whereHas('reviews', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();


        return response()->json(['data'=> ApartmentResource::collection( $apartments) ], 200);
     }
        //


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
    public function store(ReviewRequest $request)
    {



        $reviews = Review::create([
            'user_id' => Auth::guard('app_users')->user()->id,
            'apartment_id' => $request->apartment_id,
            'descriptions' => $request->descriptions,
            'rating' => $request->rating,
        ]);
        return response()->json(['isSuccess' => true, 'data' =>  $reviews], 200);
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
    public function update(ReviewRequest $request, Review $review)
    {


        $review->update([
            'user_id' => Auth::guard('app_users')->user()->id,
            'apartment_id' => $request->apartment_id,
            'descriptions' => $request->descriptions,
            'rating' => $request->rating,
        ]);
        return response()->json(['isSuccess' => true, 'data' =>   $review], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}
