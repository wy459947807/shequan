<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
use Home\Model\KillerModel;
use Home\Lib\FileOpera;
/**
 * 首页
 */
class UserController extends HomebaseController {
    
    function _initialize() {
        parent::_initialize();
        if (!sp_is_user_login()) {//判断是否登录  
            redirect(C('JRW_URL')."/userlogin.html");   
        }

        $unitList=array(0=>"条",1=>"天", 2=>"周",3=>"月",4=>"季",5=>"年");
        $this->assign('unitList', $unitList); //总共多少赢家宝
        $this->assign('header', D('Users')->getHeader());  //头部信息
        $this->assign('footer', D('Users')->getFooter());  //尾部信息

        
    }
    
    //我的课程
    public function myCourse(){
        $this->params['pageLimit']=20;
        $courseList = get_remote_data($this->params, "User/userCourse");
        

        $this->assign('courseList', $courseList['data']['list']);    //列表信息
        $this->assign('pageInfo', $courseList['data']['pageInfo']);  //分页信息
        $this->assign('totalPrice', $courseList['data']['totalPrice']);  //总共多少赢家宝
        $this->display(":user:my_course");
    }
    
    //我的交易
    public function myDeal(){
        $this->params['pageLimit']=20;
        $orderList = get_remote_data($this->params, "User/userOrder");
        $this->assign('orderList', $orderList['data']['list']);    //列表信息
        $this->assign('pageInfo', $orderList['data']['pageInfo']);  //分页信息
        $this->assign('totalPrice', $orderList['data']['totalPrice']);  //总共多少赢家宝
        $this->display(":user:my_deal");
    }

    //订单详情
    public function myDealInfo(){
        $rules = array(
            array('order_sn','require','order_sn不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $payType=array(
            1=>'未支付',
            2=>'支付宝',
            3=>'微信',
            4=>'网上银行',
            5=>'苹果支付',
        );
        
        $order = get_remote_data($this->params, "Order/orderDetail");
        
        $this->assign('order', $order['data']);    //列表信息
        $this->assign('payType', $payType);    //列表信息
        $this->display(":user:my_deal_info");
    }
   
    
    //我的粉丝
    public function  myFans(){  
        $this->params['pageLimit']=20;
        $fansItem = get_remote_data($this->params, "User/userFans");
        $this->assign('fansItem', $fansItem['data']['list']);    //列表信息
        $this->assign('pageInfo', $fansItem['data']['pageInfo']); //分页信息
        $this->display(":user:my_fans");
    }
    
    //我的粉丝
    public function  myFocus(){  
        $this->params['pageLimit']=20;
        $focusItem = get_remote_data($this->params, "User/userFocus");
    
        $this->assign('focusItem', $focusItem['data']['list']);    //列表信息
        $this->assign('pageInfo', $focusItem['data']['pageInfo']); //分页信息
        $this->display(":user:my_focus");
    }
    
    //我的礼物
    public function  myGift(){  
        $this->params['pageLimit']=20;
        $giftItem = get_remote_data($this->params, "User/killerGift");
        $this->assign('giftItem', $giftItem['data']['list']);    //列表信息
        $this->assign('totalPrice', $giftItem['data']['totalPrice']); //总共多少赢家宝
        $this->assign('pageInfo', $giftItem['data']['pageInfo']); //分页信息
        $this->display(":user:my_gift");
    }
    
    //我的订阅
    
    
    public function  myRead(){  
        $this->params['pageLimit']=20;
        $subscribeItem = get_remote_data($this->params, "User/userSubscribe");
        $this->assign('subscribeItem', $subscribeItem['data']['list']);    //列表信息
        $this->assign('totalPrice', $subscribeItem['data']['totalPrice']); //总共多少赢家宝
        $this->assign('pageInfo', $subscribeItem['data']['pageInfo']); //分页信息
        $this->display(":user:my_read");
    }
    
    
    public function  myStanderd(){ 
        $subscribeInfo = get_remote_data($this->params, "User/getSubscribe");
        $this->assign('subscribeInfo', $subscribeInfo['data']); //
        $this->display(":user:my_standerd");
    }
    
    
    public function  myYjbao(){ 
        $this->params['pageLimit']=20;
        $subscribes = get_remote_data($this->params, "User/killerSubscribe");
        $this->assign('subscribes', $subscribes['data']['list']); 
        $this->assign('totalPrice', $subscribes['data']['totalPrice']); //总共多少赢家宝
        $this->assign('pageInfo',   $subscribes['data']['pageInfo']); //分页信息
        $this->display(":user:my_yjbao");
    }
    
    
   
   
   
}