<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\AppUsers;
use Illuminate\Http\Request;
use App\Models\ManualNotification;
use App\Http\Controllers\Controller;

class ManualNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

         $notificationDate = $request->date;
         $message = $request->message;
         $user = null;

         if ($request->app_user_id) {
             $user = AppUsers::find($request->app_user_id);
         } elseif ($request->user_id) {
             $user = User::find($request->user_id);
         }

         if ($user ) {

            $user->notifiable()->create([
                'type' => $request->type,
                'date' => $request->date,
                'message' => $request->message,
            ]);


            //  if ($notificationDate) {
            //      $user->notify(new ManualNotification($message))->delay($notificationDate);
            //  } else {
            //      $user->notify(new ManualNotification($message));
            //  }
         }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
