<?php

namespace Common\Controller;

use Common\Controller\AppframeController;

use Common\Lib\Qiniu\QiniuApi;
use Home\Lib\FileOpera;

class AppbaseController extends AppframeController {

    protected $model;//公用model
    protected $loginTime=6;//登录有效期(单位：小时)
    protected $params; //页面参数

    public function __construct() {
        parent::__construct();
         //跨域处理
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT');
    }

    function _initialize() {
        parent::_initialize();
        $this->model = M(); 
        $this->params=I('param.');//获取参数
        if(empty($this->params)){
            $this->params=$this->getParams();
        }
    }
   

    //获取登录Token
    /*
    protected function getToken(){
        return  md5(time().sp_random_string());
    }*/
    
    //获取APP参数
    protected function getParams(){
        $paramsArray = json_decode(file_get_contents('php://input'), TRUE);
        return $paramsArray;
    }
    
    //检测Token
    protected function checkToken($params){
        //验证规则
        $rules = array(
            array('uid','require','uid不得为空！',1,'regex',3),
            array('token','require','token不能为空！',1,'regex',3),
        );
        //字段验证
        if ($this->model->validate($rules)->create($params)===false){
            $this->ajaxReturn(500,$this->model->getError(),"");
        }

        $whereArray=array(
            "id"=>$params['uid'],
            "token"=>$params['token'],
        );
        
        $userModel = D("Common/Users");//加载用户model
        $userInfo= $userModel->where($whereArray)->find();//查询登录用户
        
        if(empty($userInfo)){
            $this->ajaxReturn(500,"用户未登录","");
        }
        
        $expireTime=strtotime('+'.$this->loginTime.' hour',strtotime($userInfo['last_login_time']));//过期时间
        
        if($expireTime< time()){
            $this->ajaxReturn(500,"用户登录超时,请重新登录！","");
        } 
        return $userInfo;
        
    }
    
    //验证高手登录
    protected function checkKiller($params){
        $userInfo = $this->checkToken($params);//检测登录
        if(empty($userInfo['killer_id'])){
            $this->ajaxReturn(500,"您不是高手用户!","");
        }
        $killerInfo=D("Common/Killer")->where(array("id"=>$userInfo['killer_id']))->find();
        if(empty($killerInfo['status'])){
            $this->ajaxReturn(500,"您还没通过审核!","");
        }
        return $killerInfo;
    }


    //字段验证
    protected function checkField($rules,$params) {
        if ($this->model->validate($rules)->create($params)===false){
            $this->ajaxReturn(500,$this->model->getError(),"");
        }
    }
    
    //云端上传代码处理
    protected function cloudHandle($params){

        $cloudApi=new QiniuApi(C("cloud_config"));  //初始化七牛云端
        $cloudInfo= $cloudApi->uploadFile($params);//上传文件到云端

        $fileOpera = new FileOpera();
        $fileOpera->LK_del($params['filePath']); //删除临时文件

        if($cloudInfo['status']==200){
            $cloudInfo['data']['url']=C("CLOUD_HOST").$cloudInfo['data']['key'];//定义文件云端路径
        }
    
        return $cloudInfo;
    }

    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
    protected function ajaxReturn($status = 1, $msg = '', $data = '') {
        $status=($status==200)?1:0;
        $data=!empty($data)?$data:null;
        parent::ajaxReturn(array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        ));
    }

    protected function uploadImage($savePath) {
        $uploadInfo = C("UPLOAD_INFO");
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = $uploadInfo['uploadImage']['maxSize']; // 设置附件上传大小
        $upload->exts = $uploadInfo['uploadImage']['exts'];    // 设置附件上传类型
        $upload->rootPath = $uploadInfo['uploadImage']['rootPath']; // 设置附件上传根目录
        $upload->savePath = '/'.$savePath."/"; // 设置附件上传（子）目录
        // 上传文件 
        $info = $upload->upload();
        return $info;
    }
    
    
    protected function uploadFile($savePath) {
        $uploadInfo = C("UPLOAD_INFO");
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 0; // 设置附件上传大小
        //$upload->exts = $uploadInfo['uploadFile']['exts'];    // 设置附件上传类型
        $upload->rootPath = $uploadInfo['uploadFile']['rootPath']; // 设置附件上传根目录
        $upload->savePath = '/' . $savePath; // 设置附件上传（子）目录
        // 上传文件 
        $info = $upload->upload();
        return $info;
    }
    

    //移动文件
    protected function moveFile($fileName) {
        if (!empty($fileName)) {
            $fileOpera = new FileOpera();
            $source = TMP_UPLOAD . "/" . $fileName;
            $dest = dirname(TMP_UPLOAD) . "/" . MODULE_NAME . "/" . CONTROLLER_NAME . "/" . $fileName;
            $fileOpera->LK_move($source, $dest); //移动文件
            $fileName = "/data/upload/" . MODULE_NAME . "/" . CONTROLLER_NAME . "/" . $fileName;
            return $fileName;
        }
    }

}
