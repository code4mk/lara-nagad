<?php

namespace Code4mk\Nagad;

use Code4mk\Nagad\Utility;

/**
 * Nagad class
 * @author code4mk <hiremostafa@gmail.com>
 * @version 1.0.0
 */

class Nagad{

    private $tnxID;
    private $nagadHost;
    private $tnxStatus = false;
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

    /**
     * Trasaction ID.
     * @param int $id.
     * @param bool $status.
     * @author code4mk <hiremostafa@gmail.com>
     * @since v1.0.0
     * @version 1.0.0
     */

    public function tnxID($id,$status=false)
    {
        $this->tnxID = $id;
        $this->tnxStatus = $status;
        return $this;
    }

    /**
     * Amount.
     * @param int $amount.
     * @author code4mk <hiremostafa@gmail.com>
     * @since v1.0.0
     * @version 1.0.0
     */
    public function amount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get redirect url <callback url>
     * @author code4mk <hiremostafa@gmail.com>
     * @since v1.0.0
     * @version 1.0.0
     */
    public function getRedirectUrl()
    {
        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        $invoiceNo = $this->tnxStatus ? $this->tnxID : 'Inv'.Date('YmdH').rand(1000, 10000);
        $merchantCallbackURL = config('nagad.callback_url');

        $SensitiveData = [
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoiceNo,
            'challenge' => Utility::generateRandomString()
        ];

        $PostData = array(
            'accountNumber' => config('nagad.merchant_number'),
            'dateTime' => $DateTime,
            'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => Utility::SignatureGenerate(json_encode($SensitiveData))
        );

        $initializeUrl = $this->nagadHost."/check-out/initialize/" . $MerchantID . "/" . $invoiceNo;

        $Result_Data = Utility::HttpPostMethod($initializeUrl,$PostData);

        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode(Utility::DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomserver = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $invoiceNo,
                        'currencyCode' => '050',
                        'amount' => $this->amount,
                        'challenge' => $randomserver
                    );


                    // $merchantAdditionalInfo = '{"no_of_seat": "1", "Service_Charge":"20"}';
                    if($this->tnxID !== ''){
                        $this->merchantAdditionalInfo['tnx_id'] =  $this->tnxID;
                    }

                    $PostDataOrder = array(
                        'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' => Utility::SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => (object)$this->merchantAdditionalInfo
                    );
                    // order submit
                    $OrderSubmitUrl = $this->nagadHost."/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order = Utility::HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
                        if ($Result_Data_Order['status'] == "Success") {
                            $callBackUrl = ($Result_Data_Order['callBackUrl']);
                            return $callBackUrl;
                            //echo "<script>window.open($url, '_self')</script>";
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

    /**
     * Verify Payment
     * @author code4mk <hiremostafa@gmail.com>
     * @since v1.0.0
     * @version 1.0.0
     */

    public function verify(){
        $Query_String = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
        $payment_ref_id = substr($Query_String[2], 15);
        $url = $this->nagadHost."/verify/payment/" . $payment_ref_id;
        $json = Utility::HttpGet($url);
        $arr = json_decode($json, true);
        return $arr;
    }


    /**
     * Get support id for live project <callback url>
     * @author code4mk <hiremostafa@gmail.com>
     * @since v1.0.0
     * @version 1.0.0
     */
    public function getSupportID()
    {
        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        $invoiceNo = $this->tnxStatus ? $this->tnxID : 'Inv'.Date('YmdH').rand(1000, 10000);
        $merchantCallbackURL = config('nagad.callback_url');

        $SensitiveData = [
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoiceNo,
            'challenge' => Utility::generateRandomString()
        ];

        $PostData = array(
            'accountNumber' => config('nagad.merchant_number'),
            'dateTime' => $DateTime,
            'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => Utility::SignatureGenerate(json_encode($SensitiveData))
        );

        $initializeUrl = $this->nagadHost."/check-out/initialize/" . $MerchantID . "/" . $invoiceNo;

        $Result_Data = Utility::HttpPostMethodSupportID($initializeUrl,$PostData);

        return $Result_Data;

    }
}
