<?php

namespace Common\Controller;

use Common\Controller\AppframeController;

use Common\Lib\Qiniu\QiniuApi;
use Home\Lib\FileOpera;

class ApibaseController extends AppframeController {

    protected $model;//公用model
    protected $loginTime=6;//登录有效期(单位：小时)
    protected $params; //页面参数

    public function __construct() {
        parent::__construct();
    }

    function _initialize() {
        parent::_initialize();
        $this->model = M(); 
        $this->params=I('param.');//获取参数
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
    
    
    //云端视频上传代码处理
    protected function cloudVideo($params){

        $cloudApi=new QiniuApi(C("cloud_config"));  //初始化七牛云端
        $cloudInfo= $cloudApi->uploadVideo($params);//上传文件到云端

        $fileOpera = new FileOpera();
        $fileOpera->LK_del($params['filePath']); //删除临时文件

        if($cloudInfo['status']==200){
            $cloudInfo['data']['url']=C("CLOUD_HOST").$cloudInfo['data']['key'];//定义文件云端路径
        }
    
        return $cloudInfo;
    }

    //云回调
    protected function cloudNotify($params){
        $cloudApi=new QiniuApi(C("cloud_config"));  //初始化七牛云端
        /*if($cloudApi->verifyCallback($params)){*/
        $retArray= json_decode($params['callbackBody'], true);
        if(empty($retArray)){
            return array("status" => 500, "msg" => "回调失败！","data" => "");
        }
        
        //file_put_contents('1.txt', json_encode($err));
        
        $notifyInfo= $cloudApi->deleteFile(array("fileName"=>$retArray['inputKey']));//删除云端文件 
        return $notifyInfo;
        //}
        //return array("status" => 500, "msg" => "安全校验失败！","data" => "");
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
