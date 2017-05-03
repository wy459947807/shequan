<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
/**
 * 首页
 */
class TestController extends HomebaseController {
    //首页
    public function index() {
        $this->display(":test:index");
    }
    public function help_page() {
        $this->display(":test:help_page");
    }
    public function user() {
        $this->display(":test:user");
    }
    public function tearch_page() {   //临时tearch 登录页面
        $this->display(":test:tearch_page");
    }
}