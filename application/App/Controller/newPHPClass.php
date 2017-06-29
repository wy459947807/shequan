<?php  
  
  
  
  
    /**  
     *   微信支付服务端相关业务   
     *   @author 魏鹏 
     */  
class WxapiAction extends Action{  
      
    const WXURL = "https://api.mch.weixin.qq.com/pay/unifiedorder";  
    const NOIFYURL = "";//微信异步回掉地址  
    const APPID = '';  
    const MCHID = '';//商家号  
    const KEY = '';  
    const APPSECRET = '';  
    private $wxDate = null;  
      
    /**  
     * 请求微信API进行微信统一下单 
     * URL地址：https://api.mch.weixin.qq.com/pay/unifiedorder 
     */  
     public function getWxPrepayid(){  
         header("Content-type: text/html; charset=utf-8");  
         $order = $this -> chargepay_api();  
         //配置参数检测  
         $this -> checkCofigParam();  
         $noceStr = md5(rand(100,1000).time());//获取随机字符串  
         $time = time();  
         $body = $_REQUEST['c_Money1']*100;  
         $paramarr = array(  
             "appid"       =>    WxapiAction::APPID,  
             "body"        =>    "",//说明内容  
             "mch_id"      =>    WxapiAction::MCHID,  
             "nonce_str"   =>    $noceStr,  
             "notify_url"  =>    WxapiAction::NOIFYURL,  
             "out_trade_no"=>    $order,  
             "total_fee"   =>    "1",   
             "trade_type"  =>    "APP"  
  
  
         );  
         $sign = $this -> sign($paramarr);//生成签名  
         $paramarr['sign'] = $sign;  
         $paramXml = "<xml>";  
         foreach($paramarr as $k => $v){  
            $paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";  
               
         }  
         $paramXml .= "</xml>";  
         file_put_contents("./test.txt", "");  
         file_put_contents("./test.txt", $paramXml);  
         $ch = curl_init ();  
         @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查    
         @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在    
         @curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");  
         @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
         @curl_setopt($ch, CURLOPT_POST, 1);  
         @curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);  
         @$resultXmlStr = curl_exec($ch);  
         if(curl_errno($ch)){  
             print curl_error($ch);  
         }  
         curl_close($ch);  
         file_put_contents("./test2.txt", "");  
         file_put_contents("./test2.txt", $resultXmlStr);  
         $result = $this -> xmlToArray($resultXmlStr);  
          
         $time2 = time();  
         $prepayid = $result['prepay_id'];  
         $sign = "";  
         $noceStr = md5(rand(100,1000).time());//获取随机字符串  
         $paramarr2 = array(  
            "appid"     =>  WxapiAction::APPID,  
            "noncestr"  =>  $noceStr,  
            "package"   =>  "Sign=WXPay",  
            "partnerid" =>  WxapiAction::MCHID,  
            "prepayid"  =>  $prepayid,  
            "timestamp" =>  $time2  
                          
         );  
         $paramarr2["sign"] = $this -> sign($paramarr2);//生成签名  
         echo json_encode($paramarr2);     
           
     }  
       
     /** 
      * 检测配置信息是否完整    
      */  
      public function checkCofigParam(){  
          
         if(WxapiAction::APPID == ""){  
             echo "微信APPID未配置";  
             exit;  
         }elseif(WxapiAction::MCHID == ""){  
             echo "微信商户号MCHID未配置";  
             exit;  
         }elseif(WxapiAction::KEY == ""){  
             echo "微信API密钥KEY未配置";  
             exit;  
         }  
      }  
      /** 
      * sign拼装获取 
      */  
      private function sign($param){  
          
        $sign = "";  
        foreach($param as $k => $v){  
            $sign .= $k."=".$v."&";  
        }  
      
        $sign .= "key=".WxapiAction::KEY;  
        $sign = strtoupper(md5($sign));  
        return $sign;  
          
      }  
      /** 
      * xml转为数组 
      */  
      private function xmlToArray($xmlStr){  
        $msg = array();   
        $postStr = $xmlStr;   
        $msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);   
          
        return $msg;  
      }  
      //微信异步回调认证  
      public function noify_url(){  
          
          $xmlInfo = $GLOBALS['HTTP_RAW_POST_DATA'];  
            
          $this -> logecho($xmlInfo);//log打印保存  
          //解析xml  
          $arrayInfo = $this -> xmlToArray($xmlInfo);  
          $this -> wxDate = $arrayInfo;  
          /* 测试数据打印log数组转字符串=============== */  
          $test = "";  
          foreach($arrayInfo as $k => $v){  
              $test .= $k.":".$v."\t\r\n";  
          }  
          /*======================================== */  
          $this -> logecho("数据打印测试:".$test);//log打印保存  
          if($arrayInfo['return_code'] == "SUCCESS"){  
              if($return_msg != null){  
                 echo $this -> returnInfo("FAIL","签名失败");  
                 $this -> logecho("签名失败:".$sign);//log打印保存  
              }else{  
                 $wxSign = $arrayInfo['sign'];  
                 unset($arrayInfo['sign']);  
                 $arrayInfo['appid']  = WxapiAction::APPID;  
                 $arrayInfo['mch_id'] = WxapiAction::MCHID;  
                 ksort($arrayInfo);//按照字典排序参数数组  
                 $sign = $this -> sign($arrayInfo);//生成签名  
                 $this -> logecho("数据打印测试签名signmy:".$sign."微信sign:".$wxSign);//log打印保存  
                 if($this -> checkSign($wxSign,$sign)){  
                      echo $this -> returnInfo("SUCCESS","OK");  
                      $this -> logecho("签名验证结果成功:".$sign);//log打印保存  
                      $this -> orderServer();//订单处理业务逻辑  
                 }else{  
                      echo $this -> returnInfo("FAIL","签名失败");  
                      $this -> logecho("签名验证结果失败:".$sign);//log打印保存  
                 }  
                  
              }  
          }else{  
              echo $this -> returnInfo("FAIL","签名失败");  
          }  
      }  
      private function returnInfo($type,$msg){  
          if($type == "SUCCESS"){  
              return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";  
          }else{  
              return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";  
          }  
            
      
      }  
      //打印log  
      private function logecho($msg){  
          $str = file_get_contents("./logwx.txt");  
          file_put_contents("./logwx.txt",$str."\t\r\n".date("Y:m:d H:i:s")."\t\r\n".$msg);  
      }  
      //签名验证  
      private function checkSign($sign1,$sign2){  
          return trim($sign1) == trim($sign2);  
      }  
      /*fms订单查询加值业务处理 
       * @param orderNum 订单号        
       */  
      private function orderServer(){  
         //业务逻辑处理  
  
  
      }  
      //生成订单号码  
      private function chargepay_api(){  
          
          
          }  
}  