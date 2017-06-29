<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of upload
 *
 * @author Administrator
 */
namespace Common\Lib\AppPay\alipay;

require_once __DIR__ . '/AopSdk.php';//自动加载类库
class Alipay {
    
    private $config;

    //put your code here
    public function __construct($config) {
       $this->config=$config;
    }
    
    //验证登录
    public function cheack($params){
        $payConfig=C('app_pay_config');
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = $payConfig['alipay']['public_key'];
        $flag = $aop->rsaCheckV1($params, NULL, "RSA");
        if($flag){
            return $params;
        }
        
        return false;
    }

}
