<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PaymentGeteway;
use App\Http\Controllers\Controller;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['myfatoorah'] = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
        if ($data['myfatoorah']) {
            $information = json_decode($data['myfatoorah']->information, true);

            $data['myfatoorah']->information = $information;
        }
        return response()->json([
            "isSuccess" => true,
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function MyfatoorahUpdate(Request $request)
    {
        $myfatoorah = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
      //  dd($myfatoorah);
        $myfatoorah->status = $request->status;
        $information = [];
        $information['api_token'] = $request->api_token;
        $myfatoorah->information = json_encode($information);

        $myfatoorah->save();

        return response()->json([
            "isSuccess" => true,
            'data' => $myfatoorah
        ], 200);
    }

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
    public function destroy(string $id)
    {
        //
    }
}
