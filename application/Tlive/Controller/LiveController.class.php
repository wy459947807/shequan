<?php
namespace Tlive\Controller;
use Common\Controller\TlivebaseController;

/**
 * 首页
 */
class LiveController extends TlivebaseController {


    //首页
    public function index() {
        $this->display(":tlive");
    }

}