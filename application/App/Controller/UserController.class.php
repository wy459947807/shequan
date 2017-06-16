<?php

namespace App\Controller;

use Common\Controller\AppbaseController;

/**
 * 个人中心
 */
class UserController extends AppbaseController {

    protected $user_subscribe_model;
    protected $user_model;
    protected $order_model;
    protected $killer_fans_model;
    
    
    public function _initialize() {
        parent::_initialize();

        $this->user_subscribe_model = D("Home/UserSubscribe");
        $this->user_model = D("Home/Users");//加载用户model
        $this->order_model = D("Common/Order");//加载订单model
        $this->killer_fans_model = D("Home/KillerFans");//加载订单model
    }
    
    
    //获取用户个人信息
    public function userInfo(){
        $this->checkToken($this->params);//检测登录
        $dataInfo = $this->user_model->getDetail($this->params);
        $this->ajaxReturn(200,"成功!",$dataInfo['data']);
    }
    
    //我的订阅
    public function userSubscribe(){
        $this->checkToken($this->params);//检测登录
        $dataInfo = $this->user_model->userSubscribe($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //获取高手被订阅的记录
    public function killerSubscribe(){
        $this->checkToken($this->params);//检测登录
        $dataInfo = $this->user_model->killerSubscribe($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //我的订单
    public function userOrder(){
        $this->checkToken($this->params);//检测登录
        $dataInfo = $this->order_model->orderList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //我购买的课程
    public function userCourse(){
        $this->checkToken($this->params);//检测登录
        $dataInfo = $this->user_model->userCourse($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }


    //获取我的订阅标准
    public function getSubscribe() {
        $this->checkKiller($this->params);//检测登录
        $dataInfo = $this->user_model->getDetail($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],array("list"=>$dataInfo['data']['subscribe']));
    }

    //制定订阅标准
    public function setSubscribe(){
        $this->checkKiller($this->params);//检测登录
        //验证规则
        if(empty($this->params['price_array'])) $this->ajaxReturn(500,"price_array不能为空!","");
        $dataInfo = $this->user_model->setSubscribe($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //会员订阅操作
    public function subscribeKiller(){
        $userInfo = $this->checkToken($this->params);//检测登录
        
        $rules = array(
            array('killer_id','require','killer_id不得为空！',1,'regex',3),
            array('num','require','num不得为空！',1,'regex',3),
            array('type','require','type不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = $this->user_subscribe_model->add($this->params); 
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //我的粉丝
    public function userFans(){
        $userInfo = $this->checkToken($this->params);//检测登录
        $dataInfo = $this->killer_fans_model->fansList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //我的关注
    public function userFocus(){
        $userInfo = $this->checkToken($this->params);//检测登录
        $dataInfo = $this->killer_fans_model->focusList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //我收到的礼物
    public function killerGift(){
        $userInfo = $this->checkToken($this->params);//检测登录
        $dataInfo = $this->user_model->killerGift($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    

}
