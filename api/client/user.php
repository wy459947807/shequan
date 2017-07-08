<?php
!defined('IN_APP') && exit('Access Denied');
class usercontrol extends base {

    public $appid;
    public $appsecret;

	function __construct() {
		$this->usercontrol();
	}

	function usercontrol() {
		parent::__construct();
	}

    //发起同步登陆
    function uc_user_synlogin($uid) {
        return $this->uc_api_post('user', 'synlogin', array('uid'=>$uid));;
    }

    //发起同步登出
    function uc_user_synlogout() {
        return $this->uc_api_post('user', 'synlogout', array());
    }
}

?>