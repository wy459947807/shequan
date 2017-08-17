<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class MessageController extends HomebaseController {

    protected $course_model;
    protected $comment_model;
    protected $user_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->course_model = D("Common/Course");//课程模块
        $this->comment_model = D("Common/Comments");//评论模块
        $this->user_model = D("Home/Users");//加载用户model
    }
 
    
    public function msgList(){
        $dataArray=array(
            "pageLimit"=>20,
            "role"=>1,
            "msg_type"=>1,
            "is_charge"=>0
        );
        $dataInfo = get_remote_data(array_merge($this->params, $dataArray), "Message/getMessages");
        $this->assign('messages', $dataInfo['data']['list']);  //列表信息
        $this->assign('pageInfo', $dataInfo['data']['pageInfo']); //分页信息
        $this->display(":message:msg_list");  
    }
    

    public function individualShare(){
       
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),  
        );
        
        $this->checkField($rules, $this->params);//验证字段
        $this->display(":message:individual_share");  
    }
    
    

}