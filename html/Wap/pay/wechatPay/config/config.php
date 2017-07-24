<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        //'mchId'=>'7551000001',//测试商户号，商户上线需改为自己正式的
        //'key'=>'9d101c97133837e13dde2d32a5054abb',//测试密钥，商户上线需改为自己正式的
        'mchId'=>'102510378864',//测试商户号，商户上线需改为自己正式的
        'key'=>'b96669f646269393419e2518a360cfdf',//测试密钥，商户上线需改为自己正式的
        'version'=>'2.0',
        'notify_url'=>'http://msq.10jrw.com/pay/wechatPay/request.php?method=callback'//异步回调通知地址，商户上线需改为自己正式的
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>