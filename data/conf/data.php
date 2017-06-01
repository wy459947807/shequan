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
            "maxSize"=>3145728,                             // 设置图片上传大小3M
            "exts"=>array('jpg', 'gif', 'png', 'jpeg'),     // 设置图片上传类型
            "rootPath"=>'data/upload/home',                 // 设置图片上传根目录
        ),
        "uploadFile"=>array(
            "maxSize"=>104857600,                             // 设置附件上传大小100M
            "exts"=>array('jpg', 'gif', 'png', 'jpeg'),                             // 设置文件上传类型
            "rootPath"=>'data/upload/tmp',                 // 设置文件上传根目录
        ),
    ),
    'order_status'=>array(1=>"未支付",2=>"已支付",3=>"已取消"),
    'platform'=>array(1=>"电脑网页(web)",2=>"手机网站(wap)",3=>"手机客户端(app)"),
    'pay_type'=>array(1=>"未支付",2=>"支付宝",3=>"微信",4=>"网上银行"),
    'subscribe_type'=>array(
        0=>array("条","",1),
        1=>array("天","day",1),
        2=>array("周","week",1),
        3=>array("月","month",1),
        4=>array("季","month",3),
        5=>array("年","year",1),
        
    ),
);