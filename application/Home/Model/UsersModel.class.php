<?php
namespace Home\Model;
use Common\Model\CommonModel;


class UsersModel extends CommonModel{
    
    //获取用户详细信息
    public function getDetail($params){
        $this->sqlFrom = " tg_users as a"
                        ." left join tg_killer as b on a.killer_id=b.id ";                     //数据库查询表
        $this->sqlField = " a.*,b.status,b.fans,b.views,b.msgs,b.last_reply_time,b.subscribe ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();

        if(!empty($params['uid'])){
            $this->sqlWhere .= " and  a.id=%d ";
            $this->bindValues[] = $params['uid'];
        }

        $dataInfo = $this->getOne();
        
        if(!empty($dataInfo["data"])){
            $dataInfo["data"]['subscribe']= unserialize($dataInfo["data"]['subscribe'])?unserialize($dataInfo["data"]['subscribe']):null;//订阅标准
            //$dataInfo["data"]['mySubscribe']= D("Home/UserSubscribe")->where(array("user_id"=>$params['uid']))->select();//我的订阅
        }
        
        
        return $dataInfo;
    }
    
    //获取用户订阅信息
    public function userSubscribe($params){
        $this->sqlFrom = " tg_user_subscribe as a"
                        ." left join tg_killer as b on a.killer_id=b.id and b.status=1 "
                        ." left join tg_users as c on a.user_id=c.id  ";                     //数据库查询表
        $this->sqlField = " a.*,b.real_name as teacher_name,c.user_nicename as user_name ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

        
        if(!empty($params['uid'])){
            $this->sqlWhere .= " and  a.user_id=%d ";
            $this->bindValues[] = $params['uid'];
        }

        $dataInfo = $this->getPageList();
        $totalPrice=D("Home/UserSubscribe")->where(array("user_id"=>$params['uid']))->sum('price_total');
        $dataInfo['data']['totalPrice']=$totalPrice? $totalPrice:0;
        return $dataInfo;
    }
    
    //获取高手被订阅信息（已获得赢家宝记录）
    public function killerSubscribe($params){
        $this->sqlFrom = " tg_user_subscribe as a"
                        ." left join tg_killer as b on a.killer_id=b.id and b.status=1 "
                        ." left join tg_users as c on a.user_id=c.id  ";                     //数据库查询表
        $this->sqlField = " a.*,b.real_name as teacher_name,c.user_nicename as user_name ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];
        
 
        $userInfo=D("Home/Users")->where(array("id"=>$params['uid']))->find(); 
        $this->sqlWhere .= " and  a.killer_id=%d ";
        $this->bindValues[] = $userInfo['killer_id'];

        $dataInfo = $this->getPageList();
        $totalPrice=D("Home/UserSubscribe")->where(array("killer_id"=>$userInfo['killer_id']))->sum('price_total');
        $dataInfo['data']['totalPrice']=$totalPrice? $totalPrice:0;
        return $dataInfo;
    }
    
    
 
