<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PayBack
 *
 * @author Administrator
 */

require_once 'mysql-master/mysql-master/src/Connection.php';

class PayBack {
    
    private $_DB = '';
    public function __construct() {
        $this->_DB = new Workerman\MySQL\Connection('127.0.0.1', '3306', 'root', 'chenyaadmiN8888', 'tougu');
    }
    public function payback($retArray) {

        $db = $this->_DB ; 
        $db->beginTrans();
        try {
            $order_sn = trim($retArray['attach']);
            $total_fee = $retArray['total_fee'];

            $orderInfo=$db->select("*")->from('tg_order')->where("order_sn='{$order_sn}'")->row();//获取回复信息

            if(empty($orderInfo)) exit("failure");
            if($orderInfo['total_money']*100!=$total_fee) exit("failure");
            if($orderInfo['status']!=1) exit("success");

            
            $orderInfo['pay_type']=$retArray['pay_type'];//支付类型
            $orderArray=array(
                "status"=>2,//更新订单状态为已支付
                "pay_type"=> $orderInfo['pay_type'],
                "utime"=> time(),
                "pay_time"=> time(),
            );

            $db->update('tg_order')->where("order_sn='{$order_sn}'")->cols($orderArray)->query();//更新订单状态

            //更新用户赢家宝数量
            $userInfo =$db->select("*")->from('tg_users')->where("id=".$orderInfo['user_id'])->row();//获取用户信息
            $userArray=array(
                "win_coin"=>$userInfo['win_coin']+$orderInfo['total_money']
            ); 
            $db->update('tg_users')->where('id='.$orderInfo['user_id'])->cols($userArray)->query();//更新聊天记录

            $db->commitTrans();

        } catch (Exception $e) {}                        

        exit("success");
        
    }

}
/*
$PayBack= new PayBack();
$PayBack->payback(array("attach"=>"S201707201500539877","total_fee"=>0.01));*/

