<?php
namespace Home\Model;

use Common\Model\CommonModel;

class KillerModel extends CommonModel {


    //获取高手列表
    public function getList($params){

        $this->sqlFrom="tg_killer";         //数据库查询表
        $this->sqlField="*";                //数据库查询字段
        $this->sqlWhere=" (1=1) ";          //数据库查询条件
        $this->bindValues=array();
        if(!empty($params['page'])) $this->page = $params['page'];
        if(!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];
       
        //条件筛选
        if(!empty($params['orderType'])){
            switch ($params['orderType']){
                case 1:
                    $this->sqlOrder=" ORDER BY last_login_time DESC ";
                    break;
                case 2:
                    $this->sqlOrder=" ORDER BY fans DESC ";
                    break;
            }
        }
        
        //echo $this->sqlOrder;
        
        if(!empty($params['status'])){
            $this->sqlWhere.=" and  status = %d "; 
            $this->bindValues[] = $params['status'];
        }
        
        $listInfo=$this->getPageList();
        
        //数据处理
        if(!empty($listInfo['data']['list'])){
            $adeptType = C('ADEPT_TYPE');
            foreach ($listInfo['data']['list'] as $key=>$val){
                $adeptArray= array_filter(explode('|',$val['adept_type']));
                foreach ($adeptArray as $k=>$v){
                    if(isset($adeptType[$v])){
                       $val['adept_str'].=$adeptType[$v]."/";
                    }
                }
                $val['live_num']=ceil((strtotime($val['last_login_time'])-$val['ctime'])/(24*60*60));//直播数
                $listInfo['data']['list'][$key]['adept_str']= substr($val['adept_str'], 0, -1);
                $listInfo['data']['list'][$key]['live_num']=$val['live_num']>0 ? $val['live_num']:0;
            }
        }      
        return $listInfo;
    }
    
    //获取高手榜单
    public function getTopList($params){
        $this->sqlFrom="tg_killer";                         //数据库查询表
        $this->sqlField="*";                                //数据库查询字段
        $this->sqlWhere=" (1=1) and  status = 1 ";          //数据库查询条件
        $this->sqlLimit="  limit 0,5  ";                    //限制条数
        $this->bindValues=array();
        if(!empty($params['num'])){
            $this->sqlLimit="   limit 0,{$params['num']}    ";  
        }
        
        if(!empty($params['orderType'])){
            switch ($params['orderType']){
                case 1:
                    $this->sqlOrder=" ORDER BY msgs DESC ";
                    break;
                case 2:
                    $this->sqlOrder=" ORDER BY fans DESC ";
                    break;
            }
        }
        
        //擅长领域
        if(!empty($params['adeptType'])){
            $this->sqlWhere.=" and  adept_type = %d "; 
            $this->bindValues[] = $params['adeptType'];
        }
        
     
        if(!empty($params['dateTime'])){
            $this->sqlWhere.=" and  to_days(last_reply_time) = to_days('%s') "; 
            $this->bindValues[] = $params['dateTime'];
        }
        
        return $this->getAll();

    }
    
    //高手注册
    public function killerRegist($params){
        $model = M();
        $model->startTrans();//事务处理
        $id=$model->table(C('DB_PREFIX').'killer')->add($params); 
        if($id){
            $model->commit();
            $this->result['msg']= "注册成功！";
            return $this->result;
        }else{
            $model->rollback();//事物回滚
            $this->result= array(
                "status" => 500, 
                "msg" => "数据插入失败！",
                "data" => ""
            );
            return $this->result;
        }
    }
    

}