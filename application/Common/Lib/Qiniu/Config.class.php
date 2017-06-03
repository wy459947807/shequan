<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Administrator
 */

namespace Common\Lib\Qiniu;

class Config {
    public static $cfg = array(
        'accessKey'=>'PaHFjIcqPOwdtn7J40KHP2xrIQGtHxuuddpAtGSk',    //accessKey
        'secretKey'=>'PCyz-cRkSJwFah0XY5RZzrGv9VO-MVJ4W-z6ck1_',    //secretKey   
        'bucket'=>"socialcircle-speech-bucket",                     //要上传的空间
        'videoNotify'=>"http://sheji.imwork.net/index.php/api/Cloud/videoNotify",//视频上传回调接口
        'pipeLine'=>'video-queue-4',  //视频上传通道
    ); 
}
