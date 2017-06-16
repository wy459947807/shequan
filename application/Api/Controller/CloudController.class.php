<?php

namespace Api\Controller;

use Common\Controller\ApibaseController;
use Common\Lib\Qiniu\QiniuApi;
use Home\Lib\FileOpera;
/**
 * 云服务器操作
 */
class CloudController extends ApibaseController {
   
    public function index(){
        
        //echo phpinfo();
        
        $fileName="http://oqhjh5opr.bkt.clouddn.com.flv/593231afa831f.flv";
        
        $ext = substr(strrchr($fileName, '.'), 1);
        $result = basename($fileName,".".$ext);
        
        var_dump(C("cloud_config"));
        echo substr($fileName,0,strrpos($fileName,'.'));
    }


    //上传文件到云端
    public function uploadCloudVideo(){

        $fileInfo= $this->uploadFile();
        //var_dump($fileInfo);
        if(empty($fileInfo)){
            $this->ajaxReturn(500,"文件上传失败1！","");
        }
        
        $retInfo=array();
        foreach($fileInfo as $key=>$val){

            $cloudArray=array(
                "filePath"=>TMP_UPLOAD.$val['savepath'].$val['savename'],
                "fileName"=>$val['savename']
            );

            //var_dump($cloudArray);
            $cloudInfo =  $this->cloudVideo($cloudArray);
            if($cloudInfo['status']==500){
                $this->ajaxReturn($cloudInfo['status'],$cloudInfo['msg'],$cloudInfo['data']);
            }
            $retInfo[$key]=$cloudInfo['data'];
        }
        
        $this->ajaxReturn(200,"上传成功！",$retInfo);
    }
    
    //文件上传成功回调接口
    public function videoNotify(){
        $notifyBody = file_get_contents('php://input');
        $retInfo =  $this->cloudNotify(array("callbackBody"=>$notifyBody));
        
        file_put_contents('/data/upload/tmp/2.txt', json_encode($_SERVER));
        
        $this->ajaxReturn($retInfo['status'],$retInfo['msg'],$retInfo['data']);
    }
    
    
    //上传文件到云端
    public function uploadCloud(){

        $fileInfo= $this->uploadFile();
        //var_dump($fileInfo);
        if(empty($fileInfo)){
            $this->ajaxReturn(500,"文件上传失败1！","");
        }
        
        $retInfo=array();
        foreach($fileInfo as $key=>$val){

            $cloudArray=array(
                "filePath"=>TMP_UPLOAD.$val['savepath'].$val['savename'],
                "fileName"=>$val['savename']
            );

            //var_dump($cloudArray);
            $cloudInfo =  $this->cloudHandle($cloudArray);
            if($cloudInfo['status']==500){
                $this->ajaxReturn($cloudInfo['status'],$cloudInfo['msg'],$cloudInfo['data']);
            }
            $retInfo[$key]=$cloudInfo['data'];
        }
        
        $this->ajaxReturn(200,"上传成功！",$retInfo);
    }
    

}
