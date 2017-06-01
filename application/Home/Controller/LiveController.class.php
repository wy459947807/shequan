<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class LiveController extends HomebaseController {

    //首页
    public function index() {
        
        $userInfo=sp_get_current_user();
        $userId=$userInfo?$userInfo['id']:0;
        
        $t_id = $article_id=I('get.t_id',1,'intval');
        $killers = M('killer');
        $tuser = $killers->where(array('id'=>$t_id))->find();
        $killers->where(['id'=>$t_id])->setInc('views');
        if(empty($tuser)) {
            $tuser = M('killer')->where(array('id'=>1))->find();
        }
        $this->assign('tuser',$tuser);

        $live_var = [];
        if(sp_is_user_login()){
            $user = sp_get_current_user();
            $live_var['name'] = $user['user_nicename'];
            $live_var['avatar'] = !empty($user['avatar']) ? $user['avatar'] : '/themes/frontend/Public/home/images/public_header/header_img.png';
        }else{
            $live_var['name'] = $this->genUserNumber()."(游客)";
            $live_var['avatar'] =  '/themes/frontend/Public/home/images/public_header/header_img.png';
        }
        
        $this->assign('userId',$userId);
        $this->assign('live_var',$live_var);

        $this->display(":live:index");
    }
    private function genUserNumber()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ( $i = 0; $i < 3; $i++ )
        {
            $username .= $chars[mt_rand(0, strlen($chars))];
        }
        return strtoupper(base_convert(time() - 1420070400, 10, 26)).$username;
    }

    public function getLogs()
    {
        $t_id = $article_id=I('post.t_id',1,'intval');
        if(empty($t_id)){
            $this->ajaxReturn(0,'房间号为空');
        }
        $logs = M('talks_log')->query("SELECT * FROM `tg_talks_log` WHERE  room_id = {$t_id} ORDER BY id DESC LIMIT 0,300");
        
        $data =[];
        if(!empty($logs)){
            foreach($logs as $log){
                $data[] = json_decode($log,true);
            }
        }else{
            $data = $logs;
        }

        $this->ajaxReturn(1,'ok',$logs);
    }
    
    
    
    //更新消息
    /*
    public function test(){
        $msgList = D('TalksLog')->select();
        foreach ($msgList as $key=>$val){
            $contentArray=json_decode($val['content'],true);
            
       
            $updateArray=array(
                "type"=>$contentArray['type'],
                "from_client_id"=>$contentArray['from_client_id'],
                "from_client_name"=>$contentArray['from_client_name'],
                "to_client_id"=>$contentArray['to_client_id'],
                "message"=>$contentArray['content'],
                "saytime"=>$contentArray['time'],
                "avatar"=>$contentArray['avatar'],
                "role"=>$contentArray['role'], 
            );
            
            D('TalksLog')->where(array("id"=>$val['id']))->save($updateArray);
        }
        
        var_dump($result);
    }*/
}