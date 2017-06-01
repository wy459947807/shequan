<?php

namespace App\Controller;

use Common\Controller\AppbaseController;

/**
 * 消息
 */
class CourseController extends AppbaseController {


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
        
    }

    //获取课程列表
    public function getList(){
        $dataInfo = $this->course_model->courseList($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //获取课程详情
    public function getOne(){
        //验证规则
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),
        );
        //字段验证
        $this->checkField($rules, $this->params);
        $dataInfo = $this->course_model->courseDetail($this->params);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //提交评论
    public function submitComment(){
        
        $this->checkToken($this->params);//检测登录
        //验证规则
        $rules = array(
            array('course_id','require','course_id不得为空！',1,'regex',3),
            array('killer_id','require','killer_id不得为空！',1,'regex',3),
            array('content','require','content不得为空！',1,'regex',3),
        );
        //字段验证
        $this->checkField($rules, $this->params);
        
        $userInfo=$this->user_model->where(array("killer_id"=>$this->params['killer_id']))->find();
        
        $paramsArray=array(
            'uid'=> $this->params['uid'],
            'post_id'=> $this->params['course_id'],
            'to_uid'=> $userInfo['id'],
            'content'=>$this->params['content'],
            'post_table'=>"course",
        );

        $dataInfo = $this->comment_model->submitComment($paramsArray); 
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
  
    }
    
    //获取课程评论
    public function getComments(){
        //验证规则
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),
        );
        //字段验证
        $this->checkField($rules, $this->params);
        $commentArray=array(
            "post_table"=>"course",
            "post_id"=>$this->params['id']
        );
        $dataInfo = $this->comment_model->getComments($commentArray);
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    
    

    
    
}
