<?php
namespace Home\Model;
use Common\Model\CommonModel;


class UserSubscribeModel extends CommonModel{
    
    //用户订阅操作
    public function add($params) {
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "订阅成功！";
        $params['type']= empty($params['type'])?0:$params['type'];
        try {

            $killerInfo=D("Common/Killer")->where(array("id"=>$params['killer_id']))->find();

            $subscribe= unserialize($killerInfo['subscribe']);

            $subscribeType=C("subscribe_type");
            $unit = $subscribeType[$params['type']][1];
            $val =  $subscribeType[$params['type']][2]*$params['num'];
            
            $addArray=array(
                "user_id"=>$params['uid'],
                "killer_id"=>$params['killer_id'],
                "type"=>$params['type'],
                "num"=>$params['num'],
                "price_item"=>$subscribe[$params['type']],
                "price_total"=>intval($params['num']*$subscribe[$params['type']]),
                "expire_time"=>date("Y-m-d H:i:s",strtotime(" +".$val." ".$unit." ")),
                "ctime"=>time(),
            );
            
            
            $userInfo = $model->table(C('DB_PREFIX') . 'users')->where(array("id"=>$params['uid']))->find();
            
            if($userInfo['killer_id']==$params['killer_id']){
                $this->result['status'] = 201;
                $this->result['msg'] = "您不能订阅自己！";
                return $this->result; 
            }
            
            //判断用户赢家宝数量是否充足
            if($userInfo['win_coin']<$addArray['price_total']){
                $this->result['status'] = 202;
                $this->result['msg'] = "赢家宝余额不足！";
                return $this->result;
            }
            
            
            $subscribeArray=array(
                "user_id"=>$params['uid'],
                "killer_id"=>$params['killer_id']
            );
            //判断是否已经订阅
            $subscribeInfo=$model->table(C('DB_PREFIX') . 'user_subscribe')->where($subscribeArray)->find();
            $retInfo=array();
            if(!empty($subscribeInfo)){
                $retInfo = $model->table(C('DB_PREFIX') . 'user_subscribe')->where($subscribeArray)->save($addArray);//添加订阅记录
            }else{
                $retInfo = $model->table(C('DB_PREFIX') . 'user_subscribe')->add($addArray);//添加订阅记录
            } 
            
            //更新用户赢家宝数量
            if($retInfo){
                $model->table(C('DB_PREFIX') . 'users')->where(array("id"=>$params['uid']))->setDec("win_coin",$addArray['price_total']);
                $model->table(C('DB_PREFIX') . 'users')->where(array("killer_id"=>$params['killer_id']))->setInc("win_coin",$addArray['price_total']);
            }
 
            $model->commit(); //提交事物
        } catch (Exception $e) {
            $model->rollback(); //事物回滚

            $this->result['status'] = 500;
            $this->result['msg'] = "订阅失败！";
            return $this->result;
        }
        return $this->result;
    }


}

