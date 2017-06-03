<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class CourseController extends AdminbaseController {

    protected $course_model;
    protected $killer_model;
    protected $cuscate_model;

    public function _initialize() {
        parent::_initialize();
        $this->course_model = D("Common/Course");
        $this->killer_model = D("Common/Killer");
        $this->cuscate_model = D("Common/CourseCate");
    }

    // 课程列表
    public function index() {
        $params = I('post.');
        $params['page'] = I('get.p', 1, 'intval'); //获取页码
        $params['pageLimit'] = 20;
        $courseList = $this->course_model->courseList($params);

        $page = $this->page($courseList['data']['pageInfo']['num'], $params['pageLimit']);
        $this->assign("formget", $params);
        $this->assign("page", $page->show('Admin'));
        $this->assign("course", $courseList['data']['list']);
        $this->display();
    }

    //课程更新
    public function update() {

        if (IS_POST) {
            $params = I('post.');
            $params['cover'] = image_serialize_list($params, "cover");
            $params['detail'] = htmlspecialchars_decode($params['detail']);
            $params['video'] = substr($params['video'],0,strrpos($params['video'],'.')).".m3u8";//更改视频后缀
            $retInfo = $this->course_model->courseUpdate($params);
            $this->ajaxReturn($retInfo);
        } else {
            $id = I('get.id', 0, 'intval');
            $courseInfo = $this->course_model->where(array("id" => $id))->find();
            $courseCate = $this->cuscate_model->select();
            $this->assign("courseCate", $courseCate);
            if (!empty($courseInfo)) {
                $imageArray = array();
                $imageArray['cover'] = unserialize($courseInfo['cover']); //图片处理       
                $killerInfo = $this->killer_model->where(array("id" => $courseInfo['killer_id']))->find();
                $this->assign($courseInfo);
                $this->assign("images", $imageArray);
                $this->assign("teacher_name", $killerInfo['real_name']);
            }  
            $this->display();
        }
    }

    //获取高手列表
    public function killers() {
        $where = array();
        $sql = array();
        $data = I('request.');

        if (!empty($data['keyword'])) {
            $where['real_name'] = array('like', "%" . $data['keyword'] . "%");
            $where['mobile'] = array('like', "%" . $data['keyword'] . "%");
            $where['_logic'] = 'or';
            $sql['_complex'] = $where;
        }

        $count = $this->killer_model->where($sql)->count();
        $page = $this->page($count, 10, 1, 10, 'page', array("index" => "javascript:goPage(1)", "list" => "javascript:goPage({page})"), true);
        $killers = $this->killer_model
                ->where($where)
                ->order("ctime DESC")
                ->limit($page->firstRow, $page->listRows)
                ->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("killers", $killers);
        $this->assign("formget", $data);
        $this->display(":Course:update:killers");
    }

    //删除课程
    public function delete() {
        if(IS_POST){
            $ids = I('post.ids/a');
            if(!empty($ids)){
                if ($this->course_model->where(array('id' => array('in', $ids)))->save(array('status' => 1)) !== false) {
                    $this->ajaxReturn(array( "status" => 200, "msg" => "操作成功！","data" => ""));
                } else {
                    $this->ajaxReturn(array( "status" => 500, "msg" => "操作失败！","data" => ""));
                }
            }   
        }
    }

}
