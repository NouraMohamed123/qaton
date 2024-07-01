<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Point;
use App\Models\Coupon;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Events\UserLoginEvent;
use App\Events\UserLogoutEvent;
use App\Events\BookingUserEvent;
use App\Models\Booked_apartment;
use App\Notifications\UserLogin;
use App\Notifications\UserLogout;
use App\Notifications\BookingUser;
use Illuminate\Support\Facades\DB;
use App\Events\BookingToAdminEvent;
use App\Traits\NotificationControl;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Http\Resources\BookedResource;
use App\Http\Resources\OrderPaymentResource;
use Illuminate\Support\Facades\Notification;

class ReportsController extends Controller
{
    use NotificationControl;
    /**
     * Display a listing of the resource.
     */
    public function all_orders(Request $request)
    {
        $booked =  Booked_apartment::with('Apartment')->latest()->paginate($request->get('per_page', 50));
        return BookedResource::collection($booked);
    }
    public function all_payments(Request $request)
    {
        $payments = OrderPayment::with('booked.user')->latest()->paginate($request->get('per_page', 50));
        return OrderPaymentResource::collection($payments);
    }
    public function payments($id)
    {
        $payments = OrderPayment::with('booked.user')->where('booked_id', $id)->first();

        return new OrderPaymentResource($payments);
    }
    public function orderCount()
    {
        $count = Booked_apartment::count();

        return response()->json([
            "message" => "عملية العرض تمت بنجاح",
            'data' => $count
        ], 200);
    }
    // public function Immediate_follow_up(){
    //     $booked =  Booked_apartment::with('Apartment')->get();
    //  }
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
        //
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
    public function reservation_request()
    {
        $booked =  Booked_apartment::where('status','pending')->with('Apartment')->get();
        return BookedResource::collection($booked);
    }
    public function accept_reservation_request(Request $request)
    {
        $booked =  Booked_apartment::where('id',$request->id)->first();
        DB::beginTransaction();
        OrderPayment::create([
            'name' => 'cash',
            'customer_name' => $booked->user->name,
            'booked_id' => $booked->id,
            'price' =>  $booked->total_price,
            'is_success'=>1,
            'invoice_status' => "Paid",
        ]);
        $booked->paid = 1;
        $booked->status = 'recent';
        $booked->save();
        $user =  $booked->user;
          // send notification to user
          $notificationData = $this->controlNotification('booking');
          Notification::send($user, new BookingUser($notificationData['message'],$notificationData['title']));
          BookingUserEvent::dispatch($notificationData['message'],$notificationData['title']);

          //notification to login user
          $notificationData = $this->controlNotification('entry_day');
          $notificationTime = Carbon::parse($notificationData['time']);
              $hours = $notificationTime->hour;
              $minutes = $notificationTime->minute;
              $seconds = $notificationTime->second;
              $notificationDate = Carbon::parse($booked->date_from)
              ->addHours($hours)
              ->addMinutes($minutes)
              ->addSeconds($seconds);
          $user->notify((new UserLogin($notificationData['message'],$booked,$notificationData['title']))->delay($notificationDate));
          $userLoginEvent = new UserLoginEvent($notificationData['message'], $booked,$notificationData['title']);
          Queue::push(function($job) use ($userLoginEvent, $notificationDate) {
              Event::dispatch($userLoginEvent);
              $job->release($notificationDate);
          });
          //notification to logout user
          $notificationData = $this->controlNotification('exit_day');
          $hours = $notificationTime->hour;
          $minutes = $notificationTime->minute;
          $seconds = $notificationTime->second;
          $notificationDate = Carbon::parse($booked->date_to)
          ->addHours($hours)
          ->addMinutes($minutes)
          ->addSeconds($seconds);
          $user->notify((new UserLogout($notificationData['message'],$booked,$notificationData['title']))->delay($notificationDate));
          $UserLogoutEvent = new UserLogoutEvent($notificationData['message'],$booked,$notificationData['title']);
          Queue::push(function($job) use ($UserLogoutEvent, $notificationDate) {
              Event::dispatch($UserLogoutEvent);
              $job->release($notificationDate);
          });

          ///broadcast event booked user
          BookingToAdminEvent::dispatch($user, $booked->apartment);
          ////////insert to points
          Point::where('user_id', $user->id)->delete();
          Point::create([
              'booked_id' => $booked->id,
              'user_id' => $user->id,
              'point' => $booked->total_price
          ]);
            ///////////
            if ($booked->coupon_id != 0) {
                Coupon::where('id', $booked->coupon_id)->decrement('max_usage');
            }
            DB::commit();
            return response()->json(['isSuccess' => true,'message'=>'تم قبول الطلب'], 200);
    }

}
