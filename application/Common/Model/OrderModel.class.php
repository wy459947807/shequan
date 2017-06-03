<?php

namespace Common\Model;

use Common\Model\CommonModel;

class OrderModel extends CommonModel {

    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        //array('name', 'require', '课程名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH),
    );
    
    protected $_auto = array(
        array('ctime', 'mGetDate', CommonModel:: MODEL_INSERT, 'callback'),
       
    );

    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return time();
    }
    

    protected function _before_write(&$data) {
        parent::_before_write($data);
    }

    //订单列表
    public function orderList($params) {

        $this->sqlFrom = " tg_order as a left join tg_users as b on a.user_id=b.id ";    //数据库查询表
        $this->sqlField = " a.*,b.user_nicename,b.user_email,b.mobile,b.user_url,b.avatar,b.sex,b.signature";                //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        if (!empty($params['page'])) $this->page = $params['page'];
        if (!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

      
        if(!empty($params['status'])){//订单状态筛选
            $this->sqlWhere .= " and  a.status=%d ";
            $this->bindValues[] = $params['status'];
        }
        
        if (!empty($params['start_time'])) {
            $this->sqlWhere .= " and  a.ctime > UNIX_TIMESTAMP('%s') ";
            $this->bindValues[] = $params['start_time'];
        }

        if (!empty($params['end_time'])) {
            $this->sqlWhere .= " and  a.ctime < UNIX_TIMESTAMP('%s') ";
            $this->bindValues[] = $params['end_time'];
        }
        
        if(!empty($params['uid'])){//订单状态筛选
            $this->sqlWhere .= " and  a.user_id=%d ";
            $this->bindValues[] = $params['uid'];
        }


        if (!empty($params['keyword'])) {
            $this->sqlWhere .= " and  (a.order_sn like '%s' or b.user_nicename like '%s' or b.mobile like '%s') ";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
        }

        //echo $this->sqlOrder;
        /*
          if(!empty($params['status'])){
          $this->sqlWhere.=" and  status = %d ";
          $this->bindValues[] = $params['status'];
          } */

        $listInfo = $this->getPageList();
        return $listInfo;
    }

    public function orderUpdate($params) {
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "修改成功！";
        try {

            if(!empty($params['id'])){
                $model->table(C('DB_PREFIX') . 'order')->where(array("id" => $params['id']))->save($params);
            }elseif(!empty($params['order_sn'])){
                $model->table(C('DB_PREFIX') . 'order')->where(array("order_sn" => $params['order_sn']))->save($params);
            }else{
                $params['ctime'] = time();
                $model->table(C('DB_PREFIX') . 'order')->add($params);
            }
             
            $model->commit(); //提交事物
        } catch (Exception $e) {
            $model->rollback(); //事物回滚

            $this->result['status'] = 500;
            $this->result['msg'] = "修改失败！";
            return $this->result;
        }
        return $this->result;
    }
    
    public function orderDetail($params) {
        $this->sqlFrom = " tg_order as a left join tg_users as b on a.user_id=b.id ";    //数据库查询表
        $this->sqlField = " a.*,b.user_nicename,b.user_email,b.mobile,b.user_url,b.avatar,b.sex,b.signature";                //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
    
      
        if(!empty($params['id'])){
            $this->sqlWhere .= " and  a.id=%d ";
            $this->bindValues[] = $params['id'];
        }
        
        if(!empty($params['order_sn'])){
            $this->sqlWhere .= " and  a.order_sn='%s' ";
            $this->bindValues[] = $params['order_sn'];
        }
        
        $dataInfo = $this->getOne();
        
        if(empty($dataInfo['data'])){
            $this->result['status'] = 500;
            $this->result['msg'] = "订单不存在！";
            return $this->result;
        }
        
        $itemList =  $this->itemList(array("order_id"=>$dataInfo['data']['id']));
        $dataInfo['data']['itemList']=$itemList['data'];
        return $dataInfo;
    }
    
    public function itemList($params){
        $this->sqlFrom = " tg_order_item as a "
                        . "left join tg_course as b on a.course_id=b.id "
                        . "left join tg_killer as c on b.killer_id=c.id ";    
        $this->sqlField = " a.*,"
                          . "b.name as course_name,b.intro,b.hour,b.price,b.record_time,b.cover,"
                          . "c.real_name as teacher_name,c.company as organization ";                //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        
        if(!empty($params['id'])){
            $this->sqlWhere .= " and  a.id=%d ";
            $this->bindValues[] = $params['id'];
        }
        
        if(!empty($params['order_id'])){
            $this->sqlWhere .= " and  a.order_id=%d ";
            $this->bindValues[] = $params['order_id'];
        }
  
        $dataInfo = $this->getAll();
        if(!empty($dataInfo['data'])){
            foreach($dataInfo['data'] as $key=>$val){
                $dataInfo['data'][$key]['cover']= unserialize($val['cover']);
            }
        }
        
        return $dataInfo;

    }

}