    //获取订单课程
    public function userCourse($params){
        $this->sqlFrom = " tg_order_item as a"
                        ." left join tg_order as b on a.order_id=b.id and b.status=2 "
                        ." left join tg_course as c on a.course_id=c.id "
                        ." left join tg_killer as d on c.killer_id=d.id ";                     //数据库查询表
        $this->sqlField = " a.*,b.order_name,b.status,b.pay_type,b.ctime,"
                        . "c.name as course_name,c.intro,c.record_time,c.price,c.hour,d.real_name as teacher_name ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];
        
        
        if(!empty($params['uid'])){
            $this->sqlWhere .= " and  b.user_id=%d ";
            $this->bindValues[] = $params['uid'];
        }
        
        
        
        if (!empty($params['start_time'])) {
            $this->sqlWhere .= " and  b.ctime > UNIX_TIMESTAMP('%s') ";
            $this->bindValues[] = $params['start_time'];
        }

        if (!empty($params['end_time'])) {
            $this->sqlWhere .= " and  b.ctime < UNIX_TIMESTAMP('%s') ";
            $this->bindValues[] = $params['end_time'];
        }
        
        if (!empty($params['keyword'])) {
            $this->sqlWhere .= " and  (c.name like '%s' or c.intro like '%s') ";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
        }
        
        
        
        $dataInfo = $this->getPageList();
        $dataAll = $this-> getAll();
        
        
        if(!empty($dataInfo['data'])){
            foreach ($dataInfo['data']['list'] as $key=>$val){
                $dataInfo['data']['list'][$key]['win_coin']= $val['item_money'];
            }
        }
        
        
        $totalPrice=0;
        if(!empty($dataAll['data'])){
            foreach ($dataAll['data'] as $key=>$val){
                $totalPrice+=$val['item_money'];
            }
        }

        /*$totalPrice=D("Home/OrderItem")
                ->join("tg_order ON tg_order_item.order_id = tg_order.id","LEFT")
                ->where(array("tg_order.user_id"=>$params['uid'],"tg_order.status"=>array("eq",2)))
                ->sum('tg_order_item.item_money');*/
        
        $dataInfo['data']['totalPrice']= $totalPrice? $totalPrice:0;
        
        return $dataInfo;
        
    }
    
    

    //获取我提交的高手认证信息
    public function killerInfo($params){
        $this->sqlFrom = " tg_users as a"
                        ." left join tg_killer as b on a.killer_id=b.id ";                     //数据库查询表
        $this->sqlField = " b.* ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();

        if(!empty($params['uid'])){
            $this->sqlWhere .= " and  a.id=%d ";
            $this->bindValues[] = $params['uid'];
        }

        $dataInfo = $this->getOne();
        
        if(!empty($dataInfo["data"])){
            $dataInfo["data"]['subscribe']= unserialize($dataInfo["data"]['subscribe'])?unserialize($dataInfo["data"]['subscribe']):null;//订阅标准
            $dataInfo["data"]['cert_imgs']= unserialize($dataInfo["data"]['cert_imgs'])?unserialize($dataInfo["data"]['cert_imgs']):null;//证件照
        }

        return $dataInfo;
    }


    //设置订阅规则
    public function setSubscribe($params){
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "设置成功！";
        $params['type']= empty($params['type'])?0:$params['type'];
        try {
            
            $userInfo=D("Common/Users")->where(array("id"=>$params['uid']))->find();
            $updateArray=array(
                "subscribe"=> serialize($params['price_array']),
            );
            D("Common/Killer")->where(array("id"=>$userInfo['killer_id']))->save($updateArray);
 
            $model->commit(); //提交事物
        } catch (Exception $e) {
            $model->rollback(); //事物回滚

            $this->result['status'] = 500;
            $this->result['msg'] = "设置失败！";
            return $this->result;
        }
        return $this->result;
    }
    
    public function killerGift($params){
        $this->sqlFrom = " tg_user_gift as a"
                        ." left join tg_killer as b on a.killer_id=b.id and b.status=1"
                        ." left join tg_users as c on a.user_id=c.id"
                        ." left join tg_gift as d on a.gift_id=d.id ";                     //数据库查询表
        $this->sqlField = " a.*,b.real_name as teacher_name,c.user_nicename as user_name,d.name,d.cname,d.win_coin,d.img ";   //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

        $userInfo=D("Home/Users")->where(array("id"=>$params['uid']))->find();
        $this->sqlWhere .= " and  a.killer_id=%d ";
        $this->bindValues[] = $userInfo['killer_id'];

        $dataInfo = $this->getPageList();

        $model = M();
        $totalPrice=$model->table("tg_user_gift as a")
                ->join("tg_gift as b on a.gift_id=b.id ","LEFT")
                ->where(array("a.killer_id"=>$userInfo['killer_id']))
                ->sum('b.win_coin');
        $dataInfo['data']['totalPrice']= $totalPrice? $totalPrice:0;
        return $dataInfo;
        
    }
    

    
}

