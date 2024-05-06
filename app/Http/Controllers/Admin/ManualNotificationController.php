<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\AppUsers;
use Illuminate\Http\Request;
use App\Models\ManualNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use App\Events\ManalNotificationWorkersEvent;
use App\Notifications\ManalNotificationWorkers;

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
        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            ManualNotification::create([
                'user_id' => $user->id,
                'date' => $request->date,
                'time'=>$request->time,
                'message' => $request->message,
            ]);
            $notificationTime = Carbon::parse($request->time);
            $hours =$notificationTime->hour;
            $minutes =$notificationTime->minute;
            $seconds =$notificationTime->second;
            $notificationDate = Carbon::parse($request->date)
                ->addHours($hours)
                ->addMinutes($minutes)
                ->addSeconds($seconds);
            $user->notify((new ManalNotificationWorkers($request->message))->delay($notificationDate));
            $ManalNotificationWorkersEvent = new ManalNotificationWorkersEvent($request->message);
            Queue::push(function ($job) use ($ManalNotificationWorkersEvent, $notificationDate) {
                Event::dispatch($ManalNotificationWorkersEvent);
                $job->release($notificationDate);
            });
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
