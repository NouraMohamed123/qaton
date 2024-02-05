<?php

namespace App\Http\Controllers\AppUser;

use App\Models\Term;
use App\Models\AboutUs;
use App\Models\Privacy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        //
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
