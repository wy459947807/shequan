<?php

namespace Common\Model;

use Common\Model\CommonModel;

class CourseModel extends CommonModel {

    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '课程名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH),
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

    //课程列表
    public function courseList($params) {
        $uid=!empty($params['uid'])?$params['uid']:0;
        
        $this->sqlFrom = " tg_course as a "
                . "left join tg_killer as b on a.killer_id=b.id "
                . "left join tg_course_cate as c on a.cate_id=c.id "
                . "left join tg_order_item as d on d.course_id=a.id and d.user_id={$uid} "   
                . "left join tg_order as e on e.id=d.order_id ";   
        $this->sqlField = "a.*,b.real_name as teacher_name,b.avatar,c.name as cate_name,e.status as buy_status";                //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        if (!empty($params['page']))
            $this->page = $params['page'];
        if (!empty($params['pageLimit']))
            $this->pageLimit = $params['pageLimit'];

        $this->sqlWhere .= " and  a.status=0 ";
        if (!empty($params['start_time'])) {
            $this->sqlWhere .= " and  a.record_time > '%s' ";
            $this->bindValues[] = $params['start_time'];
        }

        if (!empty($params['end_time'])) {
            $this->sqlWhere .= " and  a.record_time < '%s' ";
            $this->bindValues[] = $params['end_time'];
        }
        
        if (!empty($params['killer_id'])) {
            $this->sqlWhere .= " and  a.killer_id = %d ";
            $this->bindValues[] = $params['killer_id'];
        }


        if (!empty($params['keyword'])) {
            $this->sqlWhere .= " and  (a.name like '%s' or b.real_name like '%s') ";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
            $this->bindValues[] = "%" . $params['keyword'] . "%";
        }
        
        if (!empty($params['course_ids'])) {
            $params['course_ids']= is_array($params['course_ids'])?implode(",",$params['course_ids']):$params['course_ids'];

            $this->sqlWhere .= " and  a.id in (%s) ";
            $this->bindValues[] = $params['course_ids'];
            
            //按字段排序
            $this->sqlOrder=" order by field(a.id,%s)";
            $this->bindValues[] = $params['course_ids']; 
        }

        //echo $this->sqlOrder;
        /*
          if(!empty($params['status'])){
          $this->sqlWhere.=" and  status = %d ";
          $this->bindValues[] = $params['status'];
          } */

        $listInfo = $this->getPageList();
        
        if(!empty($listInfo['data']['list'])){
            foreach($listInfo['data']['list'] as $key=>$val){
                $coverList=unserialize($val['cover']);
                $listInfo['data']['list'][$key]['cover']= !empty($coverList)?$coverList:array();
            }
        }
        return $listInfo;
    }
    
    public function courseDetail($params){
        
        $uid=!empty($params['uid'])?$params['uid']:0;
        
        $this->sqlFrom = " tg_course as a "
                . "left join tg_killer as b on a.killer_id=b.id "
                . "left join tg_course_cate as c on a.cate_id=c.id "
                . "left join tg_order_item as d on d.course_id=a.id and d.user_id={$uid} "    //数据库查询表
                . "left join tg_order as e on e.id=d.order_id ";  
        $this->sqlField = "a.*,b.real_name as teacher_name,b.avatar,c.name as cate_name,e.status as buy_status";                //数据库查询字段
        $this->sqlWhere = " (1=1) ";                        //数据库查询条件
        $this->bindValues = array();
        
        if (!empty($params['id'])) {
            $this->sqlWhere .= " and  a.id = '%d' ";
            $this->bindValues[] = $params['id'];
        }
        
        $dataInfo = $this->getOne(); 
        if(!empty($dataInfo['data'])){
            $dataInfo['data']['cover']= unserialize($dataInfo['data']['cover']);
            $model = M();
            $model->table(C('DB_PREFIX').'course')->where(array("id"=>$dataInfo['data']['id']))->setInc("views");
        }
        
        

        return $dataInfo;
        
    }



    public function courseUpdate($params) {
        $model = M();
        $model->startTrans(); //事务处理
        $this->result['msg'] = "修改成功！";
        try {

            if(!empty($params['id'])){
                $model->table(C('DB_PREFIX') . 'course')->where(array("id" => $params['id']))->save($params);
            }else{
                $params['ctime'] = time();
                $model->table(C('DB_PREFIX') . 'course')->add($params);
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
    
    
    
    

}
