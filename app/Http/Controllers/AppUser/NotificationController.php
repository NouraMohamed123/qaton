<?php

namespace App\Http\Controllers\APPUser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function NotificationRead($type){
        if (!empty(Auth::guard('app_users')->user()->notifications)) {
            if ($type == 'booking') {
                $notifications = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\BookingUser');
            } elseif ($type == 'entry') {
                $notifications = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\UserLogin');
            } elseif ($type == 'exit') {
                $notifications = Auth::guard('app_users')->user()->notifications->where('type', 'App\Notifications\UserLogout');
            }
        } else {
            $notifications = [];
        }

        $data = [];
        foreach ($notifications as $notification) {
            $data[] = $notification;
        }

      return  response()->json(['isSuccess' => true, 'data' => $data], 200);

    }
    public function MarkASRead($type){
        if (!empty(Auth::guard('app_users')->user()->notifications)) {
            if($type =='booking'){
                $notifications  =   Auth::guard('app_users')->user()->notifications->where('type','App\Notifications\BookingUser')->markAsRead();
        }elseif($type =='entry'){
                $notifications  =   Auth::guard('app_users')->user()->notifications->where('type','App\Notifications\UserLogin')->markAsRead();
        }elseif($type =='exit'){
                $notifications  =   Auth::guard('app_users')->user()->notifications->where('type','App\Notifications\UserLogout')->markAsRead();
        }

        return response()->json(['isSuccess' => true], 200);
        }
        return response()->json(['isSuccess' => false], 422);
    }
    public function Clear($type){
        if(!empty(Auth::guard('app_users')->user()->notifications)){
            if($type =='booking'){
                $notifications  =   Auth::guard('app_users')->user()->notifications()->where('type','App\Notifications\BookingUser')->delete();
            }elseif($type =='entry'){
                $notifications  =   Auth::guard('app_users')->user()->notifications()->where('type','App\Notifications\UserLogin')->delete();
            }elseif($type =='exit'){
                $notifications  =   Auth::guard('app_users')->user()->notifications()->where('type','App\Notifications\UserLogout')->delete();
            }
            return response()->json(['isSuccess' => true,'data'=> $notifications ], 200);
        }

        return response()->json(['isSuccess' => false,'error' => 'user it has no notification'], 401);

    }
}
