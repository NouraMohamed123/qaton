<?php

namespace App\Services\services;

use Illuminate\Http\Request;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use App\Services\contracts\PaymentInterface;

class myFatoorahPayment implements PaymentInterface
{
    public function __construct()
    {

        $myfatoorah = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
        $myfatoorahConf = json_decode($myfatoorah->information, true);
         Config::set('services.myfatoorah.api_token', $myfatoorahConf["api_token"]);


    }
    public function paymentProcess(
        $request,
        $_amount,
        $return,
        $callback
    ){
        $myfatoorah =   Config::get('myfatoorah.api_token');
    }
    public function successPayment(Request $request)
    {



    }
    public function calbackPayment(Request $request)
    {




    }
}
