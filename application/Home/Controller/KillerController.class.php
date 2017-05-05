<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
/**
 * 首页
 */
class KillerController extends HomebaseController {
    //首页
    public function index() {
        if(IS_POST){
            $data=I('post.');
            $rules=$this->getRules('add');//验证规则
            $model = M(); 
            if ($model->validate($rules)->create()!==false){

                //图片上传
                $uploadInfo = $this->uploadImage("killer");
                if($uploadInfo){
                    $uploadConfig=C("UPLOAD_INFO");
                    $data['card_img']=$uploadConfig['uploadImage']['rootPath'].$uploadInfo['avatar']['savepath'].$uploadInfo['avatar']['savename'];
                }
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
    public function user() {
        $this->display(":killer:user");
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