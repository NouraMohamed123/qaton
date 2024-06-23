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
use App\Models\Image;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apartments = Apartment::with('rooms','images')->get();

        return ApartmentResource::collection($apartments);
    }
    public function lastApartment()
    {
        $apartments = Apartment::whereHas('BookedApartments', function ($query) {
            $query->where('paid', 1);
        })->latest()->take(5)->get();


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
            if ($request->hasFile('access_video') && $request->file('access_video')->isValid()) {
                $access_video =  upload($request->file('access_video'), public_path('uploads/apartments/access_video'));
            } else {
                $access_video = null;
            }
            $data = [
                'name' => $request->name,
                'code'=> $request->code,
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
                'status' => 1,
                'beds_childs' => $request->beds_childs,
                ////////////access data
                'access_video'=> $access_video,
                'website_link'=> $request->website_link,
                'login_instructions'=> $request->login_instructions,
                'internet_name'=>$request->internet_name,
                'internet_password'=>$request->internet_password,
                'instructions_prohibitions'=>$request->instructions_prohibitions,
                'apartment_features'=>$request->apartment_features,
                'contact_numbers'=>json_encode($request->contact_numbers),
                'secret_door'=>$request->secret_door,
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
            if ($request->hasFile('access_images')) {
                foreach ($request->file('access_images') as $image) {
                    if ($image->isValid()) {
                        $uploadedImage = upload($image, public_path('uploads/apartments-access'));
                        $apartment->AccessImages()->create(['image' => $uploadedImage]);
                    }
                }
            }
            foreach ($request->rooms as $roomData) {
                $room = new Room();
                $room->room_number = $roomData['room_number'];
                $room->beds = $roomData['beds'];
                $room->bathrooms = $roomData['bathrooms'];
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
            if ($request->hasFile('access_video') && $request->file('access_video')->isValid()) {
                $access_video =  upload($request->file('access_video'), public_path('uploads/apartments/access_video'));
            } else {
                $access_video = $apartment->access_video;
            }
            $data = [
                'name' => $request->name,
                'code'=> $request->code,
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
                'status' => 1,
                'beds_childs' => $request->beds_childs,
                ////////////access data
                'access_video'=> $access_video,
                'website_link'=> $request->website_link,
                'login_instructions'=> $request->login_instructions,
                'internet_name'=>$request->internet_name,
                'internet_password'=>$request->internet_password,
                'instructions_prohibitions'=>$request->instructions_prohibitions,
                'apartment_features'=>$request->apartment_features,
                'contact_numbers'=>json_encode($request->contact_numbers),
                'secret_door'=>$request->secret_door,
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
            if ($request->hasFile('access_images')) {
                foreach ($request->file('access_images') as $image) {
                    if ($image->isValid()) {
                        $uploadedImage = upload($image, public_path('uploads/apartments-access'));
                        $apartment->AccessImages()->create(['image' => $uploadedImage]);
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
                        'bathrooms' => $roomData['bathrooms'],
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
    public function changeStatus(Request $request)
    {
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
    public function copyApartment($id)
    {


        $old_apartment = Apartment::find($id);
        // Check if the apartment exists
        if (!$old_apartment) {
            return response()->json(['error' => 'Apartment not found'], 404);
        }

        $data = [
            'name' => $old_apartment->name,
            'unit_space' => $old_apartment->unit_space,
            'price' => $old_apartment->price,
            'bathrooms' => $old_apartment->bathrooms,
            'lounges' => $old_apartment->lounges,
            'dining_session' => $old_apartment->dining_session,
            'features' => json_encode($old_apartment->features),
            'view' => $old_apartment->view,
            'additional_features' => json_encode($old_apartment->additional_features),
            'area_id' => $old_apartment->area_id,
            'video' => $old_apartment->video,
            'parking' => $old_apartment->parking,
            'max_guests' => $old_apartment->max_guests,

            'status' => 1,
        ];


        $apartment =  Apartment::create($data);
        $old_images = Image::where('apartment_id', $id)->get();
        foreach ($old_images as $old_image) {
            // Create a new image for the new apartment
            $new_image = new Image();
            $new_image->image = $old_image->image; // Assuming 'image' is the attribute storing the image path
            $new_image->apartment_id = $apartment->id;
            $new_image->save();
        }


        foreach ($old_apartment->rooms as $roomData) {
            $room = new Room();
            $room->room_number = $roomData['room_number'];
            $room->beds = $roomData['beds'];
            $room->adult = $roomData['adult'];
            $room->child = $roomData['child'];
            $room->apartment_id = $apartment->id;
            $room->save();
        }

        return response()->json(['isSuccess' => true, 'data' => new ApartmentResource($apartment)], 200);
    }


}
