<?php

namespace Home\Controller;

use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {

    //首页
    public function index() {

        //echo var_dump($_SESSION['uc_user']);

        $userInfo=sp_get_current_user();
        $userId=$userInfo?$userInfo['id']:0;
        
        if (!IS_POST) {

            //更新排行榜缓存
            D('killer')->updateRank();//更新排名缓存(有可能导致加载缓慢，必要时候做异步处理)

            //综合排序
            $listInfo = D('killer')->getList(array("status" => 1, "orderType" => 0, "page" => 1, "pageLimit" => 30)); //综合排序


            //今日推荐
            $tadayTopList = D('killer')->getTopList(array("orderType" => 1, "dateTime" => date("Y-m-d H:i:s"))); //今日推荐
            if (empty($tadayTopList['data']['list'])) {
                $tadayTopList = D('killer')->getTopList(array("orderType" => 1)); //今日推荐
            }
            
            //高手榜单
            $adeptType= C('ADEPT_TYPE');
            $topList=array();
            foreach ($adeptType as $key=>$val){
                $topList[$key]=D('killer')->getTopList(array("orderType" => 2, "adeptType" => $key)); //高手榜
                if(!empty($topList[$key]['data']['list'])){
                    $topList[$key]['data']=D('killer')->listProcess($topList[$key]['data']['list'],$userId);
                }
                
            }
            
            //数据列表字段处理
            if(!empty($listInfo['data'])){
                $listInfo['data']['list']=D('killer')->listProcess($listInfo['data']['list'],$userId);
            }
            
            if(!empty($tadayTopList['data']['list'])){
                $tadayTopList['data']=D('killer')->listProcess($tadayTopList['data']['list'],$userId);
            }
            
            $msgList =  D("Home/TalksLog")->getList(array("pageLimit"=>3,"msg_type"=>1,"is_charge"=>0,"role"=>1));//最新观点
  
            $this->assign('msgList', $msgList['data']['list']);     //最新观点
            $this->assign('topList', $topList);                     //高手榜
            $this->assign('adeptType', $adeptType);                 //分类
            $this->assign('tadayTopList',  $tadayTopList['data']);    //今日推荐
            $this->assign('killerList', $listInfo['data']['list']);  //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $this->display(":home");
        } else {
            $data = I('post.');
            $listInfo = D('killer')->getList($data);                   //排序列表
            if(!empty($listInfo)){
                $listInfo['data']['list']=D('killer')->listProcess($listInfo['data']['list'],$userId);
            }
            $this->assign('killerList', $listInfo['data']['list']);    //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $html = $this->fetch(":home_ajax");
            exit($html);
        }
    }
    
    //关注高手
    public function focusKiller(){
        if (IS_POST) {
          
            $data = I('post.');
            if(empty($data['id'])){
                $this->ajaxReturn(500, "参数错误！", "");
            }
            
            if(!sp_is_user_login()){
                $this->ajaxReturn(500, "您还没有登录不能关注！", "");
            }

            $userInfo=sp_get_current_user();
            
            $result = D('KillerFans')->focusKiller(array("killer_id"=>$data['id'],"users_id"=>$userInfo['id']));
            $this->ajaxReturn($result['status'], $result['msg'], "");
        }
    }

    
    public function ajaxPage() {
        if (IS_POST) {
            $data = I('post.');
            $this->assign('pageInfo', $data); //分页信息
            $html = $this->fetch(":page");
            exit($html);
        }
    }
    
    public function getUserInfo(){
        if (!sp_is_user_login()) {
            $this->ajaxReturn(500, "已经退出登录！",array());
        }
        
        $this->ajaxReturn(200, "成功！", sp_get_current_user());
    }

    public function test() {
        echo "sss";
        if (IS_POST) {
            $this->ajaxReturn(200, "666", "");
        } else {
            $this->display(":test");
        }
    }

   


}
