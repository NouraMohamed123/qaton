<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use Illuminate\Support\Facades\Storage;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cities = City::paginate($request->get('per_page', 50));
        return CityResource::collection($cities);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        if ($request->file('image')) {
            $avatar = $request->file('image');
            $avatar->store('uploads/city/', 'public');
            $image = $avatar->hashName();
        } else {
            $image = null;
        }
        $city = new City();
        $city->name = $request->name;
        $city->image = $image;
        $city->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return CityResource::collection($city);
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
    public function update(CityRequest $request, City $city)
    {
        if ($request->file('image')) {
            $avatar = $request->file('image');
            $avatar->store('uploads/cities/', 'public');
            $image = $avatar->hashName();
        } else {
            $image = null;
        }
        $city->name = $request->name;
        $city->image = $image;
        $city->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        if ($city->image) {
            $photoPath = 'uploads/cities/' . $city->image;
            Storage::delete($photoPath);
        }
        // Delete the user
        $city->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}
