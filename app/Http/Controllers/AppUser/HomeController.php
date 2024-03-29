<?php

namespace App\Http\Controllers\AppUser;

use App\Models\Area;
use App\Models\City;
use App\Models\Term;
use App\Models\Offer;
use App\Models\AboutUs;
use App\Models\Privacy;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\PaymentGeteway;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\TermsResource;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\PrivacyResource;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function about_us()
    {
        $about_us = AboutUs::get();
        return AboutUsResource::collection($about_us);
    }
    public function privacy()
    {
        $privacy = Privacy::get();
        return PrivacyResource::collection($privacy);
    }
    public function terms()
    {
        $terms = Term::get();
        return TermsResource::collection($terms);
    }
    public function cities()
    {
        $terms = City::get();
        return CityResource::collection($terms);
    }
    public function areas()
    {
        $terms = Area::get();
        return AreaResource::collection($terms);
    }
    public function settings()
    {
        $settings = Setting::pluck('value', 'key')
            ->toArray();
        $image = asset('uploads/settings/' .  $settings['site_logo']);
        $settings['site_logo'] =    $image;
        return  $settings;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function offers()
    {
        $offers =  Offer::get();
        return OfferResource::collection($offers);
    }


    public function paymentGetway()
    {
        $data['myfatoorah'] = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
        if ($data['myfatoorah']) {
            $data['myfatoorah']->image = asset('uploads/myfatoorah/' . $data['myfatoorah']->image) ;
        }

        return response()->json([
            "isSuccess" => true,
            'data' =>   [
                'keyword'=>$data['myfatoorah']->keyword,
                'image'=>$data['myfatoorah']->image,
            ]
        ], 200);
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
