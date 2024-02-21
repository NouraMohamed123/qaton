<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class OffersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     $offers =  Offer::get();
     return OfferResource::collection($offers);
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
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $avatar = $request->file('image');
            $image = upload($avatar,public_path('uploads/offers'));
        } else {
            $image = null;
        }
        $offer = new Offer();
        $offer->desc = $request->desc;
        $offer->image = $image;
        $offer->save();
        return response()->json(['isSuccess' => true,'data'=>new OfferResource( $offer)], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        return response()->json(['isSuccess' => true,'data'=>new OfferResource( $offer)], 200);
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
    public function update(Request $request, Offer $offer)
    {
        if (request()->has('image') &&  request('image') != '') {
            $avatar = request()->file('image');
            if ($avatar->isValid()) {
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = public_path('/uploads/offers');
                $avatar->move($avatarPath, $avatarName);
                $image  = $avatarName;
            }
        } else {
            $image =$offer->image;
        }
        $offer->desc = $request->desc;
        $offer->image = $image;
        $offer->save();
        return response()->json(['isSuccess' => true,'data'=> new OfferResource( $offer)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Offer $offer )
    {
        if($offer){
            if ($offer->image) {

                $imagePath = public_path('uploads/offers/' . $offer->image);


                if (File::exists($imagePath)) {

                    File::delete($imagePath);
                }

            }
            $offer->delete();
            return response()->json(['isSuccess' => true], 200);
        }

        return response()->json(['error' => 'no found'],403);
    }
}
