<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
class userInfo{
    public function index() {
        $retInfo=array(
            'status' => 1,
            'msg' => "登录成功！",
            'data' => array(),
        );
        
        if(empty($_SESSION['uc_user'])){
            $retInfo['status']=0;
            $retInfo['msg']="您还没有登录！";
            exit(json_encode($retInfo)); 
        }
        $ucUser = $_SESSION['uc_user'];
        $userInfo['jrw_id']=$ucUser['uid'];
        $userInfo['user_nicename']=$ucUser['username'];
        $userInfo['avatar']=$ucUser['headimgurl_small'];
        $userInfo['sex']=$ucUser['gender'];
        $userInfo['mobile']=$ucUser['mobile'];
        $retInfo['data']=$userInfo;
        exit(json_encode($retInfo));
    }
}

$userInfo = new userInfo();
$userInfo->index();
