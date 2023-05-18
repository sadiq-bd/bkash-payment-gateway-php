<?php
/**
 * @name: BkashAPI
 * @type: API Handler
 * @namespace: SADIQ_SOFT
 * @author: Sadiq <sadiq.developer.bd@gmail.com>
 */

namespace SADIQ_SOFT;

class BkashAPI {

    private static $baseURL = '';
    private static $tokenURL = '';
    private static $createURL = '';
    private static $executeURL = '';
    private static $queryURL = '';
    private static $refreshTokenURL = '';
    private static $refundURL = '';
    private static $refundStatusURL = '';
    private static $searchURL = '';

    private static $callBackURL = '';

    private static $app_key = '';
    private static $app_secret = '';
    private static $username = '';
    private static $password = '';

    private $grantToken = '';
    private $fetchResponse = '';
    private $errorInfo = '';

    public function __construct() {

        # self::setApiBaseURL('https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/');   # SANDBOX
        # self::setApiBaseURL('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/');       # PRODUCTION
        
        self::setTokenGrantURL('/checkout/token/grant');
        self::setRefreshTokenURL('/checkout/token/refresh');
        self::setCreatePaymentURL('/checkout/create');
        self::setExecutePaymentURL('/checkout/execute');
        self::setQueryPaymentURL('/checkout/payment/status');
        self::setRefundURL('/checkout/payment/refund');
        self::setRefundStatusURL('/checkout/payment/refund');
        self::setSearchTransactionURL('/checkout/general/searchTransaction');

    }

    public static function setApiBaseURL(string $value) {
        self::$baseURL = $value;
    }
       

    public static function setTokenGrantURL(string $value) {
        self::$tokenURL = self::makeApiURL($value);
    }
    

    public static function setCreatePaymentURL(string $value) {
        self::$createURL = self::makeApiURL($value);
    }
    

    public static function setExecutePaymentURL(string $value) {
        self::$executeURL = self::makeApiURL($value);
    }
 
    public static function setQueryPaymentURL(string $value) {
        self::$queryURL = self::makeApiURL($value);
    }
     
    public static function setRefreshTokenURL(string $value) {
        self::$refreshTokenURL = self::makeApiURL($value);
    }
    
  
    public static function setRefundURL(string $value) {
        self::$refundURL = self::makeApiURL($value);
    }
    
  
    public static function setRefundStatusURL(string $value) {
        self::$refundStatusURL = self::makeApiURL($value);
    }
    
  
    public static function setSearchTransactionURL(string $value) {
        self::$searchURL = self::makeApiURL($value);
    }

    private static function makeApiURL(string $path) {
        return rtrim(self::$baseURL, '/') . '/' . ltrim($path, '/');
    }
 
 
    public static function setCallBackUrl(string $value) {
        self::$callBackURL = $value;
    }


    public static function setAppKey(string $value) {
        self::$app_key = $value;
    }

    public static function setAppSecret(string $value) {
        self::$app_secret = $value;
    }


    public static function setUsername(string $value) {
        self::$username = $value;
    }


    public static function setPassword(string $value) {
        self::$password = $value;
    }

    public function setGrantToken(string $token) {
        $this->grantToken = $token;
        return $this;
    }

    public function getGrantToken() {
        return $this->grantToken;
    }

    public function grantToken() {
        $token = $this->fetch(
            self::$tokenURL, 
            array(
                'username: ' . self::$username,
                'password: ' . self::$password
            ), array(
                'app_key' => self::$app_key,
                'app_secret' => self::$app_secret
            )
        );
        if (!empty($token->jsonObj()->id_token)) {
            $this->setGrantToken($token->jsonObj()->id_token);
        }
        return $this;
    }

    
    public function refreshToken(string $refrshTokenValue) {
        $token = $this->fetch(
            self::$refreshTokenURL, 
            array(
                'username: ' . self::$username,
                'password: ' . self::$password
            ), array(
                'app_key' => self::$app_key,
                'app_secret' => self::$app_secret,
                'refresh_token' => $refrshTokenValue
            )
        );
        if (!empty($token->jsonObj()->id_token)) {
            $this->setGrantToken($token->jsonObj()->id_token);
        }
        return $this;
    }


    public function createPayment(string|int $amount, string $invoice, string $ref, string $intent = 'sale') {
        return $this->fetch(
            self::$createURL, 
            $this->createAuthHeaders(), 
            array(
                'mode' => '0011',
                'payerReference' => $ref,
                'callbackURL' => self::$callBackURL,
                'amount' => $amount, 
                'currency' => 'BDT', 
                'merchantInvoiceNumber' => $invoice,
                'intent' => $intent
            )
        );
    }

    public function executePayment(string $paymentID) {
        return $this->fetch(
            self::$executeURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID
            )
        );
    }

    
    public function queryPayment(string $paymentID) {
        return $this->fetch(
            self::$queryURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID
            )
        );
    }


    public function refundPayment(string $paymentID, string|int $amount, string $trxID, string $sku, string $reason) {
        return $this->fetch(
            self::$refundURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID,
                'amount' => $amount,
                'trxID' => $trxID,
                'sku' => $sku,
                'reason' => $reason
            )
        );
    }

    public function refundStatus(string $paymentID, string $trxID) {
        return $this->fetch(
            self::$refundStatusURL, 
            $this->createAuthHeaders(), 
            array(
                'paymentID' => $paymentID,
                'trxID' => $trxID
            )
        );
    }

    public function searchTransaction(string $trxID) {
        return $this->fetch(
            self::$searchURL, 
            $this->createAuthHeaders(), 
            array(
                'trxID' => $trxID
            )
        );
    }

    public function response() {
        return $this->fetchResponse;
    }

    public function json() {
        return @json_decode($this->response(), true);
    }

    public function jsonObj() {
        return @json_decode($this->response(), null);
    }

    private function createAuthHeaders() {
        return array(
            'authorization: '. $this->grantToken,
            'x-app-key: '. self::$app_key    
        );
    }

    private function fetch(string $url, array $headers = [], array $postData = []) {
        
        $postHeaders = array_merge(array(
            'Content-Type: application/json'                                                       
        ), $headers);
        
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $postHeaders);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        if (!empty($postData)) curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if (strlen($err) > 1) {
            $this->errorInfo = $err;
        }
        $this->fetchResponse = $result;

        return $this;
    }

    public function getErrorInfo() {
        return $this->errorInfo;
    }


}

