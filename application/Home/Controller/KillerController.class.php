<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
/**
 * 首页
 */
class KillerController extends HomebaseController {
    //首页
    public function index() {
        if(IS_POST){
            
            
        }else{
            $this->display(":killer:index");
        }

    }
    public function help_page() {
        $this->display(":killer:help_page");
    }
    public function user() {
        $this->display(":killer:user");
    }
    public function tearch_page() {   //临时tearch 登录页面
        $this->display(":killer:tearch_page");
    }
}