<?php

namespace App\Controller;

use Common\Controller\AppbaseController;

/**
 * 订单
 */
class OrderController extends AppbaseController {

    protected $order_model;
    protected $common_order;
   
    
    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Home/Order");//订单模块
        $this->common_order = D("Common/Order");//订单模块
    
    }
    
    
    //首页
    public function index() {
        
    }

    //下单操作
    public function submitOrder(){
        $this-> checkToken($this->params);//检测登录
        //验证规则
        if(empty($this->params['course_ids'])) $this->ajaxReturn(500,"course_ids不能为空!","");
        if(empty($this->params['course_num'])) $this->ajaxReturn(500,"course_num不能为空!","");
        $dataInfo= $this->order_model->submitOrder($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
        
    }
    
    //订单详情
    public function orderDetail(){
      
        $this-> checkToken($this->params);//检测登录
        //验证规则
        $rules = array(
            array('order_sn','require','order_sn不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $dataInfo=$this->common_order->orderDetail($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
        
    }
    
    //更新支付方式
    public function orderUpdate(){
        $this-> checkToken($this->params);//检测登录
        //验证规则
        $rules = array(
            array('order_sn','require','order_sn不得为空！',1,'regex',3),
            array('pay_type','require','pay_type不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $dataInfo=$this->common_order->orderUpdate($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
        
    }
    
    //订单支付成功回调接口
    public function callback(){
        
        //此处填写回调安全校验代码
        
        $rules = array(
            array('pay_type','require','pay_type不得为空！',1,'regex',3),
            array('order_sn','require','order_sn不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = $this->order_model ->callBack($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
        
    }
    


  
    
}
