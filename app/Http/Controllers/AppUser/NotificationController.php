<?php

namespace App\Http\Controllers\APPUser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function NotificationRead()
    {
        $notifications = [];

        if (!empty(Auth::guard('app_users')->user()->notifications)) {
            $notifications['booking'] = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\BookingUser')->values()->all();
            $notifications['entry'] = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\UserLogin')->values()->all();
            $notifications['exit'] = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\UserLogout')->values()->all();
        } else {
            $notifications = [
                'booking' => [],
                'entry' => [],
                'exit' => [],
            ];
        }

        return response()->json(['isSuccess' => true, 'data' => $notifications], 200);
    }
    public function MarkASRead()
    {
        if (!empty(Auth::guard('app_users')->user()->notifications)) {

            $notifications  =   Auth::guard('app_users')->user()->notifications->markAsRead();
            return response()->json(['isSuccess' => true], 200);
        }


        return response()->json(['isSuccess' => false], 422);
    }
    public function Clear()
    {
        if (!empty(Auth::guard('app_users')->user()->notifications)) {

            $notifications  =   Auth::guard('app_users')->user()->notifications()->delete();

            return response()->json(['isSuccess' => true, 'data' => $notifications], 200);
        }

        return response()->json(['isSuccess' => false, 'error' => 'user it has no notification'], 401);
    }
}
