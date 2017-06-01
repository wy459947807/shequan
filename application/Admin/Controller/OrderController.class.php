<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class OrderController extends AdminbaseController {

    protected $order_model;
    
    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Common/Order");
    }

    // 订单列表
    public function index() {
        $params = I('post.');
        $params['page'] = I('get.p', 1, 'intval'); //获取页码
        $params['pageLimit'] = 20;
        $orderList = $this->order_model->orderList($params);
        $page = $this->page($orderList['data']['pageInfo']['num'], $params['pageLimit']);
        $this->assign("formget", $params);
        $this->assign("page", $page->show('Admin'));
        $this->assign("order", $orderList['data']['list']);
        $this->display();
    }

    //订单详情
    public function detail() {
        $id = I('get.id',0,'intval');
        $orderInfo=$this->order_model->orderDetail(array("id"=>$id));
        if(empty($orderInfo)){
            $this->error("订单不存在！");
        }

        //var_dump($orderInfo['data']);
        
        
        $this->assign($orderInfo['data']);
        $this->display();
    }

   

    //更新订单
    public function update() {
        if(IS_POST){
            $ids = I('post.ids/a');
            $status= I('post.status/d', 3); //获取状态
            if(!empty($ids)){
                if ($this->order_model->where(array('id' => array('in', $ids)))->save(array('status' => $status,"utime"=>time())) !== false) {
                    $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                } else {
                    $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                }
            }   
        }
    }

}
