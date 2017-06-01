<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class GiftController extends AdminbaseController{
    
	protected $gift_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->gift_model = D("Common/gift");
	}
	
	// 后台礼物列表
	public function index(){
            $params = I('post.');
            $params['page'] = I('get.p', 1, 'intval'); //获取页码
            $params['pageLimit'] = 20;
            $giftList = $this->gift_model->giftList($params);

            $page = $this->page($giftList['data']['pageInfo']['num'], $params['pageLimit']);
            $this->assign("formget", $params);
            $this->assign("page", $page->show('Admin'));
            $this->assign("gift", $giftList['data']['list']);
            $this->display();
	}
        
        //添加/更新礼物
        public function update() {
            if (IS_POST) {
                $params = I('post.');   
                $params['img']=sp_get_image_preview_url($params['img']);//修改图片路径    
                $retInfo = $this->gift_model->giftUpdate($params);
                $this->ajaxReturn($retInfo);
            } else {
                $id = I('get.id', 0, 'intval');
                $giftInfo = $this->gift_model->where(array("id" => $id))->find();
                if (empty($giftInfo)) {
                    $this->display();
                } else {
                    $this->assign($giftInfo);
                    $this->display();
                }
            }
        }
        
        //禁用礼物
        public function updateStatus() {
            if(IS_POST){
                $ids = I('post.ids/a');
                $status= I('post.status/d', 2); //获取状态
                if(!empty($ids)){
                    if ($this->gift_model->where(array('id' => array('in', $ids)))->save(array('status' => $status)) !== false) {
                        $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                    } else {
                        $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                    }
                }   
            }
        }
        
        //删除礼物
        public function giftDelete() {
            if(IS_POST){
                $ids = I('post.ids/a');
                if(!empty($ids)){
                    if ($this->gift_model->where(array('id' => array('in', $ids)))->delete() !== false) {
                        $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                    } else {
                        $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                    }
                }   
            }
        }


	
	
	
	
}