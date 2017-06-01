<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class AdtypeController extends AdminbaseController{
    
	protected $adtype_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->adtype_model = D("Common/AdvertType");
    }
	
	// 后台广告位列表
	public function index(){
            $params = I('post.');
            $params['page'] = I('get.p', 1, 'intval'); //获取页码
            $params['pageLimit'] = 20;
            $adtypeList = $this->adtype_model->adtypeList($params);

            $page = $this->page($adtypeList['data']['pageInfo']['num'], $params['pageLimit']);
            $this->assign("formget", $params);
            $this->assign("page", $page->show('Admin'));
            $this->assign("adtype", $adtypeList['data']['list']);
            $this->display();
	}
        
        //添加/更新广告位
        public function update() {
            if (IS_POST) {
                $params = I('post.');   
               
                $retInfo = $this->adtype_model->adtypeUpdate($params);
                $this->ajaxReturn($retInfo);
            } else {
                $id = I('get.id', 0, 'intval');
                $adtypeInfo = $this->adtype_model->where(array("id" => $id))->find();
                if (empty($adtypeInfo)) {
                    $this->display();
                } else {
                    $this->assign($adtypeInfo);
                    $this->display();
                }
            }
        }
        
        
        
        //删除广告位
        public function adtypeDelete() {
            if(IS_POST){
                $ids = I('post.ids/a');
                if(!empty($ids)){
                    if ($this->adtype_model->where(array('id' => array('in', $ids)))->delete() !== false) {
                        $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                    } else {
                        $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                    }
                }   
            }
        }



	
}