<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class TliveController extends HomebaseController {

    //首页
    public function index() {
        $this->display(":live:index");
    }

}