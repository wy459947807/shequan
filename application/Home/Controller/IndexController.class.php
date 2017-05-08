<?php

namespace Home\Controller;

use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {

    //首页
    public function index() {
        if (!IS_POST) {
            $listInfo = D('killer')->getList(array("status" => 1, "orderType" => 0, "page" => 1, "pageLimit" => 30)); //综合排序

            $tadayTopList = D('killer')->getTopList(array("orderType" => 1, "dateTime" => date("Y-m-d H:i:s"))); //今日推荐
            $gupiaoTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 1)); //股票高手榜
            $qihuoTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 2)); //期货高手榜
            $waihuiTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 3)); //外汇高手榜
            $waipanTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 4)); //外盘高手榜

            if (empty($tadayTopList['data'])) {
                $tadayTopList = D('killer')->getTopList(array("orderType" => 1)); //今日推荐
            }

            $this->assign('tadayTopList', $tadayTopList['data']);    //今日推荐
            $this->assign('gupiaoTopList', $gupiaoTopList['data']);  //股票高手榜
            $this->assign('qihuoTopList', $qihuoTopList ['data']);  //期货高手榜
            $this->assign('waihuiTopList', $waihuiTopList['data']);  //外汇高手榜
            $this->assign('waipanTopList', $waipanTopList['data']);  //外盘高手榜

            $this->assign('killerList', $listInfo['data']['list']);  //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $this->display(":home");
        } else {
            $data = I('post.');
            $listInfo = D('killer')->getList($data);                   //排序列表
            $this->assign('killerList', $listInfo['data']['list']);    //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $html = $this->fetch(":home_ajax");
            exit($html);
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

    public function test() {
        if (IS_POST) {
            $this->ajaxReturn(200, "666", "");
        } else {
            $this->display(":test");
        }
    }

   


}
