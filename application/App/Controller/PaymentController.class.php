<?php

namespace App\Controller;

use Common\Controller\AppbaseController;
use Common\Lib\AppPay\alipay\Alipay;
use Common\Lib\AppPay\wechat\AppWechatPay;
/**
 * 订单
 */
class PaymentController extends AppbaseController {

    protected $order_model;
    protected $common_order;
   
    
    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Home/Order");//订单模块
        $this->common_order = D("Common/Order");//订单模块
    
    }
    
    //获取支付宝支付签名信息
    public function alipayInfo(){
        
    }
    
    
    //支付宝回调接口
    public function alipayNotify(){

        $alipay=new Alipay();
        $notifyInfo = $alipay->cheack($this->params);
        if(empty($notifyInfo)){
            echo "fail";
            exit;
        }
        //支付成功回调
        if ($notifyInfo['trade_status'] == 'TRADE_SUCCESS') {
           
            //校验金额
            $orderInfo = $this->common_order->where(array("order_sn"=>$notifyInfo['out_trade_no']))->find();
            if($orderInfo['total_money']!=$notifyInfo['total_amount']){
                echo "fail";
                exit;
            }
            
            $orderInfo['pay_type']=2;//支付类型
            $this->order_model ->callBack($orderInfo);
            echo 'success';
            exit;
        }
        
        echo "fail";
        exit;  
    }
    
    
    //获取微信支付签名信息
    public function wechatInfo(){
        //验证规则
        $rules = array(
            array('token','require','token不得为空！',1,'regex',3),
            array('body','require','body不得为空！',1,'regex',3),
            array('orderNo','require','orderNo不得为空！',1,'regex',3),
            array('total_fee','require','total_fee不得为空！',1,'regex',3),
        );
        
        //file_put_contents("postData.txt", json_encode($this->params));
        
        $this->checkField($rules, $this->params);//验证字段
        $config=C('app_pay_config.wechat');
        $wechatPay=new AppWechatPay($config);
        $dataInfo=$wechatPay->getWxPrepayid($this->params);
        $this->ajaxReturn(200,"成功！",$dataInfo);
    }
    
    //微信支付回调接口
    public function wechatNotify(){
        
        $config=C('app_pay_config.wechat');
        $wechatPay=new AppWechatPay($config);
        $notifyInfo=$wechatPay->noify_cheack();

        if($notifyInfo['status']==200){
            //校验金额
            $orderInfo = $this->common_order->where(array("order_sn"=>$notifyInfo['data']['out_trade_no']))->find();
            if($orderInfo['total_money']*100!=$notifyInfo['data']['total_fee']){
                $this->show($wechatPay->returnInfo("FAIL", "订单金额校验失败！"), 'utf-8', 'text/xml');
                exit;
            }
            $orderInfo['pay_type']=3;//支付类型
            $this->order_model ->callBack($orderInfo);
        }
       
        $this->show($notifyInfo['msg'], 'utf-8', 'text/xml');
        exit;
    }
    
    
    //苹果支付回调接口
    public function iosNotify(){
        
        $rules = array(
            array('receipt_data','require','receipt_data不得为空！',1,'regex',3),
            array('order_sn','require','order_sn不得为空！',1,'regex',3),
            array('course_id','require','course_id不得为空！',1,'regex',3),
        );
        
        $this->checkField($rules, $this->params);//验证字段
        
        $isSandbox=!empty($this->params['isLine'])?false:true;
        
        //$isSandbox = true;
        //如果是沙盒模式，请求苹果测试服务器,反之，请求苹果正式的服务器
        if ($isSandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        }
        else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        $postData = json_encode(array('receipt-data' => $this->params['receipt_data']));

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        $data = json_decode($response);

        //判断时候出错，抛出异常
        if ($errno != 0) {
            $this->ajaxReturn(500,$errmsg,null);
        }

        //判断返回的数据是否是对象
        if (!is_object($data)) {
            $this->ajaxReturn(500,"无效的响应数据",null);
        }
        //判断购买时候成功
        if (!isset($data->status) || $data->status != 0) {
            $this->ajaxReturn(500,"无效的收据",null);
        }
        
        $order = $data->receipt->in_app;//所有的订单的信息
        $k = count($order) -1;
        $need = $order[$k];//需要的那个订单
        
        //if($need->product_id==$this->params['course_id']){
            $orderInfo = $this->common_order->where(array("order_sn"=>$this->params['order_sn']))->find();
            $orderInfo['pay_type']=5;//支付类型
            $this->order_model ->callBack($orderInfo);

        //}
        
        $this->ajaxReturn(200,"支付成功！",array());

    }
    
    //
    public function swiftNotify(){
        
    }
    
    
    

    
}
