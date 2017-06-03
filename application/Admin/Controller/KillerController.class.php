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
                                ."（先生/女士），恭喜您通过审核成为十年赢家网高手中的一员，<br/>",
                               
                );
                        
                $this->sendEmail($emailData);

                if ($this->killer_model->where(array("id"=>$val))->save($data) == false) {   
                    $this->error("审核失败！");
                } 
            } 
            $this->success("审核成功！");
        }
        
    }
    
    //拒绝
    public function refuse(){
        
        if(IS_POST){
            $retData=array(
                "status" => 200, 
                "msg" => "操作成功！",
                "data" => ""
            );
            $params = I('post.');
            $rules=array(
                array('id','require','id不得为空！',1,'regex',3),
                array('refuse_cause','require','refuse_cause不得为空！',1,'regex',3),
            );   
            $this->checkField($rules, $params);
            
            $updateArray=array(
                "status"=>2,
                "refuse_cause"=>$params['refuse_cause'],
            );
            if ($this->killer_model->where(array("id"=>$params['id']))->save($updateArray) == false) {   
                $retData['status']=500;
                $retData['msg']="操作失败!";
                $this->ajaxReturn($retData);
            }
            
            $this->ajaxReturn($retData);

        }else{
            $this->display(":Killer:index:checkup");
        }
    }



    public function detail(){
        $id = I('get.id',0,'intval');
        $killerInfo=$this->killer_model->where(array("id"=>$id))->find();
        $this->assign($killerInfo);
        $this->display();
    }
    
    //字段验证
    protected function checkField($rules,$params) {
        $model = M(); 
        if ($model->validate($rules)->create($params)===false){
            $retData=array(
                "status" => 500, 
                "msg" => $model->getError(),
                "data" => ""
            );
            $this->ajaxReturn($retData);

        }
    }

}
