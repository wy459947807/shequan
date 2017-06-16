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

namespace Common\Lib\Jpush;
require_once __DIR__ . '/autoload.php';//自动加载类库
use JPush\Client as JPush;

class JpushApi {
    
    private $config;
    private $client;

    //put your code here
    public function __construct($config) {
       $this->config=$config;
       if(!empty($config)){
            $this->client = new JPush($config['app_key'], $config['master_secret']);
        }
    }
    
    //文件上传
    public function sendMessage($params){ 
        $fields= array(
            "notNull"=>array("message"=>"消息不能为空！"),
        );
        
        $verifyInfo = $this->paramsVerify($params, $fields);
        if($verifyInfo['status']==500){
            return $verifyInfo;
        }
        
        $params['alias']=!empty($params['alias'])?$params['alias']:"";
        
        $push_payload = $client->push()
            ->setPlatform('all')
            ->addAlias($params['alias'])
            ->addAllAudience()
            ->setNotificationAlert($params['message']);

        try {
            $response = $push_payload->send();
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            return $this->retData(500,"推送服务创建失败！",$e);
        } catch (\JPush\Exceptions\APIRequestException $e) { 
            return $this->retData(500,"推送服务内部错误！",$e);
        } 
        return $this->retData(200,"推送成功！","");

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
