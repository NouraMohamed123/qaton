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
        $notifications = ControlNotification::all();
        return response()->json($notifications);
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
