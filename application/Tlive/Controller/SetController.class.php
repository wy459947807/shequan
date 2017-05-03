<?php
namespace Tlive\Controller;
use Common\Controller\TlivebaseController;

/**
 * 首页
 */
class SetController extends TlivebaseController {

    //首页
    public function index() {

        $this->display(":index");
    }
}