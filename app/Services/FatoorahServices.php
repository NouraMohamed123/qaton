<?php

namespace App\Services;

use App\ModelTax;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Point;
use GuzzleHttp\Client;
use App\Models\OrderPayment;
use GuzzleHttp\Psr7\Request;
use App\Models\PaymentGeteway;
use App\Events\BookedUserEvent;
use App\Models\Booked_apartment;
use App\Notifications\UserLogin;
use App\Notifications\BookedUser;
use App\Notifications\UserLogout;
use App\Notifications\BookingUser;
use Illuminate\Support\Facades\DB;
use App\Models\ControlNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloguent\Model;
use Illuminate\Support\Facades\Notification;

class FatoorahServices
{
    private $base_url;
    private $headers;
    private $request_client;

    public function __construct()
    {
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
        $this->request_client = $guzzleClient;
        $myfatoorah = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
        $myfatoorahConf = json_decode($myfatoorah->information, true);
        Config::set('services.myfatoorah.api_token', "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL");

        Config::set('services.myfatoorah.base_url', 'https://apitest.myfatoorah.com/');
        $this->base_url =  config('services.myfatoorah.base_url');

        $this->headers = [
            "Content-Type" => 'application/json',
            'authorization' => 'Bearer ' . config('services.myfatoorah.api_token')

        ];
    }

    public function buildRequest($url, $mothod, $data = [])
    {

        $request = new Request($mothod, $this->base_url . $url, $this->headers);

        if (!$data)
            return false;
        $response = $this->request_client->send($request, ['json' => $data]);

        if ($response->getStatusCode() != 200)
            return false;
        $response = json_decode($response->getBody(), true);

        return $response;
    }

    public function sendPayment($data)
    {
        $response  = $this->buildRequest('v2/SendPayment', 'POST', $data);
        return $response;
    }
    public function getPaymentStatus($data)
    {
        $response  = $this->buildRequest('v2/getPaymentStatus', 'POST', $data);
        return $response;
    }

    function callAPI($endpointURL, $apiKey, $postFields = [])
    {
        $curl = curl_init($endpointURL);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($postFields),
            CURLOPT_HTTPHEADER     => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));
        $response = curl_exec($curl);
        $curlErr  = curl_error($curl);
        curl_close($curl);
        return $response;
    }
    public function callback(Request $request)
    {

        $apiKey =  config('services.myfatoorah.api_token');
        $postFields = [
            'Key'     => $request->paymentId,
            'KeyType' => 'paymentId'
        ];
        $response = $this->callAPI("https://apitest.myfatoorah.com/v2/getPaymentStatus", $apiKey, $postFields);
        $response = json_decode($response);
        // dd( $response);
        if (!isset($response->Data->InvoiceId))
            return response()->json(["error" => 'error', 'status' => false], 404);
        $InvoiceId =  $response->Data->InvoiceId;
        $payment =    OrderPayment::where('invoice_id',  $InvoiceId)->first();
        $booked = Booked_apartment::where('id', $payment->booked_id)->first();
        if ($response->IsSuccess == true) {
            if ($response->Data->InvoiceStatus == "Paid")
                if ($payment->price == $response->Data->InvoiceValue) {
                    try {
                        DB::beginTransaction();
                        $payment->invoice_status = "Paid";
                        $payment->is_success = 1;
                        $payment->Transaction_date = $response->Data->CreatedDate;
                        $payment->save();

                        $booked->paid = 1;
                        $booked->status = 'recent';
                        $booked->save();

                        /////
                        $user =  $booked->user;
                        // send notification to admins
                        $admins = User::all();
                        Notification::send($admins, new BookedUser($user, $booked->apartment));
                        // send notification to user
                        $notificationData = $this->controlNotification('booking');
                        Notification::send($user, new BookingUser($notificationData['message']));
                        //notification to login user
                        $notificationData = $this->controlNotification('entry_day');
                        $notificationDate = Carbon::parse($booked->date_from);
                        $user->notify((new UserLogin($notificationData['message'], $notificationData['time'], $booked))->delay($notificationDate));
                        //notification to logout user
                        $notificationData = $this->controlNotification('exit_day');
                        $notificationDate = Carbon::parse($booked->date_to);
                        $user->notify((new UserLogout($notificationData['message'], $notificationData['time']))->delay($notificationDate));
                        ///broadcast event booked user
                        BookedUserEvent::dispatch($user, $booked->apartment);
                        ////////insert to points
                        Point::where('user_id', $user->id)->delete();
                        Point::create([
                            'booked_id' => $booked->id,
                            'user_id' => $user->id,
                            'point' => $booked->total_price
                        ]);
                        DB::commit();
                        return response()->json(['isSuccess' => true, 'Data' => 'payment success'], 200);
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return response()->json(["error" => 'error', 'Data' => 'payment failed'], 404);
                    }
                }
        }


        return response()->json(["error" => 'error', 'Data' => 'payment faild'], 404);
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
            $time = 'Default time';
        }

        return ['message' => $message, 'time' => $time];
    }
}
