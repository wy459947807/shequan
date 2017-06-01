<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class CuscateController extends AdminbaseController{
    
        protected $cuscate_model;

        public function _initialize() {
            parent::_initialize();
            $this->cuscate_model = D("Common/CourseCate");
        }
	
	// 后台课程分类列表
	public function index(){
            $params = I('post.');
            $params['page'] = I('get.p', 1, 'intval'); //获取页码
            $params['pageLimit'] = 20;
            $cateList = $this->cuscate_model->cateList($params);

            $page = $this->page($cateList['data']['pageInfo']['num'], $params['pageLimit']);
            $this->assign("formget", $params);
            $this->assign("page", $page->show('Admin'));
            $this->assign("cate", $cateList['data']['list']);
            $this->display();
	}
        
        //添加/更新课程分类
        public function update() {
            if (IS_POST) {
                $params = I('post.');   
               
                $retInfo = $this->cuscate_model->cateUpdate($params);
                $this->ajaxReturn($retInfo);
            } else {
                $id = I('get.id', 0, 'intval');
                $cateInfo = $this->cuscate_model->where(array("id" => $id))->find();
                $this->assign($cateInfo);
                $this->display();
            }
        }
        
        
        
        //删除分类
        public function cateDelete() {
            if(IS_POST){
                $ids = I('post.ids/a');
                if(!empty($ids)){
                    if ($this->cuscate_model->where(array('id' => array('in', $ids)))->delete() !== false) {
                        $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                    } else {
                        $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                    }
                }   
            }
        }



	
}