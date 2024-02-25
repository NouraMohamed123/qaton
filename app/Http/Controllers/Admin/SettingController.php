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
            $image = asset('uploads/settings/' .  $settings['site_logo']);
            $settings['site_logo'] =    $image;
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
            'info_email' => '',
            'mobile' => '',
            'tiktok' => '',
            'instagram' => '',
            'maintenance_mode' => '',
            'siteMaintenanceMsg' => '',
            'tax_added_value' => '',
            'site_name_en' => '',
            'cr' => '',
            'vat' => '',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
    
        $settings = [];
    
        foreach ($validator->validated() as $key => $input) {
            if ($key === 'site_logo' && $request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
                $file = $request->file('site_logo');
                $fileName = 'custom_name.' . $file->getClientOriginalExtension(); // Define your custom file name here
                $filePath = $file->storeAs('uploads/settings', $fileName, 'public');
    
                $input = $fileName; // Store the file path in input
            }
    
            Setting::updateOrCreate(['key' => $key], ['value' => $input]);
        }
    
        // Fetch the stored settings after the update
        $storedSettings = Setting::pluck('value', 'key')->toArray();
    
        // Update the site_logo URL in the settings array if it exists
        if (isset($storedSettings['site_logo'])) {
            $storedSettings['site_logo'] = asset('storage/' . $storedSettings['site_logo']);
        }
    
        return response()->json(['isSuccess' => true, 'data' => $storedSettings], 200);
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
