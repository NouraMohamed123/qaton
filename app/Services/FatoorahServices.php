<?php

namespace App\Services;

use App\ModelTax;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloguent\Model;

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
        Config::set('services.myfatoorah.api_token',"BnS1HeFjAXsMPkWa5kb3slhzBiF41AE4HbV5y5DTezIRF-rJoe8N4naD8XTkiJ_sNnejH-iTf6mtRO2QarelZpwM1Qh51m_b8CjeQi5JhoXJPUNfiKjj0cVlSfAVejTcB_1SktROjN7CAUE0xflWN16ObmuNF0XmiVj32j4R-eqWWv0583Vc06Z6lJpVrC_pZK9Ck-K5nJgqrO3seUyW9TzcrSMD91khfbpfOY68xJQo-xjQr9C4CcpsUCtnkLo9V7rrpIm6pkFE__2sL-A58kltBuFCQCfJnmW9gOaGAe4Tet3bWD9XUItFnapTGelbzWTK9-2cJNb0fRqa7LGi-6q79_vWOIQTuiCBExmX-CZ0gph0pvTXa2DIsEQVAoYaTuVPi6uMxzwYpfRCUCRhE72Z1bBbpFYWbS2OQqSDl6udPGq2Nbk_wvlNxYAhbXOH-iptqm8mYxzIj7gF4YeDcWjYXab9kjp_2c6ZRD8m20Hsc6MV_r2U9UIH1hwlG0N3xPGEPvdZItXYbnCPf4QQPo_psz0mIc1NL2dG2fNwI39V34NgwuWACkCPcsNPPgZZx72oHOUiF-eNLDRFQoifwON9KIBpb70UmrnBi1TTwB38unBsTXlJMDZSup5u2pQTuJdbxfdYfYNGszKNdCbWoQh2eyCU39Nt5CBYJXJkH18F92h2RTm6WI9dp5GAillvXzIKpZTY57v0XdTsK2JvOrsP7KZB5KSQX3GCBR-BM7AqfZQD");
        Config::set('services.myfatoorah.base_url','https://apitest.myfatoorah.com/');
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
}
