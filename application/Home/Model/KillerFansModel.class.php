<?php
namespace Home\Model;

use Common\Model\CommonModel;

class KillerFansModel extends CommonModel {

     //我的粉丝列表
    public  function fansList($params){
        $this->sqlFrom=" tg_killer_fans as a left join tg_users as b on a.users_id=b.id ";    //数据库查询表
        $this->sqlField="b.*";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];
  
        if(!empty($params['uid'])){
            $userInfo=D("Home/Users")->where(array("id"=>$params['uid']))->find(); 
            $this->sqlWhere.=" and a.killer_id = %d "; 
            $this->bindValues[] = $userInfo['killer_id']; 
        }
        
 
        $listInfo=$this->getPageList();
        return $listInfo;
    }


    //我的关注列表
    public function focusList($params){

        $this->sqlFrom=" tg_killer_fans as a left join tg_killer as b on a.killer_id=b.id ";    //数据库查询表
        $this->sqlField="b.*";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];

        if(!empty($params['uid'])){
            $this->sqlWhere.=" and a.users_id = %d "; 
            $this->bindValues[] = $params['uid']; 
        }
        
        $listInfo=$this->getPageList();
        //$listInfo['data']['list']= D('killer')->listProcess($listInfo['data']['list']);
       
        return $listInfo;
    }
    

    //关注高手
    public function focusKiller($params){
        $model = M();
        $model->startTrans();//事务处理
        $this->result['msg']= "关注成功！";
        try { 
            $focusInfo = $model->table(C('DB_PREFIX').'killer_fans')->where(array("killer_id"=>$params['killer_id'],"users_id"=>$params['users_id']))->find();
            if(!empty($focusInfo)){
                $model->table(C('DB_PREFIX').'killer_fans')->where(array("id"=>$focusInfo['id']))->delete();
                $model->table(C('DB_PREFIX').'killer')->where(array("id"=>$params['killer_id']))->setDec("fans");
                $this->result['msg']= "已取消关注！"; 
            }else{
                $params['ctime']=date("Y-m-d H:i:s");
                $model->table(C('DB_PREFIX').'killer_fans')->add($params); 
                $model->table(C('DB_PREFIX').'killer')->where(array("id"=>$params['killer_id']))->setInc("fans");
            }
            $model->commit();//提交事物
        } catch (Exception $e) {
            $model->rollback();//事物回滚
            
            $this->result['status']=500;
            $this->result['msg']= "关注失败！"; 
            return $this->result;
        }       
         return $this->result;       
    }
}