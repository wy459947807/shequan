<?php

//导航管理

namespace Api\Controller;
use Common\Controller\AppframeController;
use Common\Lib\Wechat;

class WechatController extends AppframeController {
    
    private $wechatObj;
    private $wechatInfo;  
    private $user_model;

    public function _initialize() {
        parent::_initialize();
        $this->wechatInfo=C('wechat');                          //获取微信配置信息
        $this->wechatObj=new Wechat($this->wechatInfo['config']);  //初始化微信对象
        $this->user_model=D("Common/ActUsers"); 
    }
 
    //微信自动回复
    public function index() {
        $wechat = $this->wechatObj;
        //$this->wechatObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $wechat->getRev()->getRevType();
        switch ($type) {
            case $wechat::MSGTYPE_TEXT:
                //$word=$wechat->getRevContent();//获取接收文字
                $wechat->text("hello, I'm wechat")->reply();
                exit;
                break;
            case $wechat::MSGTYPE_EVENT:
                break;
            case $wechat::MSGTYPE_IMAGE:
                break;
            default:
                $wechat->text("help info")->reply();
        }
    }

    //设置菜单
    public function createmenu() {
        $newmenu =  $this->wechatInfo['menu'];
        $result = $this->wechatObj->createMenu($newmenu);
        var_dump($result);
    }

    public function authRedirect(){
        $params=I("get.");
        if(!empty($params['url'])){
            header("location: ".$params['url'].'?code='.$params['code']);
            exit;
        }
    }


    //微信用户认证回调页面
    public function authUser() {
        if (IS_GET) {
            $token = $this->wechatObj->getOauthAccessToken();
            //var_dump($token);
            if ($token == false) {
                return $this->ajaxReturn(array("status" => 500,"msg" => "获取openid失败！", "data" => ""));
            } else {
                $openid = $token['openid'];
                
                //file_put_contents('1.txt', json_encode($token));
                //$userinfo = $this->wechatObj->getUserInfo($openid);//需要关注后才能获取用户信息
                $userinfo = $this->wechatObj->getOauthUserinfo($token['access_token'],$token['openid']);//不需要关注就能获取用户信息
                //var_dump($userinfo);die();
                //echo json_encode($userinfo);die();
                if(!empty($userinfo)){
                  $retInfo=$this->user_model->userUpdate($userinfo);
                  $userinfo['id']=$retInfo['data'];
                }
                return $this->ajaxReturn(array("status" => 200, "msg" => "认证成功！", "data" => $userinfo));
            }
 
        }
    }
    
    public function setTest(){
       echo  S("777","666",array('type'=>'file','expire'=>3600)); //设置缓存
    }




    public function sign() {
            $resultArray = array();
            $params=I("post.");

            $url = $params['url'];
            $sign = $this->wechatObj->getJsSign(htmlspecialchars_decode($url), 0, '',$this->wechatInfo['config']['appid']);
            $resultArray['status'] = 1;
            $resultArray['msg'] = '成功';
            $resultArray['data'] = $sign;
            return $this->ajaxReturn($resultArray);
    }

    //微信用户登录
    public function Oauthlogin() {
        /*
          OpenLoginRequest {
          avatar_url (string, optional):

          用户头像的URL stringDefault:http://wx.qlogo.cn/mmopen/JQpUg1oh5adIwByjCjRaibQku0dXIOAWt6dBbUibDV3QrHNFQzwZ5GTQoKbsHoHD3Zq35ibNCxlsG3s3IwUbuOhHw/0,
          cid (string, optional):

          getui notify client ID ,
          display_name (string, optional):

          用户的昵称 stringDefault:Lincoln,
          openid (string, optional):

          微信用户的OPENID或者授权的用户名字 stringDefault:okwyOwpvP0WJfi0GhGxzQ5sDJMCY,
          type (string, optional):

          Oauth authorize type, eg 1 – Wechat, 2 - SINA, 3 - QQ , .. stringDefault:wehcat
          } */
        //header('Access-Control-Allow-Origin:*');
        if (IS_POST) {
            $resultArray=array("result" => 0,
                    "msg" => "用户登录成功",
                    "token" =>"aaeeffe",
                    "display_name" => "aaa",);
            $params=$this->getParams();
            $userInfo = $this->wechatObj->getUserInfo($params['openid']);
            if(!empty($userInfo)){
                $resultArray = array(
                    "result" => 0,
                    "msg" => "用户登录成功",
                    "token" => "dddffffee",
                    "display_name" => "aaa",
                );
            }
            return $this->ajaxReturn($resultArray);
        }
    }
    
    
    


    
    
    public function Test(){
       echo  sp_get_host().'/index.php/api/wechat/getopenid';
       // echo json_encode($_POST);
    }
   

}
