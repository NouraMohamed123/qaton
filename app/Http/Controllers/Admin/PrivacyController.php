<?php

namespace App\Http\Controllers\Admin;

use App\Models\Privacy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrivacyResource;
use Illuminate\Support\Facades\Validator;

class PrivacyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $privacy = Privacy::all();
        return PrivacyResource::collection($privacy);
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
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $privacy = new Privacy();
        $privacy->description = $request->description;
        $privacy->save();
        return response()->json(['isSuccess' => true,'data'=>new PrivacyResource($privacy)], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Privacy $privacy)
    {
        return  new PrivacyResource($privacy);
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
    public function update(Request $request,Privacy $privacy)
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
        $privacy->description = $request->description;
        $privacy->save();
        return response()->json(['isSuccess' => true,'data'=>new PrivacyResource($privacy)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Privacy $privacy)
    {
        $privacy->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}
