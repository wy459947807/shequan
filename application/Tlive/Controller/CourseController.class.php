<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;

/**
 * 首页
 */
class CourseController extends HomebaseController {

    //首页
    public function index() {
        $this->display(":course:index");
    }

}