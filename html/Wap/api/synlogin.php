<?php




define('IN_APP',true);


//用户发起登陆-登陆成功后 通知 十年赢家网 服务端（完成其他应用的同步登陆）

require_once dirname(__FILE__) . "/release.php";
require_once dirname(__FILE__) . "/function.php";
require_once dirname(__FILE__) . "/client/base.php";
require_once dirname(__FILE__) . "/client/user.php";

$classname = 'usercontrol';
$control = new $classname();
$method = 'uc_user_synlogin';
if(method_exists($control, $method)) {
	$control->appid='h2M0adsdz7r0nbvc';//应用id
	$control->appsecret='y2D0Ed0d77J06bTcQepccci3w6xeb2We';//应用密钥
	$uid="3333";//用户openid
	echo ($control->$method($uid));//返回数据
}else{
	return '';
}


/*

返回数据格式如下：

不用做任何处理  直接再浏览器里输出来即可 



<script type="text/javascript" src="http://www.baidu.com/test/api/uc_test.php?time=1498725663&code=342423423423423432432" reload="1"></script>


<script type="text/javascript" src="http://www.qq.com/api/uc_test.php?time=1498725663&code=rewrwerewrewrqw" reload="1"></script>


<script type="text/javascript" src="http://www.sina.com/api/uc_test.php?time=1498725663&code=1111111111" reload="1"></script>"

*/