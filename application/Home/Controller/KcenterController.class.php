<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
use Home\Model\KillerModel;
use Home\Model\KillerFansModel;
use Home\Lib\FileOpera;
/**
 * 高手个人中心
 */
class KcenterController extends HomebaseController {
    
    function _initialize() {
        parent::_initialize();
        if (!sp_is_user_login()) {//判断是否登录  
            redirect(C('JRW_URL')."/userlogin.html");   
        }
        
        if(!sp_is_tuser_login()){//判断是否为高手
            redirect(U('User/index'));   
        }
        
        $this->assign("tuser",session('tuser'));
    }
    
    //首页
    public function index() {

        $this->display(":kcenter:index");
    }
    
    //个人资料
    public function live(){
        $this->display(":kcenter:index:live");
    }
    
    //订阅标准
    public function subscribe(){
        $this->display(":kcenter:index:subscribe");
    }
    
    //已收到礼物
    public function gift(){
        $this->display(":kcenter:index:gift");
    }
    
    //我的粉丝
    public function fans(){
       
        if (IS_POST) {
            $data = I('post.');
            //$data['pageLimit']=1;
            $killerInfo=sp_get_current_tuser();
            $data['killer_id']=$killerInfo['id'];
            $fansList= D('KillerFans')->fansList($data);           //排序列表
            $this->assign('fansList', $fansList['data']['list']);    //列表信息
            $this->assign('pageInfo', $fansList['data']['pageInfo']);  //分页信息
            $html = $this->fetch(":kcenter:index:fans");
            exit($html);
        }
        
        //$this->display(":index:fans");
    }
    
    //个人资料
    public function info(){
        $this->display(":kcenter:index:info");
    }

   
}