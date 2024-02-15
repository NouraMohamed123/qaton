<?php

namespace App\Http\Controllers\AppUser;

use Carbon\Carbon;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Models\Booked_apartment;
use App\Services\FatoorahServices;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookedResource;
use App\Models\OrderPayment;

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
        $booked =  Booked_apartment::with('Apartment')->get();
        return BookedResource::collection($booked);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $apartment_list = explode(',', $request->apartment_list);

        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);
        $payment_method = $request->payment_method;
        $totalDays = $checkInDate->diffInDays($checkOutDate);

        if ($checkInDate == $checkOutDate) {
            return response()->json(['error' => 'Check in and Check out date should not be same'], 403);
        }
        $apartments = Apartment::where('status', 1)->whereIn('id', $apartment_list)->with(['BookedApartments' => function ($BookedApartments) use ($checkInDate, $checkOutDate) {
            $BookedApartments->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->where(function ($qq) use ($checkInDate, $checkOutDate) {
                    $qq->where('date_from', '<=', $checkInDate)->where('date_to', '>', $checkInDate);
                })->orWhere(function ($qqq) use ($checkInDate, $checkOutDate) {
                    $qqq->where('date_from', '<=', $checkOutDate)->where('date_to', '>=', $checkOutDate);
                });
            });
        }])->get();
        foreach ($apartments as $apartment) {
            if ($apartment->BookedApartments->count() > 0) {
                return response()->json(['error' => 'Some apartment has already booked'], 403);
            }
        }
        if ($apartments->count() <= 0) {
            return response()->json(['error' => 'There is no apartment found'], 403);
        }

        $totalPrice = $apartments->sum('price');
        $totalPrice = $totalPrice * $totalDays;
        foreach ($apartments as $apartment) {
            $booked =  Booked_apartment::create([
                'user_id' => Auth::guard('app_users')->user()->id,
                'apartment_id' => $apartment->id,
                'total_price' => $totalPrice,
                'date_from' => $checkInDate,
                'date_to' => $checkOutDate
            ]);
        }
        if ($payment_method == 'fatoorah') {
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
            $response = $this->fatoorah_services->sendPayment($data);
            if (isset($response['IsSuccess']))
                if ($response['IsSuccess'] == true) {

                    $InvoiceId  = $response['Data']['InvoiceId'];
                    $InvoiceURL = $response['Data']['InvoiceURL'];
                    OrderPayment::create([
                        'customer_name'=> Auth::guard('app_users')->user()->name,
                        'invoice_id'=>$InvoiceId,
                        'invoice_url'=>$InvoiceURL,
                        'booked_id'=> $booked->id,
                        'price'=>$totalPrice
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
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Booked_apartment $booked)
    // {
    //     $apartment_list = explode(',', $request->apartment_list);
    //     $checkInDate = Carbon::parse($request->check_in_date);
    //     $checkOutDate = Carbon::parse($request->check_out_date);

    //     $totalDays = $checkInDate->diffInDays($checkOutDate);

    //     if ($checkInDate == $checkOutDate) {
    //         return response()->json(['error' => 'Check in and Check out date should not be same'], 403);
    //     }
    //     $apartments = Apartment::where('status', 1)->whereIn('id', $apartment_list)->with(['BookedApartments' => function ($BookedApartments) use ($checkInDate, $checkOutDate) {
    //         $BookedApartments->where(function ($q) use ($checkInDate, $checkOutDate) {
    //             $q->where(function ($qq) use ($checkInDate, $checkOutDate) {
    //                 $qq->where('date_from', '<=', $checkInDate)->where('date_to', '>', $checkInDate);
    //             })->orWhere(function ($qqq) use ($checkInDate, $checkOutDate) {
    //                 $qqq->where('date_from', '<=', $checkOutDate)->where('date_to', '>=', $checkOutDate);
    //             });
    //         });
    //     }])->get();
    //     foreach ($apartments as $apartment) {
    //         if ($apartment->BookedApartments->count() > 0) {
    //             return response()->json(['error' => 'Some apartment has already booked'], 403);
    //         }
    //     }
    //     if ($apartments->count() <= 0) {
    //         return response()->json(['error' => 'There is no apartment found'], 403);
    //     }
    //     $totalPrice = $apartments->sum('price');
    //     $totalPrice = $totalPrice * $totalDays;
    //     foreach ($apartments as $apartment) {

    //         $booked->update([
    //             'user_id' => Auth::guard('app_users')->user()->id,
    //             'apartment_id' => $apartment->id,
    //             'total_price' => $totalPrice,
    //             'date_from' => $checkInDate,
    //             'date_to' => $checkOutDate
    //         ]);
    //     }
    //     return response()->json(['isSuccess' => true, 'data' => new BookedResource($booked)], 200);
    // }

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

        $user= Auth::guard('app_users')->user();
        $BookedApartments =   Booked_apartment::where('user_id',$user->id)->where('status', $request->status)->get();
        return BookedResource::collection($BookedApartments);
    }



    public function callback(Request $request)
    {
 dd($request);
        $apiKey = 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL';
        $postFields = [
            'Key'     => $request->paymentId,
            'KeyType' => 'paymentId'
        ];
        $response = $this->fatoorah_services->callAPI("https://apitest.myfatoorah.com/v2/getPaymentStatus", $apiKey, $postFields);
        $response = json_decode($response);
        if (!isset($response->Data->InvoiceId))
            return response()->json(["error" => 'error', 'status' => false], 404);
        $InvoiceId =  $response->Data->InvoiceId;
        $payment =    OrderPayment::where('invoice_id',  $InvoiceId)->first();
        if ($response->IsSuccess == true) {
            if ($response->Data->InvoiceStatus == "Paid")
                if ( $payment->price == $response->Data->InvoiceValue) {
                    $payment->invoice_status = "Paid";
                    $payment->is_success = true;
                    $payment->Transaction_date = $response->Data->createDate;
                    $payment->save();
                    return response()->json(['isSuccess' => true,'Data'=>'payment success'], 200);
                }
        }

        return response()->json(["error" => 'error', 'Data'=>'payment faild'], 404);
    }

    public function error(Request $request)
    {
        dd($request);
    }
}
