<?php
namespace Tlive\Model;

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
       
 
        if(!sp_is_tuser_login()){
            $this->result['status']="500";
            $this->result['msg']="您还没有登录！";
            return $this->result;
        }

        $userInfo=sp_get_current_tuser();
        $this->sqlWhere.=" and a.killer_id = %d "; 
        $this->bindValues[] = $userInfo['id']; 
  
        //echo $this->sqlOrder;
        /*
        if(!empty($params['status'])){
            $this->sqlWhere.=" and  status = %d "; 
            $this->bindValues[] = $params['status'];
        }*/
        
        $listInfo=$this->getPageList();
        //$listInfo['data']['list']= D('killer')->listProcess($listInfo['data']['list']);
       
        return $listInfo;
    }





}