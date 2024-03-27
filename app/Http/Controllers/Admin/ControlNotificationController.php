<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ControlNotification;
use App\Http\Controllers\Controller;

class ControlNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data['booking'] = ControlNotification::where('type', 'booking')->first();
        $data['entry_day'] = ControlNotification::where('type', 'entry_day')->first();
        $data['exit_day'] = ControlNotification::where('type', 'exit_day')->first();

        return response()->json($data);
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:booking,entry_day,exit_day',
            'time' => 'nullable|date_format:H:i',
            'message' => 'nullable|string',
        ]);

        $notification = ControlNotification::create($request->all());
        return response()->json($notification, 200);
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
    public function update(Request $request, ControlNotification $notification )
    {
        $validatedData = $request->validate([
            'type' => 'required|in:booking,entry_day,exit_day',
            'time' => 'nullable|date_format:H:i',
            'message' => 'nullable|string',
        ]);
        $notification->update($request->all());
        return response()->json($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ControlNotification $notification)
    {

        return response()->json(['isSuccess' => true], 200);
    }
}
