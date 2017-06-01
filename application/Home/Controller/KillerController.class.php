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
        
        $this->user=session('user');
        if(sp_is_tuser_login()){//判断是否为高手
            $this->tuser=session('tuser');
            $this->assign("tuser",session('tuser'));
        }

    }
    
    //高手注册
    public function index() {
        if(IS_POST){
            $data=I('post.');
            $rules=$this->getRules('add');//验证规则
            
            
            $killerInfo=D("Killer")->where(array("id"=>$this->user['killer_id']))->find();
            
            $userMobile="";
            if(!empty($killerInfo)){
                $userMobile=$killerInfo['mobile'];
            }
            //验证手机号码是否存在
            $checkInfo= D("Killer")->where(array("mobile"=>array(array('eq',$data['mobile']),array('neq',$userMobile),'and')))->find();
            if(!empty($checkInfo)){
                $this->ajaxReturn(500,"该手机号码已经注册，请更换手机后再进行注册！","");  
            }

            $model = M(); 
            if ($model->validate($rules)->create()!==false){
                $data['status']=0;
                $data['user_id']=$this->user['id'];
                if(!empty($killerInfo)){
                    $data['id']=$killerInfo['id'];
                }
                
                $data['user_login']=$this->user['user_login'];//登录帐号就是手机号
                $data['user_pass']=$this->user['user_pass'];//登录帐号就是手机号
                $data['card_img']=$this->moveFile($data['card_img']);//移动图片
                $data['ctime']= time();
                $retInfo=D('killer')->killerRegist($data);//高手注册
                $this->ajaxReturn($retInfo['status'],$retInfo['msg'],$retInfo['data']);
            }else{
                $this->ajaxReturn(500,$model->getError(),"");
            }
        }else{
   
            $this->assign('adeptType',C("ADEPT_TYPE"));  //擅长领域
            $this->assign('cardType',C("CARD_TYPE"));    //资格证种类
            $this->display(":killer:index");
        }

    }
    public function help_page() {
        $this->display(":killer:help_page");
    }
    
    public function tearch_page() {   //临时tearch 登录页面
        $this->display(":killer:tearch_page");
    }
    
    private function getRules($index){
        $rules['add'] = array(
            array('real_name','require','真实姓名不得为空！',1,'regex',3),
            array('mobile','require','手机号码不能为空！',1,'regex',3),
            array('email','email','邮箱格式不正确！',1,'regex',3),
        );
        return $rules[$index];
    }


   
}