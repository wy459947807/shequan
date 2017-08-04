<?php

namespace App\Controller;

use Common\Controller\AppbaseController;

/**
 * 高手
 */
class KillerController extends AppbaseController {

    protected $killer_model;
    protected $common_killer;
    protected $killer_fans_model;
    protected $user_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->killer_model = D("Home/Killer");//高手模块
        $this->common_killer = D("Common/Killer");//高手模块
        $this->killer_fans_model = D("Home/KillerFans");
        $this->user_model = D("Home/Users");
        
    }
    
    
    //首页
    public function index() {
        
    }

    //今日推荐
    public function tadayRec(){
        
        $this->params['dateTime']=date("Y-m-d H:i:s");
        $this->params['orderType']=1;
        
        $dataInfo = $this->killer_model->getTopList($this->params); //今日推荐
        if(empty($dataInfo['data']['list'])){
            unset($this->params['dateTime']);
            $dataInfo = $this->killer_model->getTopList($this->params); //今日推荐
        }

        //列表字段处理
        if(!empty($dataInfo['data']['list'])){
            $dataInfo['data']['list']=$this->killer_model->listProcess($dataInfo['data']['list'],$this->params['uid']);
        }
        
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //获取高手列表（圈主分享）
    public function getKillers(){
        //array("status" => 1, "orderType" => 0, "page" => 1, "pageLimit" => 30)
        $dataInfo = $this->killer_model->getList($this->params); //综合排序
        
        //数据列表字段处理
        if(!empty($dataInfo['data'])){
            $dataInfo['data']['list']=$this->killer_model->listProcess($dataInfo['data']['list'],$this->params['uid']);
        }
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'],$dataInfo['data']);
    }
    
    //获取高手排行榜
    public function getRank(){
        
        $rules = array(
            array('adeptType','require','adeptType不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        
        $adept = $this->params['adeptType'];
        
        //高手榜单
        $adeptType= C('ADEPT_TYPE');
        
        //var_dump($adeptType);
        $listInfo=array();
        foreach ($adeptType as $key=>$val){
            
            $this->params['adeptType']=$key;
            $this->params['orderType']=2;
            $listInfo[$key]=$this->killer_model->getTopList($this->params); //高手榜
            if(!empty($listInfo[$key]['data']['list'])){
                $listInfo[$key]['data']['list']=$this->killer_model->listProcess($listInfo[$key]['data']['list'],$this->params['uid']);
            }
        }
        
        $dataInfo=$listInfo[$adept];
        $this->ajaxReturn($dataInfo['status'],$dataInfo['msg'], $dataInfo['data']);
    }
    
    //关注高手
    public function focusKiller(){
        $this->checkToken($this->params);//检测登录
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo = $this->killer_fans_model->focusKiller(array("killer_id"=>$this->params['id'],"users_id"=>$this->params['uid']));
        $this->ajaxReturn($dataInfo['status'], $dataInfo['msg'], "");
    }
    
    
    //高手详情
    public function killerDetail(){

        //验证规则
        $rules = array(
            array('id','require','id不得为空！',1,'regex',3),
        );
        $this->checkField($rules, $this->params);//验证字段

        $this->common_killer->where(array("id" => $this->params['id']))->setInc("views",1); //浏览数量+1 
        $dataInfo= $this->common_killer->getDetail($this->params);

        $this->ajaxReturn($dataInfo['status'], $dataInfo['msg'], $dataInfo['data']);
    }
    
    //申请民间高手认证
    
    //文件上传字段：card_img  cert_imgs
    public function registKiller(){
        
        $this->checkToken($this->params);//检测登录
        $rules = array(
            array('real_name','require','real_name不得为空！',1,'regex',3),
            array('mobile','require','mobile不得为空！',1,'regex',3),
        );
        
        
        $this->checkField($rules, $this->params);//验证字段
        $dataInfo= $this->killer_model->killerRegist($this->params);
        $this->ajaxReturn($dataInfo['status'], $dataInfo['msg'], $dataInfo['data']);
    }
    
    //获取我提交的高手认证信息
    public function killerInfo(){
        $this->checkToken($this->params);//检测登录
        $dataInfo= $this->user_model->killerInfo($this->params);
        $this->ajaxReturn($dataInfo['status'], $dataInfo['msg'], $dataInfo['data']);
    }
    
    

}
