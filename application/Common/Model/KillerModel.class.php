<?php
namespace Common\Model;
use Common\Model\CommonModel;
class KillerModel extends CommonModel
{
	
	protected $_validate = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('user_login', 'require', '用户名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH  ),
            array('user_pass', 'require', '密码不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
	);
	
	protected $_auto = array(
	    array('ctime','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
	);
	
	//用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
	function mGetDate() {
            return time();
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
		
		if(!empty($data['user_pass']) && strlen($data['user_pass'])<25){
			$data['user_pass']=sp_password($data['user_pass']);
		}
	}
        
        public function getDetail($params){
            $this->sqlFrom = " tg_killer ";                     //数据库查询表
            $this->sqlField = " * ";                            //数据库查询字段
            $this->sqlWhere = " (1=1) ";                        //数据库查询条件
            $this->bindValues = array();
            
            if(!empty($params['id'])){
                $this->sqlWhere .= " and  id=%d ";
                $this->bindValues[] = $params['id'];
            }
            
            $dataInfo = $this->getOne();
            
            if(!empty($dataInfo["data"])){
                $adeptArray=C('ADEPT_TYPE');
                $dataInfo["data"]['adept_type']=$adeptArray[$dataInfo["data"]['adept_type']];
                $dataInfo["data"]['subscribe']= unserialize($dataInfo["data"]['subscribe'])?unserialize($dataInfo["data"]['subscribe']):null;//获取订阅标准
                $dataInfo["data"]['cert_imgs']= unserialize($dataInfo["data"]['cert_imgs'])?unserialize($dataInfo["data"]['cert_imgs']):null;//获取订阅标准
                $dataInfo["data"]['adept_names']= unserialize($dataInfo["data"]['adept_names'])?unserialize($dataInfo["data"]['adept_names']):null;//获取擅长领域
                $dataInfo["data"]['tag']= !empty($dataInfo["data"]['tag'])?array_filter(explode("|",$dataInfo["data"]['tag'])):null;
                
                
                $courseList=D("Common/Course")->courseList(array("killer_id"=>$dataInfo["data"]['id']));
                $dataInfo["data"]['courseList']=$courseList['data']['list'];
                
                $dataInfo["data"]['is_focused']=false;
                if(!empty($params['uid'])){
                    $focusInfo= M("KillerFans")->where(array("killer_id"=>$params['id'],"users_id"=>$params['uid']))->find();
                    if(!empty($focusInfo)){
                        $dataInfo["data"]['is_focused']=true;
                    }
                }
                
            }

            
            return $dataInfo;
 
        }
	
}

