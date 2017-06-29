<?php
namespace Home\Model;

use Common\Model\CommonModel;

class TalksLogModel extends CommonModel {


    //获取消息列表
    public function getList($params){

        $this->sqlFrom=" tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1 ";      //数据库查询表
        $this->sqlField="a.*,b.subscribe";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

        $this->sqlOrder=" order by id desc "; 
        
        if(!empty($params['role'])){
            $this->sqlWhere.=" and  a.role = %d "; 
            $this->bindValues[] = $params['role'];
        }
        
        if(!empty($params['killer_id'])){
            $this->sqlWhere.=" and  a.room_id = %d "; 
            $this->bindValues[] = $params['killer_id'];
        }
        
        $listInfo=$this->getPageList();
        
        if($listInfo['data']){
            foreach ($listInfo['data']['list'] as $key=>$val){
                $listInfo['data']['list'][$key]['subscribe']= unserialize($val['subscribe'])?unserialize($val['subscribe']):null;
            }
        }
        
        return $listInfo;
    }
    
    //获取一条消息
    public function getMsgInfo($params){
        $this->sqlFrom="tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1 ";      //数据库查询表
        $this->sqlField="a.*,b.subscribe";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
        
        //$userInfo=D("Common/Users")->where(array("id"=>$params['uid']))->find();
        
        $messageInfo=$this->getDetail($params);//获取消息详情
        
        $subscribeArray=array(
            "user_id"=>$params['uid'],
            "killer_id"=>$messageInfo['data']['room_id'],
        );
        //判断是否已经订阅
        $subscribeInfo=D("Home/UserSubscribe")->where($subscribeArray)->find();

        if(empty($subscribeInfo)){
            $this->result['status'] = 201;
            $this->result['msg'] = "您还没有订阅该老师课程！";
            return $this->result;
        }
        
        //如果是按条为单位订阅
        
        if($subscribeInfo['type']===0){
            if($subscribeInfo['num']<=$subscribeInfo['use_num']){
                $this->result['status'] = 202;
                $this->result['msg'] = "您订阅的条数已经用完！";
                return $this->result;
            }
            D("Home/UserSubscribe")->where($subscribeArray)->setInc("use_num",1);//增加一条订阅记录
        }else{ 
            if(time()> strtotime($subscribeInfo['expire_time'])){
                $this->result['status'] = 203;
                $this->result['msg'] = "您的订阅已经过期请重新订阅！";
                return $this->result;
            }
        }

        return $messageInfo;
    }
    
    
     //获取一条消息
    public function getDetail($params){
        $this->sqlFrom="tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1 ";      //数据库查询表
        $this->sqlField="a.*,b.subscribe";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
      
        if(!empty($params['id'])){
            $this->sqlWhere.=" and  a.id = %d "; 
            $this->bindValues[] = $params['id'];
        }

        $dataInfo=$this->getOne();
        return $dataInfo;
    }
    
    
    //发送礼物消息
    public function sendGift($params){
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "发送成功！";
        $params['type']= empty($params['type'])?0:$params['type'];
        try {
            $giftInfo=D("Common/Gift")->where(array("id"=>$params['gift_id']))->find();//获取礼物详情
            $userInfo=D("Home/Users")->where(array("id"=>$params['uid']))->find();//获取用户信息
            if(empty($giftInfo)){
                $this->result['status'] = 500;
                $this->result['msg'] = "礼物不存在！";
                return $this->result;
            }

            if($userInfo["win_coin"]<$giftInfo['win_coin']){
                $this->result['status'] = 201;
                $this->result['msg'] = "您的赢家宝余额不足！";
                return $this->result;
            }
            
            $addArray=array(
                "killer_id"=>$params['killer_id'],
                "user_id"=>$params['uid'],
                "gift_id"=>$params['gift_id'],
                "ctime"=>time(),
            );
            
            $retInfo = $model->table(C('DB_PREFIX') . 'user_gift')->add($addArray);
            if($retInfo){
                //更新用户和高手的赢家宝
                $model->table(C('DB_PREFIX') . 'users')->where(array("id"=>$params['uid']))->setDec("win_coin",$giftInfo['win_coin']);
                $model->table(C('DB_PREFIX') . 'users')->where(array("killer_id"=>$params['killer_id']))->setInc("win_coin",$giftInfo['win_coin']); 
            }

            $model->commit(); //提交事物
        } catch (Exception $e) {
            $model->rollback(); //事物回滚
            $this->result['status'] = 500;
            $this->result['msg'] = "发送失败！";
            return $this->result;
        }
        return $this->result;
        
    }
    
    

    
}