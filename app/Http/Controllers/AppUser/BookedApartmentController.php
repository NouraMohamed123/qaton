<?php

namespace App\Http\Controllers\AppUser;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\Apartment;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Events\BookedUserEvent;
use App\Models\Booked_apartment;
use App\Notifications\UserLogin;
use App\Notifications\BookedUser;
use App\Notifications\UserLogout;
use PhpParser\Node\Stmt\TryCatch;
use App\Services\FatoorahServices;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookedResource;
use App\Models\price;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class BookedApartmentController extends Controller
{
    private $fatoorah_services;
    /**
     * Display a listing of the resource.
     */
    public function __construct(FatoorahServices $fatoorahServices)
    {
        $this->fatoorah_services = $fatoorahServices;
    }
    public function index()
    {
        $booked =  Booked_apartment::with('Apartment', 'user')->get();
        return BookedResource::collection($booked);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_list' => 'required',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $apartmentId = $request->apartment_list;

        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);
        $paymentMethod = $request->payment_method;
        $totalDays = $checkInDate->diffInDays($checkOutDate);

        if ($checkInDate == $checkOutDate) {
            return response()->json(['error' => 'Check-in and Check-out date should not be the same'], 403);
        }

        $apartment = Apartment::where('status', 1)
        ->where('id', $apartmentId)
        ->with(['BookedApartments' => function ($BookedApartments) use ($checkInDate, $checkOutDate) {
            $BookedApartments->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->where(function ($qq) use ($checkInDate, $checkOutDate) {
                    $qq->where('date_from', '<=', $checkInDate)->where('date_to', '>', $checkInDate);
                })->orWhere(function ($qqq) use ($checkInDate, $checkOutDate) {
                    $qqq->where('date_from', '<=', $checkOutDate)->where('date_to', '>=', $checkOutDate);
                });
            })->where('paid', 1);
        }])
        ->first();
        if (!$apartment) {
            return response()->json(['error' => 'Apartment not found'], 403);
        }

        if ($apartment->BookedApartments->count() > 0) {
            return response()->json(['error' => 'The apartment has already been booked'], 403);
        }
       ///////////////logic price
        $price_day=  price::where('apartment_id', $apartmentId)->where('date', $checkInDate)->value('price');
        $settings = Setting::pluck('value', 'key')->toArray();
        $taxAddedValue = $settings['tax_added_value'];
        $price_with_tax = $taxAddedValue ?
        (($price_day ?? $apartment->price) + $taxAddedValue) :
        ($price_day ?? $apartment->price);
        if(apply_discount($totalDays) > 0){

            $discountPercentage = apply_discount($totalDays) / 100;
            $discountedPrice = $price_with_tax * $discountPercentage;
            $totalPrice = ($price_with_tax - $discountedPrice) * $totalDays;
        }else{
            $totalPrice = $price_with_tax * $totalDays;
        }
        /////////////////////
        $user = Auth::guard('app_users')->user();
        $booked =  Booked_apartment::create([
            'user_id' => $user->id,
            'apartment_id' => $apartment->id,
            'total_price' => $totalPrice,
            'date_from' => $checkInDate,
            'date_to' => $checkOutDate
        ]);
        ///
        if ($paymentMethod == 'cash') {
            $booked->paid = 1;
            $booked->status = 'recent';
            $booked->save();
            // send notification to admin
            $admins = User::all();
            Notification::send($admins, new BookedUser($user, $booked->apartment));
            // send notification to user
            Notification::send($user, new BookedUser($user, $booked->apartment));
            //notification to login user
            $notificationDate = Carbon::parse($booked->date_from);
            $user->notify((new UserLogin($user, $booked))->delay($notificationDate));

            //notification to logout user
            $notificationDate = Carbon::parse($booked->date_to);
            $user->notify((new UserLogout($user, $booked))->delay($notificationDate));
            ///broadcast event booked user
            BookedUserEvent::dispatch($user, $booked->apartment);

            return response()->json(['isSuccess' => true, 'Data' => 'payment success'], 200);
        }
        $settings = Setting::pluck('value', 'key')
        ->toArray();
        if(!$settings['available_bookings'] == 1){
            $booked::update([
                'status'=>'pending'
            ]);
            return response()->json(['messsage' => 'طلب حجز'], 200);

        }
        if ($paymentMethod && $paymentMethod == 'fatoorah') {
            $data = [
                "CustomerName" => Auth::guard('app_users')->user()->name,
                "Notificationoption" => "LNK",
                "Invoicevalue" => $totalPrice, // total_price
                "CustomerEmail" => Auth::guard('app_users')->user()->email,
                "CalLBackUrl" => route('callback'),
                "Errorurl" => route('error'),
                "Languagn" => 'en',
                "DisplayCurrencyIna" => 'SAR'
            ];
            // dd( $data);
            $response = $this->fatoorah_services->sendPayment($data);
            if (isset($response['IsSuccess']))
                if ($response['IsSuccess'] == true) {

                    $InvoiceId  = $response['Data']['InvoiceId'];
                    $InvoiceURL = $response['Data']['InvoiceURL'];
                    OrderPayment::create([
                        'customer_name' => Auth::guard('app_users')->user()->name,
                        'invoice_id' => $InvoiceId,
                        'invoice_url' => $InvoiceURL,
                        'booked_id' => $booked->id,
                        'price' => $totalPrice
                    ]);
                }
            return $response['Data']['InvoiceURL'];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booked_apartment $booked)
    {
        return  new BookedResource($booked);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booked_apartment $booked)
    {
        $booked->delete();
        return response()->json(['isSuccess' => true], 200);
    }
    public function canceld(Booked_apartment $booked)
    {
        if ($booked) {
            $booked->update([
                'status' => 'canceled',
            ]);
        } else {
            return response()->json(['isSuccess' => false], 200);
        }

        return response()->json(['error' => 'There is no booked found'], 403);
    }
    public function userBooked(Request $request)
    {

        $user = Auth::guard('app_users')->user();
        $BookedApartments =   Booked_apartment::where('user_id', $user->id)->where('status', $request->status)->get();
        return BookedResource::collection($BookedApartments);
    }



    public function callback(Request $request)
    {

        $apiKey =  config('services.myfatoorah.api_token');
        $postFields = [
            'Key'     => $request->paymentId,
            'KeyType' => 'paymentId'
        ];
        $response = $this->fatoorah_services->callAPI("https://apitest.myfatoorah.com/v2/getPaymentStatus", $apiKey, $postFields);
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
                        $user = Auth::guard('app_users')->user();
                        // send notification to admins
                        $admins = User::all();
                        Notification::send($admins, new BookedUser($user, $booked->apartment));
                        // send notification to user
                        Notification::send($user, new BookedUser($user, $booked->apartment));
                        //notification to login user
                        $notificationDate = Carbon::parse($booked->date_from);
                        $user->notify((new UserLogin($user, $booked))->delay($notificationDate));

                        //notification to logout user
                        $notificationDate = Carbon::parse($booked->date_to);
                        $user->notify((new UserLogout($user, $booked))->delay($notificationDate));
                         ///broadcast event booked user
                        ///broadcast event booked user
                        BookedUserEvent::dispatch($user, $booked->apartment);

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

    public function error(Request $request)
    {
        return response()->json(["error" => 'error', 'Data' => 'payment faild'], 404);
    }
}
