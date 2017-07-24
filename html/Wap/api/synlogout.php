<?php


define('IN_APP',true);


//用户用户退出登录成功后 通知 十年赢家网 服务端（完成其他应用的同步退出登录）

require_once dirname(__FILE__) . "/release.php";
require_once dirname(__FILE__) . "/function.php";
require_once dirname(__FILE__) . "/client/base.php";
require_once dirname(__FILE__) . "/client/user.php";


$classname = 'usercontrol';
$control = new $classname();
$method = 'uc_user_synlogout';
if(method_exists($control, $method)) {
	$control->appid='h2M0adsdz7r0nbvc';//应用id
	$control->appsecret='y2D0Ed0d77J06bTcQepccci3w6xeb2We';//应用密钥
	echo ($control->$method());
}else{
	return '';
}



/*

返回数据格式如下：

不用做任何处理  直接再浏览器里输出来即可 



<script type="text/javascript" src="http://www.baidu.com/test/api/uc_test.php?time=1498725684&code=11111111" reload="1"></script>


<script type="text/javascript" src="http://www.qq.com/api/uc_test.php?time=1498725684&code=2222222" reload="1"></script>


<script type="text/javascript" src="http://www.sina.com/api/uc_test.php?time=1498725684&code=3333333" reload="1"></script>




*/