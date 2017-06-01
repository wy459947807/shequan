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
    
}
