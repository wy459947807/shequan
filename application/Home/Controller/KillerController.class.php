<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
use Home\Model\KillerModel;
use Home\Lib\FileOpera;
/**
 * 高手注册
 */
class KillerController extends HomebaseController {
   
    protected $user=array();
    protected $tuser=array();
        
    function _initialize() {
        parent::_initialize();

        if (!sp_is_user_login()) {//判断是否登录  
            redirect(C('JRW_URL')."/userlogin.html");   
        }
    }
    
    //高手注册
    public function index() {
        $killerInfo = get_remote_data($this->params,'Killer/killerInfo');//高手详情
        $this->assign('killerInfo',$killerInfo['data']);  //擅长领域
        $this->assign('adeptType',C("ADEPT_TYPE"));  //擅长领域
        $this->assign('cardType',C("CARD_TYPE"));    //资格证种类
        $this->display(":killer:index");

    }
   
}