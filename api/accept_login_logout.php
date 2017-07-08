<?php

// 服务端 返回给客户端 很多 <script src="***************"  reload="1"></script>  其中一个的例子


define('UC_CLIENT_VERSION', '1.6.0');
define('UC_CLIENT_RELEASE', '20110501');

define('API_SYNLOGIN', 1);		//note 同步登录 API 接口开关
define('API_SYNLOGOUT', 1);		//note 同步登出 API 接口开关
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('IN_API', true);
define('UC_API', true);

define('DATELEVEL', 0);
define('APPID', '1111111');         //应用id
define('APPSECRET', '222222');      //应用密钥

class Ucenter{

    public function index() {
		require_once dirname(__FILE__) . "/release.php";
		require_once dirname(__FILE__) . "/function.php";

        $get = $post = array();
        $code = @$_GET['code'];
        parse_str(authcode($code, 'DECODE', APPSECRET), $get);
		
        if(get_magic_quotes_gpc()) {
            $get = uc_stripslashes($get);
        }
        $timestamp = time();
        if($timestamp - $get['time'] > 3600) {
            exit('Authracation has expiried');
        }
        if(empty($get)) {
            exit('Invalid Request');
        }
        require_once dirname(__FILE__) . "/lib/xml.class.php";
        $post = xml_unserialize(file_get_contents('php://input'));
        if(in_array($get['action'], array('synlogin', 'synlogout'))) {
            exit($this->$get['action']($get, $post));
        } else {
            exit(API_RETURN_FAILED);
        }
    }

    /**
     * 同步登陆 
     */
    public function synlogin($get, $post) {

        if(!API_SYNLOGIN) {
            return API_RETURN_FORBIDDEN;
        }
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

		$uid=$get['uid'];
        $username=trim($get['username']);
		
        if ($uid==0) {
            return API_RETURN_FAILED;
        }
        //登陆钩子
        
        //登陆 [ 写入session 或者 cookie ]
		/**
		* $_SESSION['uid']=$uid;
		*/
        return API_RETURN_SUCCEED;
    }
    
    /**
     * 同步退出
     */
    public function synlogout($get, $post) {

        if(!API_SYNLOGOUT) {
            return API_RETURN_FORBIDDEN;
        }
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        //退出登录 [清除session或者cookie]
		/**
		* unset($_SESSION['uid']);
		*/
        return API_RETURN_SUCCEED;
    }
		
}

$control = new Ucenter();
$control->index();

/*
 * 如果是通知同步登陆
 * url是这样的
 * http://www.example.com/api/uc.php?time=1498730944&code=c359TdBQ2KgwJ6i3CDrEXteUA%2BSqCTz
 *
 *用APPSECRET解析code之后
 *array(7) {
  ["action"] => string(8) "synlogin"                  //登陆
  ["username"] => string(9) "左岸的小熊"             //用户昵称
  ["uid"] => string(15) "5F9vsEy2/n0gtyk"           //用户的openid
  ["headimgurl_small"] => string(55) "http://www.10jrw.com/data/upload/avatar/default_100.jpg"   //用户头像 小图 size 100
  ["headimgurl_big"] => string(55) "http://www.10jrw.com/data/upload/avatar/default_200.jpg"    //用户头像 大图 size 200
  ["gender"] => string(1) "0"                           //0表示女 1表示男
  ["time"] => string(10) "1498729863"                   //时间 比较下有没有过期
}
 */



/*
 * 如果是通知同步退出
 * url也是这样的
 * http://www.example.com/api/uc.php?time=1498731261&code=ef0addHXJf6IPEsS3daxihtS1cZQ
 *
 * 用APPSECRET解析code之后
 *
 *array (size=2)
  'action' => string 'synlogout' (length=9)  //退出
  'time' => string '1498731261' (length=10)  //时间 比较下有没有过期
 */