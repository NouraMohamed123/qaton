<?php
namespace App\Services;
use App\ModelTax;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloguent\Model;

class FatoorahServices{
    private $base_url;
    private $headers;
    private $request_client;

    public function __construct(Client $request_client){
        $myfatoorah = PaymentGeteway::where([
            ['keyword', 'myfatoorah'],
        ])->first();
        $myfatoorahConf = json_decode($myfatoorah->information, true);
       $this->request_client =  Config::set('services.myfatoorah.api_token', $myfatoorahConf["api_token"]);
       $this->base_url = Config::set('services.myfatoorah.base_url','https://apitest.myfatoorah.com/');

          $this->headers= [
           "Content-Type" =>'application/json',
           "authorization" => 'Bearer '.env("fatoora_token","fatoora_token")
       ];
    }

    public function buildRequest($url,$mothod, $data =[]){
        $request = new Request($mothod , $this->base_url.$url, $this->headers);
        if (!$data)
            return false;
        $response = $this->request_client->send($request, ['json'=>$data]);
        if ($response->getStatusCode() != 200)
            return false;
        $response = json_decode ($response->getBody (),true);
        return $response;
    }

    public function sendPayment($data){
        $response  = $this->buildRequest('v2/SendPayment','POST', $data);
        return $response;
    }
    public function getPaymentStatus($data){
        $response  = $this->buildRequest('v2/getPaymentStatus','POST', $data);
        return $response;
    }

    function callAPI($endpointURL, $apiKey, $postFields = []) {
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

?>
