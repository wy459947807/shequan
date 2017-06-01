<?php

namespace App\Controller;

use Common\Controller\AppbaseController;

/**
 * 消息
 */
class MessageController extends AppbaseController {

  
    protected $message_model;
    protected $gift_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->message_model = D("Home/TalksLog");//消息模块
        $this->gift_model = D("Common/Gift");//礼物模块
    }
    
    
    //首页
    public function index() {
        
    }

    //获取最新发言
    public function getMessages(){
        $dataInfo = $this->message_model->getList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
        
    //查看老师消息
    public function messageInfo(){
        $userInfo = $this->checkToken($this->params);//检测登录
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = $this->message_model->getMsgInfo($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']); 
    }
    
    
    //获取礼物列表
    public  function giftList(){
        $dataInfo = $this->gift_model->giftList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //发送礼物
    public function sendGift(){
        $userInfo = $this->checkToken($this->params);//检测登录
        $rules = array(
            array('killer_id','require','killer_id不得为空！',1,'regex',3), 
            array('gift_id','require','gift_id不得为空！',1,'regex',3), 
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = $this->message_model->sendGift($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);  
    }
    

}
