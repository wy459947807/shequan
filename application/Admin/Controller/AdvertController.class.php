<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class AdvertController extends AdminbaseController{
    
	protected $advert_model;
        protected $adtype_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->advert_model = D("Common/Advert");
                $this->adtype_model = D("Common/AdvertType");
	}
	
	// 后台广告列表
	public function index(){
            $params = I('post.');
            $params['page'] = I('get.p', 1, 'intval'); //获取页码
            $params['pageLimit'] = 20;
            $advertList = $this->advert_model->advertList($params);
            $adtypeList = $this->adtype_model->select();

            $page = $this->page($advertList['data']['pageInfo']['num'], $params['pageLimit']);
            $this->assign("formget", $params);
            
            $this->assign("adtypeList", $adtypeList);
            
            $this->assign("page", $page->show('Admin'));
            $this->assign("advert", $advertList['data']['list']);
            $this->display();
	}
        
        //添加/更新广告
        public function update() {
            if (IS_POST) {
                $params = I('post.');
                $params['imgs'] = image_serialize_list($params, "imgs");           
                $retInfo = $this->advert_model->advertUpdate($params);
                $this->ajaxReturn($retInfo);
            } else {
                $id = I('get.id', 0, 'intval');
                $advertInfo = $this->advert_model->where(array("id" => $id))->find();
                $adtypeList = $this->adtype_model->select();//查询广告位
                $this->assign("adtypeList", $adtypeList);
                
                if (empty($advertInfo)) {
                    $this->display();
                } else {
                    $imageArray = array();
                    $imageArray['imgs'] = unserialize($advertInfo['imgs']); //图片处理   
                  
                    $this->assign($advertInfo);
                    $this->assign("images", $imageArray);
                    $this->display();
                }
            }
        }
        
        //禁用广告
        public function updateStatus() {
            if(IS_POST){
                $ids = I('post.ids/a');
                $status= I('post.status/d', 2); //获取状态
                if(!empty($ids)){
                    if ($this->advert_model->where(array('id' => array('in', $ids)))->save(array('status' => $status)) !== false) {
                        $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                    } else {
                        $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                    }
                }   
            }
        }

	
	
	
	
}