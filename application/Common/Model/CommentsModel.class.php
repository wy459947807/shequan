<?php
namespace Common\Model;
use Common\Model\CommonModel;
class CommentsModel extends CommonModel{
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('full_name', 'check_full_name', '姓名不能为空！', 1, 'callback', CommonModel:: MODEL_INSERT  ),
			//array('email', 'check_full_email', '邮箱不能为空！', 1, 'callback', CommonModel:: MODEL_INSERT ),
			array('content', 'require', '评论不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
			//array('email','email','邮箱格式不正确！',0,'',CommonModel:: MODEL_BOTH ),
			array('post_table','table_exists','您评论的内容不存在！',0,'callback',CommonModel:: MODEL_BOTH ),
	);
	
	protected $_auto = array(
			array('createtime','mDate',1,'callback'), // 对msg字段在新增的时候回调htmlspecialchars方法
			
	);
	
	function mDate(){
		return date("Y-m-d H:i:s");
	}
	
	function check_full_name($data){
		if(empty($data)){
		    $session_user=session('user');
			if(!empty($session_user)){
				return true;
			}
			return false;
		}
		
		return true;
	}
	
	function check_full_email($data){
		if(empty($data)){
		    $session_user=session('user');
			if(!empty($session_user)){
				return true;
			}
			return false;
		}
		
		return true;
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
	
	protected function _after_insert($data,$options){
		parent::_after_insert($data,$options);
		$id=$data['id'];
		$parent_id=$data['parentid'];
		if($parent_id==0){
			$d['path']="0-$id";
		}else{
			$parent=$this->where("id=$parent_id")->find();
			$d['path']=$parent['path'].'-'.$id;
		}
		$this->where("id=$id")->save($d);
	}
	
	
	protected function _after_update($data,$options){
		parent::_after_update($data,$options);
		
		if(isset($data['parentid'])){
			$id=$data['id'];
			$parent_id=$data['parentid'];
			if($parent_id==0){
				$d['path']="0-$id";
			}else{
				$parent=$this->where("id=$parent_id")->find();
				$d['path']=$parent['path'].'-'.$id;
			}
			
			$this->where("id=$id")->save($d);
		}
		
	}
        
        
        // 前台用户提交评论
	public function submitComment($params){
            $model = M();
            $users_model=M('Users');
            $user=$users_model->field("user_login,user_email,user_nicename")->where(array("id"=>$params['uid']))->find();
            $username=$user['user_login'];
            $user_nicename=$user['user_nicename'];
            $email=$user['user_email'];
            $params['full_name']=empty($user_nicename)?$username:$user_nicename;
            $params['email']=$email;
 
            if(C("COMMENT_NEED_CHECK")){
                    $params['status']=0;//评论审核功能开启
            }else{
                    $params['status']=1;
            }

            $result=$model->table(C('DB_PREFIX') . 'comments')->add($params);
            
            if ($result==false){
                $this->result['status'] = 500;
                $this->result['msg'] = "评论失败！";
                return $this->result;
            }

            return $this->result;
	}
        
        
        public function getComments($params){
            $this->sqlFrom = " tg_comments ";                     //数据库查询表
            $this->sqlField = " * ";                            //数据库查询字段
            $this->sqlWhere = " (1=1) ";                        //数据库查询条件
            $this->bindValues = array();
            if (!empty($params['page'])) $this->page = $params['page'];
            if (!empty($params['pageLimit'])) $this->pageLimit = $params['pageLimit'];
            
            if (!empty($params['post_id'])) {
                $this->sqlWhere .= " and  post_id = %d ";
                $this->bindValues[] = $params['post_id'];
            }
 
            if (!empty($params['post_table'])) {
                $this->sqlWhere .= " and  post_table = '%s' ";
                $this->bindValues[] = $params['post_table'];
            }

            $listInfo = $this->getPageList();
            return $listInfo;
            
        }
        
        
        
}