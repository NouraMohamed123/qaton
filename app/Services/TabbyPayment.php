<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
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
use App\Models\ControlNotification;
use Illuminate\Support\Facades\Http;
use App\Notifications\BookingToAdmin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class TabbyPayment
{
    public function __construct()
    {

        // $tabby = PaymentGetway::where([
        //     ['keyword', 'Tabby'],
        // ])->first();
        // $tabbyConf = json_decode($tabby->information, true);
        // dd($tabbyConf);
        // Config::set('services.tabby.pk_test ',$tabbyConf["pk_test"]);
        // Config::set('services.tabby.sk_test  ',$tabbyConf["sk_test"]);
        // Config::set('services.tabby.base_url','https://api.tabby.ai/api/v2/');

        Config::set('services.tabby.pk_test', 'pk_test_6ff1345a-5791-441c-9b66-96c6b32c445d');
        Config::set('services.tabby.sk_test', 'sk_test_45f9e11d-7f70-4da8-9ea8-daa13fcfb094');
        Config::set('services.tabby.base_url', 'https://api.tabby.ai/api/v2/');
    }






    public function createSession($data)
    {

        $body = $this->getConfig($data);

     $http = Http::withToken(Config::get('services.tabby.pk_test'))
       ->baseUrl(Config::get('services.tabby.base_url'))
       ->withOptions([
           'verify' => false
       ]);

        $response = $http->post('checkout', $body);

        return $response->object();
    }
    public function getSession($payment_id)
    {

        $http = Http::withToken(Config::get('services.tabby.sk_test'))->baseUrl(Config::get('services.tabby.base_url'));

        $url = 'payments/' . $payment_id;

        $response = $http->get($url);

        return $response->object();
    }

    public function getConfig($data)
    {
        $body = [];

        $body = [
            "payment" => [
                // "is_test"=>true,
                "amount" => $data['amount'],
                "currency" => $data['currency'],
                "description" =>  $data['description'],
                "buyer" => [
                    "phone" => $data['buyer_phone'],
                    "email" => $data['buyer_email'],
                    "name" => $data['full_name'],
                    "dob" => "2019-08-24"
                ],
                "shipping_address" => [
                    "city" => $data['city'],
                    "address" =>  $data['address'],
                    "zip" => $data['zip'],
                ],
                "order" => [
                    "tax_amount" => "0.00",
                    "shipping_amount" => "0.00",
                    "discount_amount" => "0.00",
                    "updated_at" =>  "2019-08-24T14:15:22Z",
                    "reference_id" => $data['order_id'],
                    "items" =>
                    $data['items'],
                ],
                "buyer_history" => [
                    "registered_since" => $data['registered_since'],
                    "loyalty_level" => $data['loyalty_level'],
                ],
            ],
            "lang" => app()->getLocale(),
            "merchant_code" => "شركة  قطون",
            "merchant_urls" => [
                "success" => $data['success-url'],
                "cancel" => $data['cancel-url'],
                "failure" => $data['failure-url'],
            ]
        ];

        return $body;
    }

    public function calbackPayment(Request $request)
    {
        $response = $this->getSession($request->payment_id);

        if ($response->status == "CLOSED") {

            $booked = Booked_apartment::where('id', $response->order->reference_id)->first();
            $payment =    OrderPayment::where('booked_id', $booked->id)->first();

            if ($response->status == "CLOSED") {
                try {
                    DB::beginTransaction();
                    OrderPayment::create([
                        'name' => 'tabby',
                        'customer_name' => $booked->user->name,
                        'invoice_id' => $request->payment_id,
                        'booked_id' => $booked->id,
                        'price' =>  $booked->total_price,
                        'is_success'=>1,
                        'invoice_status' => "Paid",
                    ]);
                    $booked->paid = 1;
                    $booked->status = 'recent';
                    $booked->save();

                    /////
                    $user =  $booked->user;
                    // send notification to admins
                    $admins = User::all();
                    Notification::send($admins, new BookingToAdmin($user, $booked->apartment));
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
                    return response()->json(['isSuccess' => true, 'Data' => 'payment success'], 200);
                } catch (\Throwable $th) {
                    dd($th->getMessage(), $th->getLine());
                    DB::rollBack();
                    return response()->json(["error" => 'error', 'Data' => 'payment failed'], 404);
                }
            }
        }
    }

    public function controlNotification($type)
    {
        $message = '';
        $time = '';
        if ($type == 'booking') {
            $message = ControlNotification::where('type', 'booking')->value('message');
            $time = ControlNotification::where('type', 'booking')->value('time');
        } elseif ($type == 'entry_day') {
            $message = ControlNotification::where('type', 'entry_day')->value('message');
            $time = ControlNotification::where('type', 'entry_day')->value('time');
        } elseif ($type == 'exit_day') {
            $message = ControlNotification::where('type', 'exit_day')->value('message');
            $time = ControlNotification::where('type', 'exit_day')->value('time');
        } else {
            $message = 'Default message';
            $time = '';
        }

        return ['message' => $message, 'time' => $time];
    }
}
