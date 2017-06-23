<?php
namespace Home\Model;

use Common\Model\CommonModel;

class OrderModel extends CommonModel {


    public function submitOrder($params){
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "操作成功！";
        try {
            $orderArray=array(
                "user_id"=>$params['uid'],
                "order_sn"=>"S".date("Ymd").time(),//订单号
                "ctime"=>time(),
                "utime"=>time(),
            );
            
         
            $orderId = $model->table(C('DB_PREFIX') . 'order')->add($orderArray);
 
            $updateArray=array();
            $courseList = D("Common/Course")->courseList(array("course_ids"=>$params["course_ids"]));//获取课程列表
            foreach ($courseList['data']['list'] as $key=>$val){//插入订单购买课程列表
                $orderItem=array();
                $orderItem['course_id']=$val['id'];
                $orderItem['killer_id']=$val['killer_id'];
                $orderItem['user_id']=$params['uid'];
                $orderItem['order_id']=$orderId;
                $orderItem['order_sn']=$orderArray['order_sn'];
                $orderItem['num']=is_array($params["course_num"])?$params["course_num"][$key]:$params["course_num"];
                $orderItem['item_money']= round($orderItem['num']*$val['price'],2);
                $model->table(C('DB_PREFIX') . 'order_item')->add($orderItem);//更新订单项

                $updateArray['money']+=$orderItem['item_money'];//计算订单总额
                $updateArray['num']+=$orderItem['num'];  //计算订单购买数量
                $updateArray['order_name'].=$val['teacher_name'].",";
            }
            
            $updateArray['order_name']=substr($updateArray['order_name'], 0, -1)."社圈私密分享";
            $updateArray['minus_money']=0;//优惠金额
            $updateArray['total_money']=$updateArray['money']-$updateArray['minus_money'];//实付金额 
            $model->table(C('DB_PREFIX') . 'order')->where(array("id"=>$orderId))->save($updateArray);//更新订单
  
            $this->result['data'] = $orderArray;
            $model->commit(); //提交事物
        } catch (Exception $e) {
            $model->rollback(); //事物回滚

            $this->result['status'] = 500;
            $this->result['msg'] = "修改失败！";
            return $this->result;
        }
        return $this->result;
    }
    
    
    
    //订单支付成功回调接口
    public function callBack($params){
        $model = M();
        $model->startTrans();//事务处理
        try { 
            $orderInfo=D("Common/Order")->orderDetail($params);
            if($orderInfo['data']['status']!=1){
                $this->result['status'] = 500;
                $this->result['msg'] = "该订单已经支付！";
                return $this->result;
            }
            //更新订单状态
            $orderArray=array(
                "order_sn"=>$params['order_sn'],
                "status"=>2,//更新订单状态为已支付
                "utime"=> time(),
                "pay_time"=> time(),
            );
            $retInfo=D("Common/Order")->orderUpdate($orderArray);

            //更新用户赢家宝数量
            D("Common/Users")->where(array('id'=>$orderInfo['data']['user_id']))
                             ->setInc("win_coin",$orderInfo['data']['total_money']);
            
            $model->commit();//提交事务
         } catch (Exception $e) {
            $model->rollback();//事务回滚
            
            $this->result['status']=500;
            $this->result['msg']= "付款失败！"; 
            return $this->result;
        }       
        return $this->result;
 
    }
    
    

}