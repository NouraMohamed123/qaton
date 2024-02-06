<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')
        ->toArray();
        return  $settings;
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
            'site_logo' => '',
            'site_name' => '',
            'info_email'=> '',
            'mobile'=> '',
            'tiktok'=> '',
            'instagram' => '',
            'maintenance_mode' => '',
            'siteMaintenanceMsg' => '',
            'tax_added_value'=>'',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        foreach ($validator->validated() as $key => $input) {

            if (request()->hasFile('site_logo') && $request->file('site_logo')->isValid()) {

                $avatar = $request->file('site_logo');
                $image = upload($avatar,public_path('uploads/settings'));
                 $input =$image;

            }

          $settings =  Setting::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $input,
                ]
            );
        }

        return response()->json(['isSuccess' => true,'data'=> $settings], 200);
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

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
