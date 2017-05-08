<?php
/**
 * 数据相关的配置文件
 */
return array(
    //高手擅长的类型
    'ADEPT_TYPE' =>array(1=>'股票',2=>'期货',3=>'外汇',4=>'外盘'),
    //资格证类型
    'CARD_TYPE' => array(1=>'一般从业资格证',2=>'证券投资咨询业务（分析师）',3=>'证券投资咨询业务（投资顾问）'),
    //高手标签
    'TAG' =>array(),
    'UPLOAD_INFO'=>array(
        "uploadImage"=>array(
            "maxSize"=>3145728,                             // 设置图片上传大小
            "exts"=>array('jpg', 'gif', 'png', 'jpeg'),     // 设置图片上传类型
            "rootPath"=>'data/upload/home',                 // 设置图片上传根目录
        )  
    ),

);