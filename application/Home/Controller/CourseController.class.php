<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class CourseController extends HomebaseController {

    protected $course_model;
    protected $comment_model;
    protected $user_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->course_model = D("Common/Course");//课程模块
        $this->comment_model = D("Common/Comments");//评论模块
        $this->user_model = D("Home/Users");//加载用户model
    }
    
    //首页
    public function index() {
        $dataInfo = $this->course_model->courseList($this->params);
        $this->assign('courseList', $dataInfo['data']['list']);  //列表信息
        $this->assign('pageInfo', $dataInfo['data']['pageInfo']); //分页信息
        $this->display(":course:index");
    }
    
    public function info(){
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = get_remote_data($this->params, "Course/getOne");
        $this->assign('course', $dataInfo['data']);  //列表信息
        $this->display(":course:info");
    }
    
    public function order(){
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = get_remote_data($this->params, "Course/getOne");
        $this->assign('course', $dataInfo['data']);  //列表信息
        $this->display(":course:order");
    }
    
    public function pay(){
        $rules = array(
            array('order_sn','require','order_sn不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $orderInfo = get_remote_data($this->params, "Order/orderDetail");
        
        if(empty($orderInfo['data'])){
            $this->error("订单不存在！");
        }
        
        $parameterInfo= get_remote_data(array(), "Index/index");
        $payInfo=array(
            'body'=>$orderInfo['data']['order_name'],//订单名称
            'out_trade_no'=>$orderInfo['data']['order_sn'],//订单编号
            'total_fee'=>$orderInfo['data']['total_money']*100,//总金额(不能超过5000000)
            'attach'=>$orderInfo['data']['order_sn'],//附加信息
            'mch_create_ip'=>$parameterInfo['data']['ip'],//获取ip
            'method'=>"submitOrderInfo",//操作类型
            'time_expire'=>"",	
            'time_start'=>"",	
        );

        $this->assign('order', $orderInfo['data']);  //列表信息
        $this->assign('parameter', $parameterInfo['data']);  //列表信息
        $this->assign('payInfo', $payInfo);  //列表信息
        $this->display(":course:pay");
               
    }
    
    public function buySuccess(){
        $rules = array(
            array('order_sn','require','order_sn不得为空！',1,'regex',3),  
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $orderInfo = get_remote_data($this->params, "Order/orderDetail");
        $this->assign('order', $orderInfo['data']);  //列表信息
        $this->display(":course:buy_success");
    }
    
    
    

}