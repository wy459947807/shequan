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

namespace Common\Lib\AppPay\wechat;

class AppWechatPay {

    private $config;

    //put your code here
    public function __construct($config) {
        $this->config = $config;
    }

    //获取微信APP支付信息
    public function getWxPrepayid($params) {
        
        file_put_contents("postData2.txt", json_encode($params));
  
        $noceStr = md5(rand(100, 1000) . time()); //获取随机字符串  
        $time = time();
        $paramarr = array(
            "appid" => $this->config['APPID'],
            "body" => $params['body'], //说明内容  
            "mch_id" => $this->config['MCHID'],
            "nonce_str" => $noceStr,
            "notify_url" => $this->config['NOIFYURL'],
            "out_trade_no" => $params['orderNo'],
            "spbill_create_ip" => get_client_ip(0, true),
            "total_fee" =>intval($params['total_fee'] * 100),
            "trade_type" =>!empty($params['trade_type'])?$params['trade_type']:"APP",
        );
        
        if(!empty($params['scene_info'])){
            $paramarr['scene_info']=htmlspecialchars_decode($params['scene_info']);
        }

        $sign = $this->sign($paramarr); //生成签名  
        $paramarr['sign'] = $sign;
        $paramXml = "<xml>";
        foreach ($paramarr as $k => $v) {
            $paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";
        }
        $paramXml .= "</xml>";
        file_put_contents("test.txt", "");
        file_put_contents("test.txt", $paramXml);
        $ch = curl_init();
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查    
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在    
        @curl_setopt($ch, CURLOPT_URL, $this->config['WXURL']);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_POST, 1);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);
        @$resultXmlStr = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        file_put_contents("test2.txt", "");
        file_put_contents("test2.txt", $resultXmlStr);
        $result = $this->xmlToArray($resultXmlStr);
        $prepayid = $result['prepay_id'];
        $noceStr = md5(rand(100, 1000) . time()); //获取随机字符串  
        $paramarr2 = array(
            "appid" => $this->config['APPID'],
            "noncestr" => $noceStr,
            "package" => "Sign=WXPay",
            "partnerid" => $this->config['MCHID'],
            "prepayid" => $prepayid,
            "timestamp" => time()
        );
        $paramarr2["sign"] = $this->sign($paramarr2); //生成签名

        return $paramarr2;

        //echo json_encode($paramarr2);  
    }

    public function noify_cheack() {

        $xmlInfo = $GLOBALS['HTTP_RAW_POST_DATA'];

        $this->logecho($xmlInfo); //log打印保存  
        //解析xml  
        $arrayInfo = $this->xmlToArray($xmlInfo);
        //$this -> wxDate = $arrayInfo;  
        /* 测试数据打印log数组转字符串=============== */
        $test = "";
        foreach ($arrayInfo as $k => $v) {
            $test .= $k . ":" . $v . "\t\r\n";
        }
        /* ======================================== */
        //$this->logecho("数据打印测试:" . $test); //log打印保存  
        if ($arrayInfo['return_code'] == "SUCCESS") {
            if ($return_msg != null) {
                return array(
                    'status' => 500,
                    'msg' => "签名失败:" . $sign,
                    'data' => $this->returnInfo("FAIL", "签名失败")
                );
            } else {
                $wxSign = $arrayInfo['sign'];
                unset($arrayInfo['sign']);
                $arrayInfo['appid'] = $this->config['APPID'];
                $arrayInfo['mch_id'] = $this->config['MCHID'];
                ksort($arrayInfo); //按照字典排序参数数组  
                $sign = $this->sign($arrayInfo); //生成签名  
                //$this->logecho("数据打印测试签名signmy:" . $sign . "微信sign:" . $wxSign); //log打印保存  
                if ($this->checkSign($wxSign, $sign)) {
                    return array( 
                        'status' => 200,
                        'msg' => $this->returnInfo("SUCCESS", "OK"),
                        'data' => $arrayInfo
                    ); 
                } else {
                    return array( 
                        'status' => 500,
                        'msg' =>  $this->returnInfo("FAIL", "签名失败"),
                        'data' =>""
                    );
                }
            }
        } else { 
            return array( 
                'status' => 500,
                'msg' => $this->returnInfo("FAIL", "签名失败"),
                'data' =>""
            );
        }
    }

    public function returnInfo($type, $msg) {
        if ($type == "SUCCESS") {
            return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";
        } else {
            return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
        }
    }

    //打印log  
    private function logecho($msg) {
        $str = file_get_contents("logwx.txt");
        file_put_contents("logwx.txt", $str . "\t\r\n" . date("Y:m:d H:i:s") . "\t\r\n" . $msg);
    }

    //签名验证  
    private function checkSign($sign1, $sign2) {
        return trim($sign1) == trim($sign2);
    }

    /**
     * sign拼装获取 
     */
    private function sign($param) {

        $sign = "";
        foreach ($param as $k => $v) {
            $sign .= $k . "=" . $v . "&";
        }

        $sign .= "key=" . $this->config['KEY'];
        $sign = strtoupper(md5($sign));
        return $sign;
    }

    private function signSha($param) {

        $sign = "";
        foreach ($param as $k => $v) {
            $sign .= $k . "=" . $v . "&";
        }

        $sign .= "key=" . $this->config['KEY'];

        $re = hash('sha256', $sign, $this->config['KEY']);
        return bin2hex($re);
    }

    /**
     * xml转为数组 
     */
    private function xmlToArray($xmlStr) {
        $msg = array();
        $postStr = $xmlStr;
        $msg = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $msg;
    }

}
