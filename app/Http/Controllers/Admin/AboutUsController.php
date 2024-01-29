<?php

namespace App\Http\Controllers\Admin;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AboutUsResource;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $about_us = AboutUs::all();
        return AboutUsResource::collection($about_us);
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
        $about_us = new AboutUs();
        $about_us->description = $request->description;
        $about_us->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutUs $about_us)
    {
        return  new AboutUsResource($about_us);
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
    public function update(Request $request,AboutUs $about_us)
    {

        $about_us->description = $request->description;
        $about_us->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutUs $about_us)
    {
        $about_us->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}
