<?php

namespace Tlive\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {



    //首页
    public function index() {
        session("__TL_ADMIN_LOGIN_PAGE_SHOWED_SUCCESS__",true);
        if(sp_is_tuser_login()){
            redirect(U("Set/index"));
        }
        $this->display(":login");
    }

    public function dologin(){

        $login_page_showed_success=session("__TL_ADMIN_LOGIN_PAGE_SHOWED_SUCCESS__");
        if(!$login_page_showed_success){
            $this->error('login error!');
        }
        $name = I("post.username");
        if(empty($name)){
            $this->error(L('USERNAME_OR_EMAIL_EMPTY'));
        }
        $pass = I("post.password");
        if(empty($pass)){
            $this->error(L('PASSWORD_REQUIRED'));
        }
        $verrify = I("post.verify");
        if(empty($verrify)){
            $this->error(L('CAPTCHA_REQUIRED'));
        }
        //验证码
        if(!sp_check_verify_code()){
            $this->error(L('CAPTCHA_NOT_RIGHT'));
        }else{
            $user = M('killer');
            $where['user_login']=$name;

            $result = $user->where($where)->find();
            if(!empty($result) && $result['status']==1){
                if(sp_compare_password($pass,$result['user_pass'])){

                    //登入成功页面跳转
                    session('tuser',$result);
                    $result['last_login_ip']=get_client_ip(0,true);
                    $result['last_login_time']=date("Y-m-d H:i:s");
                    $user->save($result);
                    cookie("tlive_username",$name,3600*24*30);
                    redirect(U("Set/index"),0,L('LOGIN_SUCCESS'));
                }else{
                    $this->error(L('PASSWORD_NOT_RIGHT'));
                }
            }else{
                $this->error(L('USERNAME_NOT_EXIST'));
            }
        }
    }
//
//    public function tlive(){
//        $this->display(":tlive");
//    }

    public function adduser(){
        // echo sp_password('shaolisheng');exit;
        $data = [
            'user_login' => 'wangqingfu',
            'user_pass'  => sp_password('wangqingfu'),
            'real_name'  => '王清福',
            'avatar'     => '/themes/frontend/Public/home/images/home/Student12.jpg',
            'adept_type' =>1,
            'intro'       =>'中国上饶耀亚资本管理有限公司（总经理），江西上饶人，南昌大学创业导师，上饶政协委员（连任16年），优秀共产党员，投资股票17年 ，投资期货6年。',
            'ctime'      => time(),
            'tag'        => '紧跟趋势|短线操作|风险控制',
            'status'     =>1,
            'type'       =>0
        ];
        $user = M('killer')->add($data);
        print_r($user);exit;
    }

    public function logout(){
        session("tuser",null);
        redirect(U('index/index'));
    }

}