<?php

namespace App\Controller;

use Common\Controller\AppbaseController;


/**
 * 首页
 */
class IndexController extends AppbaseController {

    protected $user_model;
    protected $advert_model;
    protected $killer_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->user_model = D("Common/Users");//加载用户model
        $this->advert_model = D("Common/Advert");
        $this->killer_model = D("Home/Killer");//高手模块
    }
    //首页
    public function index() {
        $configInfo=array(
            "ip"=>get_client_ip(0, true),
        );
        $this->ajaxReturn(200,"成功！",$configInfo);
    }
    
    //用户登录
    
    public function login(){

        $rules=$this->getRules('login');//验证规则
        $this->checkField($rules, $this->params);
 
        $whereArray=array(
            "user_login"=>$this->params['username'],
            "user_pass"=>sp_password($this->params['password']),
        );
        
        $userInfo= $this->user_model->where($whereArray)->find();//查询登录用户
        if(empty($userInfo)){
           $this->ajaxReturn(500,"帐号/密码错误！",""); 
        }
        
        $userInfo['token']=$this->getToken();
        
        $updateArray=array(
            "token"=>$userInfo['token'],
            "last_login_time"=>date("Y-m-d H:i:s"),
        );
        
        $this->user_model->where($whereArray)->save($updateArray);//更新登录信息
  
        $this->ajaxReturn(200,"登录成功！",$userInfo);

    }
    
    //获取登录token
    public function getToken() {

        if(!empty($_SESSION['uc_user'])){
            $ucUser=$_SESSION['uc_user'];
            //$this->params['jrw_id']=$ucUser['jrw_id'];
            $this->params['jrw_id']=$ucUser['uid'];
            $this->params['user_nicename']=$ucUser['username'];
            $this->params['avatar']=$ucUser['headimgurl_small'];
            $this->params['sex']=$ucUser['gender'];
            $this->params['mobile']=$ucUser['mobile'];
        }
        
        $rules=array(
            array('jrw_id','require','jrw_id不得为空！',1,'regex',3),
        );   
        $this->checkField($rules, $this->params);
        
        $userInfo= $this->user_model->where(array("jrw_id"=>$this->params['jrw_id']))->find();//查询登录用户
        $uid=0;//用户ID
        if(empty($userInfo)){ 
            $rules=array(
                array('user_nicename','require','user_nicename不能为空！',1,'regex',3),
                //array('mobile','require','mobile不能为空！',1,'regex',3),
            );
            $this->checkField($rules, $this->params);
            
            $updateArray=array(
                "jrw_id"=>$this->params['jrw_id'],
                "user_login"=>sp_random_string(10),
                "user_nicename"=>$this->params['user_nicename'],
                "avatar"=>$this->params['avatar'],
                "sex"=>$this->params['sex'],
                "user_email"=>$this->params['user_email'],
                "user_url"=>$this->params['user_url'],
                "mobile"=>$this->params['mobile'],
                "birthday"=>$this->params['birthday'],
                "signature"=>$this->params['signature'],
                "last_login_ip"=>get_client_ip(0, true),
                "create_time" => date("Y-m-d H:i:s"),
                "last_login_time" => date("Y-m-d H:i:s"),
                "user_status" => 1,
                "user_type" => 2, //会员
            );
            
            $uid= $this->user_model->add($updateArray);//更新登录信息
        }else{
            $uid=$userInfo['id'];
        }

        //登录信息
        $loginInfo=array(
            'avatar'=>$this->params['avatar'],
            'user_nicename'=>$this->params['user_nicename'],
            'sex'=>$this->params['sex'],
            'mobile'=>$this->params['mobile'],
            'token'=>md5(time().sp_random_string()),
            "last_login_ip"=>get_client_ip(0, true),
            "last_login_time" => date("Y-m-d H:i:s"),
        );

        $this->killer_model->where(array("id"=>$userInfo['killer_id']))->save(array('avatar'=>$this->params['avatar']));//更新高手头像
        $retInfo = $this->user_model->where(array("id"=>$uid))->save($loginInfo);//更新登录信息
        if(!$retInfo){
            $this->ajaxReturn(500,"失败！","");
        }
        
        $retArray=array(
            "uid"=>$uid,
            "token"=>$loginInfo['token']
        );
        $this->ajaxReturn(200,"成功！",$retArray);
    }


    //退出登录
    public function logout(){
    
        $this-> checkToken($this->params);//检测登录
        
        $whereArray=array(
            "id"=>$this->params['uid'],
            "token"=>$this->params['token'],
        );
        $result= $this->user_model->where($whereArray)->save(array("token"=>""));//清空token
        if(!$result){
            $this->ajaxReturn(500,"退出失败！","");
        }
        
        $this->ajaxReturn(200,"退出成功！","");
    }
    
    //获取banner广告
    public function getAdverts(){
        $advertInfo = $this->advert_model->where(array("advert_type_id" => 1,"platform"=>3))->find();
        $imageArray = unserialize($advertInfo['imgs']); //图片处理  
        $this->ajaxReturn(200,"成功！",array("list"=>$imageArray));        
    }
    
    //上传文件到云端
    public function uploadCloud(){

        $fileInfo= $this->uploadFile();
        //var_dump($fileInfo);
        if(empty($fileInfo)){
            $this->ajaxReturn(500,"文件上传失败1！","");
        }
        
        $retInfo=array();
        foreach($fileInfo as $key=>$val){

            $cloudArray=array(
                "filePath"=>TMP_UPLOAD.$val['savepath'].$val['savename'],
                "fileName"=>$val['savename']
            );

            //var_dump($cloudArray);
            $cloudInfo =  $this->cloudHandle($cloudArray);
            if($cloudInfo['status']==500){
                $this->ajaxReturn($cloudInfo['status'],$cloudInfo['msg'],$cloudInfo['data']);
            }
            $retInfo[$key]=$cloudInfo['data'];
        }
        
        $this->ajaxReturn(200,"上传成功！",$retInfo);
    }
    
    //上传文件到本地
    public function uploadLocal(){
        $fileInfo= $this->uploadFile();
        //var_dump($fileInfo);
        if(empty($fileInfo)){
            $this->ajaxReturn(500,"文件上传失败！","");
        }
        
        $host = sp_get_host();
        $uploadInfo=C("UPLOAD_INFO");      
        $retInfo=array();
        foreach($fileInfo as $key=>$val){
            $imgArray=array(
                'name'=>$key,
                'remote_url'=>$host.'/'.$uploadInfo['uploadFile']['rootPath'].$val['savepath'].$val['savename'],
                'local_url'=>'/'.$uploadInfo['uploadFile']['rootPath'].$val['savepath'].$val['savename'],
            );
            $retInfo[]=$imgArray;
        }
        $dataInfo['list']=$retInfo;
        $this->ajaxReturn(200,"上传成功！",$dataInfo);
    }
    
    

    private function getRules($index){
        $rules['login'] = array(
            array('username','require','登录名不得为空！',1,'regex',3),
            array('password','require','密码不能为空！',1,'regex',3),
        );
        return $rules[$index];
    }
    
    
    /*
    public function test(){
        $killerList= $this->killer_model->select();
        $adeptArray=C("ADEPT_TYPE");
        foreach ($killerList as $key=>$val){
            $tempArray=array();
            $tempArray[]=$adeptArray[$val['adept_type']];
            $this->killer_model->where(array("id"=>$val['id']))->save(array("adept_names"=> serialize($tempArray)));
        }
    }*/
    
    
    
}
