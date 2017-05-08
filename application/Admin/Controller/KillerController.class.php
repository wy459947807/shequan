<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class KillerController extends AdminbaseController {

    protected $killer_model;

    public function _initialize() {
        parent::_initialize();
        $this->killer_model = D("Common/Killer");
    }

    // 管理员列表
    public function index() {
        $where = array();
        $status = I('request.status');
        $where['status'] = 0;
        if ($status) {
            $where['status'] = $status;
        }
        $count = $this->killer_model->where($where)->count();
        $page = $this->page($count, 20);
        $killers = $this->killer_model
                ->where($where)
                ->order("ctime DESC")
                ->limit($page->firstRow, $page->listRows)
                ->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("killers", $killers);
        $this->display();
    }

    // 高手审核
    public function check() {
        if (isset($_POST['ids'])) {
            $data["status"] = 1;
            
            foreach ($_POST['ids'] as $key=>$val){
                $data['user_pass']= sp_random_string(10);
                $killerInfo=$this->killer_model->where(array("id"=>$val))->find();
                if(empty($killerInfo)){
                    $this->error("审核失败！");
                }
                
                $emailData=array(
                    "to"=>$killerInfo['email'],
                    "subject"=>"十年赢家网-赢家社圈",
                    "content"=>"您好".$killerInfo['real_name']
                                ."（先生/女士），恭喜您通过审核成为十年赢家网高手中的一员，<br/>"
                                ."您的登录账户为：".$killerInfo['user_login']."<br/>"
                                . "密码为：".$data['user_pass']."<br/>"
                                . "请及时登录赢家社圈修改密码！",
                );
                        
                $this->sendEmail($emailData);

                if ($this->killer_model->where(array("id"=>$val))->save($data) == false) {   
                    $this->error("审核失败！");
                } 
            } 
            $this->success("审核成功！");
        }
        
    }
    

    public function detail(){
        $id = I('get.id',0,'intval');
        $killerInfo=$this->killer_model->where(array("id"=>$id))->find();
        $this->assign($killerInfo);
        $this->display();
    }

}
