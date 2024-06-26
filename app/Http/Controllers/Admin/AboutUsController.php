<?php

namespace App\Http\Controllers\Admin;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AboutUsResource;
use Illuminate\Support\Facades\Validator;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $about_us = AboutUs::paginate($request->get('per_page', 50));
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
     $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $about_us = new AboutUs();
        $about_us->description = $request->description;
        $about_us->save();
        return response()->json(['isSuccess' => true,'data'=> new AboutUsResource($about_us)], 200);
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
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $about_us->description = $request->description;
        $about_us->save();
        return response()->json(['isSuccess' => true,'data'=> new AboutUsResource($about_us)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutUs $about_us)
    {

        if($about_us){
            $about_us->delete();
            return response()->json(['isSuccess' => true], 200);
        }
        return response()->json(['error' => 'no found'],403);
    }
}
