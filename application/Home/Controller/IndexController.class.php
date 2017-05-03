<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {
    //首页
    public function index() {
        if(!IS_POST){
            //统合排序
            $list = M('killer')->query("SELECT COUNT(`id`) as num FROM `tg_killer` WHERE  status = 1");
            //按直播时间排序
            $listOne = M('killer')->query("SELECT `id`,`real_name`,`avatar`,`adept_type`,`type`,`fans`,`ctime`,`last_login_time` FROM `tg_killer` WHERE  status = 1 ORDER BY last_login_time LIMIT 0,30");
            //按粉丝数排序
            $listThree = M('killer')->query("SELECT `id`,`real_name`,`avatar`,`adept_type`,`type`,`fans`,`ctime`,`last_login_time` FROM `tg_killer` WHERE  status = 1 ORDER BY fans LIMIT 0,30");
            $this->assign('pcount',ceil($list[0]['num']/30));
            $this->assign('listOne',$listOne);
            $this->assign('listThree',$listThree);
            $this->display(":home");
        }else{
            $pageIndex = $_POST['pageIndex'];
            $pageSize = $_POST['pageSize'];
            $from = $pageIndex*$pageSize;
            //统合排序
            $listOne = M('killer')->query("SELECT `id`,`real_name`,`avatar`,`adept_type`,`type`,`fans`,`ctime`,`last_login_time` FROM `tg_killer` WHERE  status = 1 ORDER BY id LIMIT {$from},{$pageSize}");
            $this->assign('listOne',$listOne);
            $html = $this->fetch(":home_ajax");
            exit($html);
        }

    }

}