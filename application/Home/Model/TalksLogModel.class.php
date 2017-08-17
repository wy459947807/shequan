<?php
namespace Home\Model;

use Common\Model\CommonModel;

class TalksLogModel extends CommonModel {


    //获取消息列表
    public function getList($params){

        $this->sqlFrom=" tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1 "
                . " left join tg_users  as c on a.user_id=c.id "
                . " left join tg_killer as d on d.id=c.killer_id and d.status=1 ";
        
        $this->sqlField="a.*,b.subscribe,d.id as killer_id,e.is_read";                //数据库查询字段
        $this->sqlWhere=" (1=1) and b.status = 1 ";          //数据库查询条件
        $this->bindValues=array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

        $userInfo=array();
        if(!empty($params['uid'])){
            $this->sqlFrom.=" left join tg_user_talks_read as e on e.talk_id=a.id and e.user_id={$params['uid']} ";      //数据库查询表
            $userInfo= D("Home/Users")->where(array("id"=>$params['uid']))->find();//查询用户信息
        }else{
            $this->sqlFrom.=" left join tg_user_talks_read as e on e.talk_id=a.id and e.user_id=0 ";      //数据库查询表
        }

        $this->sqlOrder=" order by a.id desc "; 
        
        if(!empty($params['role'])){
            $this->sqlWhere.=" and  a.role = %d "; 
            $this->bindValues[] = $params['role'];
        }
        
        if(!empty($params['killer_id'])){
            $this->sqlWhere.=" and  a.room_id = %d "; 
            $this->bindValues[] = $params['killer_id'];
        }
        
        if(isset($params['is_charge'])){
            $this->sqlWhere.=" and  a.is_charge = %d "; 
            $this->bindValues[] = $params['is_charge'];
        }
        
        //消息类型
        if(isset($params['msg_type'])){
            $this->sqlWhere.=" and  a.msg_type = %d "; 
            $this->bindValues[] = $params['msg_type'];
        }
        
        //显示左右两边消息
        if(!empty($params['display_type'])&&!empty($params['killer_id'])){
            if($params['display_type']=="1"){
                $this->sqlWhere.=" and  c.killer_id = %d "; 
                $this->bindValues[] = $params['killer_id'];
            }else if($params['display_type']=="2"){
                $this->sqlWhere.=" and  c.killer_id != %d "; 
                $this->bindValues[] = $params['killer_id'];
            }
        }

        $listInfo=$this->getPageList();
        
        if($listInfo['data']){
            
            if(!empty($params['reverse'])){
               $listInfo['data']['list'] = array_reverse($listInfo['data']['list']);
            }
            
            foreach ($listInfo['data']['list'] as $key=>$val){
                $listInfo['data']['list'][$key]['subscribe']= unserialize($val['subscribe'])?unserialize($val['subscribe']):null;
                
                //设为已读
                if(!empty($params['uid'])){
                    $readArray=array(
                        "talk_id"=>$val['id'],
                        "user_id"=>$params['uid'],
                        "room_id"=>$val['room_id'],
                    );
                    $readInfo=D("Home/UserTalksRead")->where($readArray)->find();
                    if(empty($readInfo)){
                        if(!($val['is_charge']&&($userInfo['killer_id']!=$val['room_id']))){
                            $readArray['read_time']= date("Y-m-d H:i:s");
                            D("Home/UserTalksRead")->add($readArray);  
                        } 
                    } 
                }
                
            }
            
            
            //获取已读数量和未读数量
            $listInfo['data']['readInfo']['isRead']=0;
            $listInfo['data']['readInfo']['noRead']=$listInfo['data']['pageInfo']['num'];
            if(!empty($params['uid'])){
                $params['is_read']=1;
                $isRead=$this->getReadNum($params);
                $listInfo['data']['readInfo']['isRead']=$isRead['data']['num'];
                $listInfo['data']['readInfo']['noRead']=$listInfo['data']['pageInfo']['num']-$isRead['data']['num'];
            }
 
        }
        
        return $listInfo;
    }
    
    //获取已读未读数量
    public function getReadNum($params){
        
         $this->sqlFrom=" tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1"
                . " left join tg_user_talks_read as c on c.talk_id=a.id";      //数据库查询表
        $this->sqlField=" COUNT(*) as num";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
       
     
       
        $this->sqlWhere.=" and  c.is_read = %d "; 
        $this->bindValues[] = $params['is_read'];
        
        if(!empty($params['uid'])){
            $this->sqlWhere.=" and  c.user_id = %d "; 
            $this->bindValues[] = $params['uid'];
        }
        
        if(!empty($params['role'])){
            $this->sqlWhere.=" and  a.role = %d "; 
            $this->bindValues[] = $params['role'];
        }
        
        if(!empty($params['killer_id'])){
            $this->sqlWhere.=" and  a.room_id = %d "; 
            $this->bindValues[] = $params['killer_id'];
        }

        $listInfo=$this->getOne();
        
        return $listInfo;
    }
    
    //设为已读
    public function toRead($params){

        $this->sqlFrom=" tg_talks_log as a "
                . " left join tg_killer as b on a.room_id=b.id and b.status=1 ";      //数据库查询表
        $this->sqlField="a.*,b.subscribe";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();

        $this->sqlOrder=" order by a.id desc "; 
        
        if(!empty($params['role'])){
            $this->sqlWhere.=" and  a.role = %d "; 
            $this->bindValues[] = $params['role'];
        }
        
        if(!empty($params['killer_id'])){
            $this->sqlWhere.=" and  a.room_id = %d "; 
            $this->bindValues[] = $params['killer_id'];
        }
        
        $listInfo=$this->getAll();
        
        $ids=array();
        if($listInfo['data']){
            foreach ($listInfo['data'] as $key=>$val){
                if(!$val['is_read']){
                    $ids[]=$val['id'];
                }
            }
        }
        
        D("Home/TalksLog")->where(array('id' => array('in', $ids)))->save(array('is_read' => 1));
        
        $listInfo['data']=null;
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
            $this->result['data'] =array();
            return $this->result;
        }
        
        //如果是按条为单位订阅
        if($subscribeInfo['type']==0){
            if($subscribeInfo['num']<=$subscribeInfo['use_num']){
                $this->result['status'] = 202;
                $this->result['msg'] = "您订阅的条数已经用完！";
                $this->result['data'] =array();
                return $this->result;
            }
            D("Home/UserSubscribe")->where($subscribeArray)->setInc("use_num",1);//增加一条订阅记录
        }else{ 
            if(time()> strtotime($subscribeInfo['expire_time'])){
                $this->result['status'] = 203;
                $this->result['msg'] = "您的订阅已经过期请重新订阅！";
                $this->result['data'] =array();
                return $this->result;
            }
        }

        
        //设为已读
        if(!empty($params['uid'])){
            $userInfo= D("Home/Users")->where(array("id"=>$params['uid']))->find();//查询用户信息
            $readArray=array(
                "talk_id"=>$messageInfo['data']['id'],
                "user_id"=>$params['uid'],
                "room_id"=>$messageInfo['data']['room_id'],
            );

            $readInfo=D("Home/UserTalksRead")->where($readArray)->find();
            if(empty($readInfo)){
                $readArray['read_time']= date("Y-m-d H:i:s");
                D("Home/UserTalksRead")->add($readArray);  
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
        $dataInfo['data']['subscribe']=!empty($dataInfo['data']['subscribe'])?unserialize($dataInfo['data']['subscribe']):array();
       
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