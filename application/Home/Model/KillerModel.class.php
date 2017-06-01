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
  
        
        if(!empty($params['status'])){
            $this->sqlWhere.=" and  status = %d "; 
            $this->bindValues[] = $params['status'];
        }
        
        $listInfo=$this->getPageList();
        //$listInfo['data']['list']=$this->listProcess($listInfo['data']['list']);
       
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
        
        $listInfo= $this->getAll();
        
        //$listInfo['data']=$this->listProcess($listInfo['data']);
        return $listInfo;
    }
    
    //列表数据处理
    public function listProcess($listInfo,$userId){
        //数据处理
        if(!empty($listInfo)){
            $ids=array();//id数组
            $adeptType = C('ADEPT_TYPE');
            $rankList= json_decode(S('rankList'),true) ;//排名缓存
            
           
            foreach ($listInfo as $key=>$val){
                $adeptArray= array_filter(explode('|',$val['adept_type']));
                foreach ($adeptArray as $k=>$v){
                    if(isset($adeptType[$v])){
                       $val['adept_str'].=$adeptType[$v]."/";
                    }
                }
                $val['live_num']=ceil((strtotime($val['last_login_time'])-$val['ctime'])/(24*60*60));//直播数
                $listInfo[$key]['adept_str']= substr($val['adept_str'], 0, -1);//擅长领域
                $listInfo[$key]['live_num']=$val['live_num']>0 ? $val['live_num']:0;  
                $listInfo[$key]['is_focused']=false;
                
                //计算排名变动
                $listInfo[$key]['rankChange']=0;
                
   
                if(isset($rankList[$val['adept_type']][$val['id']])){ 
                    
                    $listInfo[$key]['rankChange']=$rankList[$val['adept_type']][$val['id']]-$key;
                }
  
                $ids[]=$val['id'];
            }
            
            
 
            //判断是否被关注
            $focusArray=array();
            if(!empty($userId)){
                $focusList= M("KillerFans")->where(array("killer_id"=>array('in',$ids)))->select();
                foreach ($focusList as $key=>$val){
                    $focusArray[$val['killer_id']."_".$userId]=true;
                }
                foreach ($listInfo as $key=>$val){
                    if(isset($focusArray[$val['id']."_".$userId])){
                        $listInfo[$key]['is_focused']=true;
                    }
                }
            }
            
            
  
 
        }

        return $listInfo;
    }


    //高手注册
    public function killerRegist($params){
        
        $userInfo=D("Home/Users")->where(array("id"=>$params['uid']))->find();//获取用户信息
        $killerInfo=D("Home/Killer")->where(array("id"=>$userInfo['killer_id']))->find();      
        $userMobile=!empty($killerInfo)?$killerInfo['mobile']:"";
        //验证手机号码是否存在
        $checkInfo= D("Killer")->where(array("mobile"=>array(array('eq',$data['mobile']),array('neq',$userMobile),'and')))->find();
        if(!empty($checkInfo)){
            $this->result['status']=201;
            $this->result['msg']="该手机号码已经注册，请更换手机后再进行注册！";
            return $this->result;
        }
        

        $model = M();
        $model->startTrans();//事务处理
      
        $updateArray=array(
            "real_name"=>$params['real_name'],
            "avatar"=>$params['avatar'],
            "mobile"=>$params['mobile'],
            "wechat"=>$params['wechat'],
            "email"=>$params['email'],
            "adept_type"=>$params['adept_type'],
            "tag"=>$params['tag'],
            "card_img"=>$params['card_img'],
            "cert_imgs"=>serialize($params['cert_imgs']),
            "intro"=>$params['intro'],
            "type"=> intval($params['type']),
            "company"=>$params['company'],
            "ctime"=>time(),
            "status"=>0,
            "last_login_ip"=>get_client_ip(0, true),
            "last_login_time"=>date("Y-m-d H:i:s", time()),
        );
        
        if(empty($params['id'])){
            $retInfo=$id=$model->table(C('DB_PREFIX').'killer')->add($updateArray); 
        }else{
            $id=$params['id'];
            $retInfo=$model->table(C('DB_PREFIX').'killer')->where(array("id"=>$params['id']))->save($updateArray); 
        }
        
        if($retInfo){

            $model->table(C('DB_PREFIX').'users')->where(array("id"=>$params['uid']))->save(array("killer_id"=>$id));
            
            $model->commit();
            $this->result['msg']= "提交成功！";
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
    
    
    
    //更新高手排名
    public function updateRank(){
        /*S('gupiaoRankList',null);
        S('qihuoRankList',null);
        S('waihuiRankList',null);*/
        //S('rankList',null);
        
        $adeptType= C('ADEPT_TYPE');
        $rankList= json_decode(S('rankList'),true);//排名缓存
        foreach($adeptType as $key=>$val){
            //更新排名
            if(empty($rankList[$key])){
                $topArray=array();
                $topList = $this->getTopList(array("orderType" => 2, "adeptType" => $key,"num"=>300)); //高手榜
                
                if(!empty($topList['data'])){
                    foreach ($topList['data'] as $k=>$v){
                        $topArray[$v['id']]=$k;
                    }
                }
                $rankList[$key] = $topArray;
               
            }
        }
        S('rankList',json_encode($rankList),array('type'=>'file','expire'=>(3600*24)*3)); //保存期限为3天
    }
    
}