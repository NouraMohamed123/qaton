<?php

namespace App\Http\Controllers\Admin;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Requests\AreaRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $areas = Area::with('city')->get();
        return AreaResource::collection($areas);
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
    public function store(AreaRequest $request)
    {
        $area = new Area();
        $area->name = $request->name;
        $area->city_id = $request->city_id;
        $area->boundaries = $request->boundaries;
        $area->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $area = Area::with('city')->where('id', $id)->first();
        return new AreaResource($area);
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
    public function update(AreaRequest $request, Area $area)
    {
        $area = new Area();
        $area->name = $request->name;
        $area->city_id = $request->city_id;
        $area->boundaries = $request->boundaries;
        $area->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return response()->json(['isSuccess' => true], 200);
    }
}