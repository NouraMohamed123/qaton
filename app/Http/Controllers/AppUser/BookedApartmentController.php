<?php

namespace App\Http\Controllers\AppUser;

use PDF;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Point;
use App\Models\price;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Apartment;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Events\UserLoginEvent;
use App\Services\TabbyPayment;
use App\Events\BookedUserEvent;
use App\Events\UserLogoutEvent;
use App\Events\BookingUserEvent;
use App\Models\Booked_apartment;
use App\Notifications\UserLogin;
use App\Notifications\BookedUser;
use App\Notifications\UserLogout;
use PhpParser\Node\Stmt\TryCatch;
use App\Notifications\BookingUser;
use App\Services\FatoorahServices;
use Illuminate\Support\Facades\DB;
use App\Events\BookingToAdminEvent;
use App\Events\LeavingToAdminEvent;
use App\Models\ControlNotification;
use App\Traits\NotificationControl;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingToAdmin;
use App\Notifications\LeavingToAdmin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Http\Resources\BookedResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookedResourceMobile;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\ApartmentResourceAccess;

class BookedApartmentController extends Controller
{
    use NotificationControl;
    private $fatoorah_services;
    public $tabby;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->fatoorah_services = new FatoorahServices();
        $this->tabby = new TabbyPayment();
    }
    public function index()
    {
        $booked =  Booked_apartment::with('Apartment', 'user')->get();
        return BookedResourceMobile::collection($booked);
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
        $user = Auth::guard('app_users')->user();
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
                })->where(function ($w) {
                    $w->where('paid', 1)
              ->where('status', '!=', 'canceled');
                });
            }])
            ->first();
        if (!$apartment) {
            return response()->json(['error' => 'Apartment not found'], 403);
        }

        if ($apartment->BookedApartments->count() > 0) {
            return response()->json(['error' => 'The apartment has already been booked'], 403);
        }
        ///////////////logic price
        // $price_day =  price::where('apartment_id', $apartmentId)->where('date', $checkInDate)->value('price');
        // $settings = Setting::pluck('value', 'key')->toArray();
        // $taxAddedValue = $settings['tax_added_value'];
        // $price_with_tax = $taxAddedValue ?
        //     (($price_day ?? $apartment->price) + $taxAddedValue) : ($price_day ?? $apartment->price);
        $price_day = Price::where('apartment_id', $apartmentId)->where('date', $checkInDate)->value('price');
        $settings = Setting::pluck('value', 'key')->toArray();
        $taxAddedValue = isset($settings['tax_added_value']) ? $settings['tax_added_value'] : 0;
        $base_price = $price_day ?? $apartment->price;

        // Calculate price with tax if tax is added
        if ($taxAddedValue) {
            $price_with_tax = $base_price + ($base_price * $taxAddedValue / 100);
        } else {
            $price_with_tax = $base_price;
        }
        // Output or return $price_with_tax as needed
        if ($request->has('coupon_code') && !empty($request->coupon_code)) {
            $coupon_data = checkCoupon($request->coupon_code, $price_with_tax);
            if ($coupon_data && $coupon_data['status'] == true) {
                $discount = $coupon_data['discount'];
                $price_with_tax -= $discount;
            } else {
                return response()->json(['status' => false, 'message' => $coupon_data['message']], 310);
            }
        }
        if ($request->points == true) {
            if ($riyals = calculateRiyalsFromPoints($user->id) > 0) {

                $price_with_tax -= $riyals;
            }
        }
        if ($settings['available_discount'] == '1') {

            if (apply_discount($totalDays) > 0) {
                $discountPercentage = apply_discount($totalDays) / 100;
                $discountedPrice = $price_with_tax * $discountPercentage;
                $totalPrice = ($price_with_tax - $discountedPrice) * $totalDays;
            }
        }
            $totalPrice = $price_with_tax * $totalDays;
        /////////////////////
        $booked =  Booked_apartment::create([
            'user_id' => $user->id,
            'apartment_id' => $apartment->id,
            'total_price' => $totalPrice,
            'date_from' => $checkInDate,
            'date_to' => $checkOutDate,
            'coupon_id' => $coupon_data['id'] ?? 0,
            'tax'=>$taxAddedValue??0,
            'price_notax'=> $base_price* $totalDays,
            'price_day'=>$price_day??0,

        ]);


        $settings = Setting::pluck('value', 'key')
            ->toArray();
        if ($settings['available_bookings'] == '0') {
            $booked->status =  'pending';
            $booked->save();
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
                        'name' => 'fatoorah',
                        'customer_name' => Auth::guard('app_users')->user()->name,
                        'invoice_id' => $InvoiceId,
                        'invoice_url' => $InvoiceURL,
                        'booked_id' => $booked->id,
                        'price' => $totalPrice
                    ]);
                }
            return $response['Data']['InvoiceURL'];
        } elseif ($paymentMethod && $paymentMethod == 'Tabby') {
            $items = collect([]);
            $items->push([
                'title' => 'title',
                'quantity' => 1,
                'unit_price' => 20,
                'category' => 'Clothes',
            ]);

            $order_data = [
                'amount' => $totalPrice,
                'currency' => 'SAR',
                'description' => 'description',
                'full_name' => Auth::guard('app_users')->user()->name ?? 'user_name',
                'buyer_phone' =>   Auth::guard('app_users')->user()->phone ?? '9665252123',
                //  'buyer_email' => 'card.success@tabby.ai',//this test
                'buyer_email' =>    Auth::guard('app_users')->user()->email ?? 'card.success@tabby.ai',
                'address' => 'Saudi Riyadh',
                'city' => 'Riyadh',
                'zip' => '1234',
                'order_id' => "$booked->id",
                'registered_since' =>  $booked->created_at,
                'loyalty_level' => 0,
                'success-url' => route('success-ur'),
                'cancel-url' => route('cancel-ur'),
                'failure-url' => route('failure-ur'),
                'items' =>  $items,
            ];

            $payment = $this->tabby->createSession($order_data);



            if (isset($payment->configuration->available_products->installments[0]->web_url)) {
                $redirect_url = $payment->configuration->available_products->installments[0]->web_url;
                return $redirect_url;
            } else {
                // Handle the case where installments or web_url is not available
                return redirect()->back()->with('error', 'Unable to process payment with Tabby.');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booked_apartment $booked)
    {
        return  new BookedResourceMobile($booked);
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

    public function checkCoupon(Request $request)
    {
        return checkCoupon($request->couponCode, $request->totalAmount);
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
                        //////
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

        return response()->json(["error" => 'error', 'Data' => 'payment faild'], 404);
    }

    public function error(Request $request)
    {
        return response()->json(["error" => 'error', 'Data' => 'payment faild'], 404);
    }
    public function sucess(Request $request)
    {
        return   $this->tabby->calbackPayment($request);
    }
    public function cancel(Request $request)
    {
        return response()->json(["error" => 'error', 'Data' => 'payment canceld'], 404);
    }
    public function failure(Request $request)
    {
        return response()->json(["error" => 'error', 'Data' => 'payment failure'], 404);
    }

    public function canceld(Request $request)
    {
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);
            $booked = Booked_apartment::where('apartment_id', '=', $request->apartment_id)->where('date_from',$checkInDate)->where('date_to',$checkOutDate)->first();
            if ($booked) {
            $booked->status = 'canceled';
            $booked->save();
            return response()->json(['isSuccess' => true,'message'=> 'successfuly' ], 200);
            }

        return response()->json(['error' => 'There is no booked found for this apartment'], 403);
    }

    public function userBooked(Request $request)
    {

        $user = Auth::guard('app_users')->user();
        $BookedApartments = Booked_apartment::where('user_id', $user->id)
        ->whereIn('status', ['recent', 'past', 'canceled', 'pending'])
        ->whereIn('id', function ($query) use ($user) {
            $query->selectRaw('MAX(id)')
                ->from('booked_apartments')
                ->where('user_id', $user->id)
                ->whereIn('status', ['recent', 'past', 'canceled', 'pending'])
                ->groupBy('apartment_id', 'status');
        })
        ->latest()
        ->get();
    return BookedResourceMobile::collection($BookedApartments);
    }
    public function userBookedDetailsAccess($id){

        $booked =   Apartment::where('id', $id)->first();
        return  new ApartmentResourceAccess($booked);



    }
    public function userLeaving(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:apartments,id',
            'exit' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        $user = Auth::guard('app_users')->user();
        $booked =   Booked_apartment::where('apartment_id', $request->id)->where('user_id', $user->id)->first();

        if($booked){
        $booked->update([
            'exit' =>$request->exit,
        ]);
        $admins = User::all();
        if($booked->exit == 1){
            Notification::send($admins, new LeavingToAdmin($user, $booked));
            LeavingToAdminEvent::dispatch($user, $booked->apartment);
            return response()->json(['isSuccess' => true,'message'=> 'send successfuly' ], 200);

        }
    }
    }
    public function generate_pdf($id)
{
    // Fetch the booked apartment along with related data
    $booked = Booked_apartment::with('user', 'apartment', 'coupon', 'order_payment')
        ->where('id', $id)
        ->where('status', 'recent')
        ->first();

        if (!$booked) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        // Debugging: Log the fetched data
        \Log::info('Booked Apartment Data:', $booked->toArray());

        // Generate a unique filename
        $dateTime = now();
        $fileName = $dateTime->format('YmdHis') . '_translation.pdf';

        // Generate PDF using mPDF
        try {
            // Configure mPDF
            $mpdf = new Mpdf([
                'default_font' => 'Arial',
                'format' => 'A4',
                'font_size' => '8px',

            ]);

            // Load the view and render HTML to a string
            $html = view('invoices', ['booked' => $booked])->render();

            // Write HTML to the PDF
            $mpdf->WriteHTML($html);

            // Save the PDF to storage
            $filePath = storage_path('app/public/' . $fileName);
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'PDF generation failed.'], 500);
        }

        // Update invoice link in the database
        $orderPayment = OrderPayment::where('booked_id', $booked->id)->first();
        if ($orderPayment) {
            $orderPayment->invoice_link = $fileName;
            $orderPayment->save();
        } else {
            return response()->json(['success' => false, 'message' => 'Order payment not found.'], 404);
        }

        // Get the file URL for download
        $urlToDownload = asset('storage/' . $fileName);

        // Return the response with the download URL
        return response()->json([
            'success' => true,
            'url' => $urlToDownload,
        ]);

}



}
