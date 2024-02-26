<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ApartmentRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ApartmentResource;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apartments = Apartment::with('rooms', 'reviews', 'images')->get();

        return ApartmentResource::collection($apartments);
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
    public function store(ApartmentRequest $request)
    {
        try {

            DB::beginTransaction();

            if ($request->hasFile('video') && $request->file('video')->isValid()) {
                $video =  upload($request->file('video'), public_path('uploads/apartments/vidios'));
            } else {
                $video = null;
            }
            $data = [
                'name' => $request->name,
                'unit_space' => $request->unit_space,
                'price' => $request->price,
                'bathrooms' => $request->bathrooms,
                'lounges' => $request->lounges,
                'dining_session' => $request->dining_session,
                'features' => json_encode($request->features),
                'view' => $request->view,
                'additional_features' => json_encode($request->additional_features),
                'area_id' => $request->area_id,
                'video' => $video,
                'parking' => $request->parking,
                'max_guests' => $request->max_guests,
                'status' => $request->status,
            ];


            $apartment =  Apartment::create($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $uploadedImage = upload($image, public_path('uploads/apartments'));
                        $apartment->images()->create(['image' => $uploadedImage]);
                    }
                }
            }
            foreach ($request->rooms as $roomData) {
                $room = new Room();
                $room->room_number = $roomData['room_number'];
                $room->beds = $roomData['beds'];
                $room->adult = $roomData['adult'];
                $room->child = $roomData['child'];
                $room->apartment_id = $apartment->id;
                $room->save();
            }
            DB::commit();
            return response()->json(['isSuccess' => true, 'data' => new ApartmentResource($apartment)], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        return new ApartmentResource($apartment);
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
    public function update(ApartmentRequest $request, Apartment $apartment)
    {
        try {
            DB::beginTransaction();


            if ($request->hasFile('video') && $request->file('video')->isValid()) {
                $video =  upload($request->file('video'), public_path('uploads/apartments/vidios'));
            } else {
                $video = $apartment->video;
            }
            $data = [
                'name' => $request->name,
                'unit_space' => $request->unit_space,
                'price' => $request->price,
                'bathrooms' => $request->bathrooms,
                'lounges' => $request->lounges,
                'dining_session' => $request->dining_session,
                'view' => $request->view,
                'features' => json_encode($request->features),
                'additional_features' => json_encode($request->additional_features),
                'area_id' => $request->area_id,
                'video' => $video,
                'parking' => $request->parking,
                'max_guests' => $request->max_guests,
                'status' => $request->status,
            ];

            $apartment->update($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $uploadedImage = upload($image, public_path('uploads/apartments'));
                        $apartment->images()->create(['image' => $uploadedImage]);
                    }
                }
            }
            foreach ($request->rooms as $roomData) {
                $room = Room::updateOrCreate(
                    [

                        'apartment_id' => $apartment->id,
                    ],
                    [
                        'room_number' => $roomData['room_number'],
                        'beds' => $roomData['beds'],
                        'adult' => $roomData['adult'],
                        'child' => $roomData['child'],
                    ]
                );
            }

            DB::commit();
            return response()->json(['isSuccess' => true, 'data' => new ApartmentResource($apartment)], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        $images = $apartment->images;
        if ($images) {
            foreach ($images as $image) {
                $imagePath = public_path('uploads/apartments/' . $apartment->image);


                if (File::exists($imagePath)) {

                    File::delete($imagePath);
                }
            }
        }
        if ($apartment) {
            if ($apartment->video) {
                $photoPath = 'uploads/apartments/vidios/' . $apartment->video;
                Storage::delete($photoPath);
            }
            $apartment->delete();
            return response()->json(['isSuccess' => true], 200);
        }
        return response()->json(['error' => 'no found'], 403);
    }
     public function changeStatus(Request $request){
       $apartment =  Apartment::where('id', $request->id)->first();
        $apartment->status = $request->status;
        $apartment->save();
        return response()->json(['isSuccess' => true, 'data' => new ApartmentResource($apartment)], 200);
    }
    public function apartmentCount()
    {
        $count = Apartment::count();

        return response()->json([
         
            "message" => "عملية العرض تمت بنجاح",
            'data' => $count
        ], 200);
    }
}
