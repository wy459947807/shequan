<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of upload
 *
 * @author Administrator
 */

namespace Common\Lib\Qiniu;
require_once __DIR__ . '/autoload.php';//自动加载类库
use Qiniu\Auth;// 引入鉴权类
use Qiniu\Storage\UploadManager;// 引入上传类
use Common\Lib\Qiniu\Config;//引用配置文件
use Qiniu\Storage\BucketManager;

class QiniuApi {
    
    private $config;

    //put your code here
    public function __construct() {
       $this->config=Config::$cfg;
    }
    
    //文件上传
    public function uploadFile($params){
        
        $fields= array(
            "notNull"=>array("filePath"=>"文件路径不存在！","fileName"=>"文件名不存在！"),
        );
        
        $verifyInfo = $this->paramsVerify($params, $fields);
        if($verifyInfo['status']==500){
            return $verifyInfo;
        }
        
        $auth = new Auth($this->config['accessKey'], $this->config['secretKey']);// 构建鉴权对象
        $token = $auth->uploadToken($this->config['bucket']);// 生成上传 Token
        $uploadMgr = new UploadManager();// 初始化 UploadManager 对象并进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $params['fileName'], $params['filePath']);// 调用 UploadManager 的 putFile 方法进行文件的上传。
        if ($err !== null) {
            return $this->retData("500","上传失败！",$err);
        } 
        
        return $this->retData("200","上传成功！",$ret);
    }
    
    //视频上传
    public function uploadVideo($params){
        
        $fields= array(
            "notNull"=>array("filePath"=>"文件路径不存在！","fileName"=>"文件名不存在！"),
        );
        
        $verifyInfo = $this->paramsVerify($params, $fields);
        if($verifyInfo['status']==500){
            return $verifyInfo;
        }

        $ext = substr(strrchr($params['fileName'], '.'), 1);//获取文件后缀
        $fileName = basename($params['fileName'],".".$ext);//获取文件名（不带后缀）

        $auth = new Auth($this->config['accessKey'], $this->config['secretKey']);// 构建鉴权对象
        $token = $auth->uploadToken($this->config['bucket']);// 生成上传 Token
        $uploadMgr = new UploadManager();// 初始化 UploadManager 对象并进行文件的上传。
        
        $pfop = "avthumb/m3u8/noDomain/1/segtime/15/vb/240k";
        $saveas_key =  $this->base64_urlSafeEncode('socialcircle-speech-bucket:'.$fileName.'.m3u8');
        $pfop = $pfop.'|saveas/'.$saveas_key;
        
        $policy = array(
            'persistentOps' => $pfop,
            'persistentNotifyUrl' => $this->config['videoNotify'],
            //'callbackUrl' => $this->config['videoNotify'],
            //'callbackBody' => 'filename=$(fname)&filesize=$(fsize)',
            'persistentPipeline' => $this->config['pipeLine'],
        );

        $token = $auth->uploadToken($this->config['bucket'], null, 3600, $policy);
        
        list($ret, $err) = $uploadMgr->putFile($token, $params['fileName'], $params['filePath']);// 调用 UploadManager 的 putFile 方法进行文件的上传。
        if ($err !== null) {
            return $this->retData("500","上传失败！",$err);
        } 
        
        return $this->retData("200","上传成功！",$ret);
    }

    //删除文件
    public function deleteFile($params){
        $auth = new Auth($this->config['accessKey'], $this->config['secretKey']);// 构建鉴权对象
        //初始化BucketManager
        $bucketMgr = new BucketManager($auth);

        //删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($this->config['bucket'], $params['fileName']);
        
        if ($err !== null) {
            return $this->retData("500","删除失败！",$err);
        }   
        return $this->retData("200","删除成功！",$ret);
        
    }
    
    //验证回调是否合法
    public function verifyCallback($params){
        $auth = new Auth($this->config['accessKey'], $this->config['secretKey']);// 构建鉴权对象
        $callbackBody = $params['callbackBody'];
        $contentType = 'application/x-www-form-urlencoded';
        $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        $url = $this->config['videoNotify'];
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        return $isQiniuCallback;
    }

    //字段过滤
    public function paramsVerify($params,$fields){
        $errArray=array();  
        foreach ($fields as $key=>$val){
            switch ($key){
                case "notNull":
                    foreach ($val as $k=>$v){
                        if(empty($params[$k])){
                            return $this->retData("500",$val[$k],"");
                        }
                    }
                break;
            }
        }
    }


    //返回数据类型
    public function retData($status,$msg,$data){
        return array(
            "status" => $status, 
            "msg" => $msg,
            "data" => $data
        );
    }

    
    public function test(){
        
        $params=array(
            "filePath"=>"666",
            "fileName"=>"ddd",
            "aaa"=>"afd"
        );
        
        $fields= array(
            "notNull"=>array("filePath"=>"文件路径不存在！","fileName"=>"文件名不存在！","asdfa"=>"asdfafsd"),
        );
        
        $verifyInfo = $this->paramsVerify($params, $fields);
        if($verifyInfo['status']==500){
            return $verifyInfo;
        }
        
        //return $this->config;
    }
    
    
      
    protected function base64_urlSafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }



    
}
