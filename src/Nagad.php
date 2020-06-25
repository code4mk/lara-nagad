<?php

namespace Code4mk\Nagad;

use GuzzleHttp\Client;
use Code4mk\Nagad\Utility;

class Nagad{

    private $amount;
    private $tnx;

    private $nagadHost;
    private $tnx_status = false;

    private $merchantAdditionalInfo = [];

    public function __construct()
    {
        date_default_timezone_set('Asia/Dhaka');
        if (config('nagad.sandbox_mode') === 'sandbox') {
            $this->nagadHost = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs";
        }else{
            $this->nagadHost = "https://api.mynagad.com/api/dfs";
        }

    }

    public function tnx($id,$status=false)
    {
        $this->tnx = $id;
        $this->tnx_status = $status;
        return $this;
    }

    public function amount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getSession()
    {

        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        //$invoice_no = 'Inv'.Date('YmdH').rand(1000, 10000);
        $invoice_no = $this->tnx_status ? $this->tnx :'Inv'.Date('YmdH').rand(1000, 10000);
        $merchantCallbackURL = config('nagad.callback_url');

        $SensitiveData = [
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoice_no,
            'challenge' => Utility::generateRandomString()
        ];

        $PostData = array(
            'accountNumber' => config('nagad.merchant_number'), //optional
            'dateTime' => $DateTime,
            'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => Utility::SignatureGenerate(json_encode($SensitiveData))
        );

        $ur = $this->nagadHost."/check-out/initialize/" . $MerchantID . "/" . $invoice_no;

            $Result_Data = Utility::HttpPostMethod($ur,$PostData);
            //return $Result_Data;


        return $Result_Data;


        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode(Utility::DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomserver = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $invoice_no,
                        'currencyCode' => '050',
                        'amount' => $this->amount,
                        'challenge' => $randomserver
                    );


                    // $merchantAdditionalInfo = '{"no_of_seat": "1", "Service_Charge":"20"}';
                    if($this->tnx !== ''){
                        $this->merchantAdditionalInfo['tnx_id'] =  $this->tnx;
                    }
                    // echo $merchantAdditionalInfo;
                    // exit();

                    $PostDataOrder = array(
                        'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' => Utility::SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => (object)$this->merchantAdditionalInfo
                    );

                    // echo json_encode($PostDataOrder);
                    // exit();

                    $OrderSubmitUrl = $this->nagadHost."/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order = Utility::HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
                        if ($Result_Data_Order['status'] == "Success") {
                            $url = ($Result_Data_Order['callBackUrl']);
                            return response()->json($url);
                            echo "<script>window.open($url, '_self')</script>";
                        }
                        else {
                            echo json_encode($Result_Data_Order);
                        }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }

    }

    public function verify(){
        $Query_String = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
        $payment_ref_id = substr($Query_String[2], 15);
        $url = $this->nagadHost."/verify/payment/" . $payment_ref_id;
        $json = Utility::HttpGet($url);
        $arr = json_decode($json, true);
        return $arr;
    }
}
