<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
use Home\Model\KillerModel;
use Home\Lib\FileOpera;
/**
 * 首页
 */
class UserController extends HomebaseController {
    
    function _initialize() {
        parent::_initialize();
        if (!sp_is_user_login()) {//判断是否登录  
            redirect(C('JRW_URL')."/userlogin.html");   
        }
        
        if(sp_is_tuser_login()){//判断是否为高手
            redirect(U('Kcenter/index'));   
        }
    }

    public function index() {

        $focusList= D('KillerFans')->focusList(array());
        //var_dump($focusList['data']['list']);
        $this->display();
    }
    
    
    public function focus(){
        if (IS_POST) {
            $data = I('post.');
            //$data['pageLimit']=1;
            $userInfo=sp_get_current_user();
            $data['user_id']=$userInfo['id'];
            $focusList= D('KillerFans')->focusList($data);           //排序列表
            
            if(!empty($focusList['data'])){
                $focusList['data']['list']= D('killer')->listProcess($focusList['data']['list'],$data['user_id']);
            }

            $this->assign('focusList', $focusList['data']['list']);    //列表信息
            $this->assign('pageInfo', $focusList['data']['pageInfo']);  //分页信息
            $html = $this->fetch(":user:focus");
            exit($html);
        }
    }
    
    
    public function live(){
        if (IS_POST) {
            $data = I('post.');
         
            $html = $this->fetch(":user:live");
            exit($html);
        }
    }
    
    
    public function order(){
        if (IS_POST) {
            $data = I('post.');
         
            $html = $this->fetch(":user:order");
            exit($html);
        }
    }
    
    public function coin(){
        if (IS_POST) {
            $data = I('post.');
         
            $html = $this->fetch(":user:coin");
            exit($html);
        }
    }
    
    
   
   
   
}