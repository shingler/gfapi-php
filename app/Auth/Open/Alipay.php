<?php
namespace App\Auth\Open;

require_once env("APP_ROOT").'/vendor/alipay-sdk-php/aop/AopClient.php';
require_once env("APP_ROOT").'/vendor/alipay-sdk-php/aop/AopCertification.php';
require_once env("APP_ROOT").'/vendor/alipay-sdk-php/aop/request/AlipaySystemOauthTokenRequest.php';

class Alipay implements Base
{
    private $aop;

    public function __construct() {
        $this->aop = new \AopClient ();
        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $this->aop->appId = env("CONNECT_ALIPAY_APP_ID");
        $this->aop->rsaPrivateKey = env("CONNECT_ALIPAY_RSA_PRIVATE_KEY");
        $this->aop->alipayrsaPublicKey = env("CONNECT_ALIPAY_RSA_PUBLIC_KEY");
        $this->aop->apiVersion = '1.0';
        $this->aop->signType = 'RSA2';
        $this->aop->postCharset='UTF-8';
        $this->aop->format='json';
    }

    public function getAccessToken(string $auth_code) {
        $request = new \AlipaySystemOauthTokenRequest ();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
//        $request->setRefreshToken("201208134b203fe6c11548bcabd8da5bb087a83b");
        $result = $this->aop->execute ($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
//        dd($responseNode,$result,$result->$responseNode);
        return $result->$responseNode;
    }

    public function getUserData(string $access_token) {
        $request = new AlipayUserInfoShareRequest();
        $result = $this->aop->execute($request, $access_token);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            return $result->$responseNode;
        } else {
            return None;
        }
    }
}
