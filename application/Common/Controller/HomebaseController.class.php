<?php

namespace Common\Controller;

use Common\Controller\AppframeController;
use Home\Lib\FileOpera;

class HomebaseController extends AppframeController {

    protected $model;//公用model
    protected $loginTime=6;//登录有效期(单位：小时)
    protected $params; //页面参数
    
    public function __construct() {
        $this->set_action_success_error_tpl();
        parent::__construct();
    }

    function _initialize() {
        parent::_initialize();
        defined('TMPL_PATH') or define("TMPL_PATH", C("SP_TMPL_PATH"));
        $site_options = get_site_options();
        $this->assign($site_options);
        $ucenter_syn = C("UCENTER_ENABLED");
        if ($ucenter_syn) {
            $session_user = session('user');
            if (empty($session_user)) {
                if (!empty($_COOKIE['thinkcmf_auth']) && $_COOKIE['thinkcmf_auth'] != "logout") {
                    $thinkcmf_auth = sp_authcode($_COOKIE['thinkcmf_auth'], "DECODE");
                    $thinkcmf_auth = explode("\t", $thinkcmf_auth);
                    $auth_username = $thinkcmf_auth[1];
                    $users_model = M('Users');
                    $where['user_login'] = $auth_username;
                    $user = $users_model->where($where)->find();
                    if (!empty($user)) {
                        $is_login = true;
                        session('user', $user);
                    }
                }
            } else {
                
            }
        }
        
        
        $this->model = M(); 
        $this->params=I('param.');//获取参数
        if(empty($this->params)){
            $this->params=$this->getParams();
        }

        //$this->check_jrw();
        $this->check_user_login();
        
        if (sp_is_user_login()) {
            $this->assign("user", sp_get_current_user());
        }

        if (!empty(C("JRW_URL"))) {
            $this->assign("jrw_url", C("JRW_URL"));
        }
    }
    
    //获取put参数
    protected function getParams(){
        $paramsArray = json_decode(file_get_contents('php://input'), TRUE);
        return $paramsArray;
    }
    
    //检测用户登录状态
    protected function check_user_login(){
        
        /*测试帐号登录*/
        
        $_SESSION['uc_user']=array(
            "uid"=>"1252",
            'username'=>"测试帐号",
            'headimgurl_small'=>"http://www.10jrw.com/data/upload/avatar/000/00/80/fbb5d2cf4558b47551ad4a26ce3f11a0_48.jpg",
            'gender'=>"1",
            'mobile'=>"18739178217"
        );
        
        /*测试高手帐号登录*/
        /*
        $_SESSION['uc_user']=array(
            "uid"=>"8074",
            'username'=>"汪勇",
            'headimgurl_small'=>"http://www.10jrw.com/data/upload/avatar/000/00/80/fbb5d2cf4558b47551ad4a26ce3f11a0_48.jpg",
            'gender'=>"1",
            'mobile'=>"18739178207"
        );*/
        
        if(!empty($_SESSION['uc_user'])){
            $ucUser=$_SESSION['uc_user'];
            //$this->params['jrw_id']=$ucUser['jrw_id'];
            $tempData['jrw_id']=$ucUser['uid'];
            $tempData['user_nicename']=$ucUser['username'];
            $tempData['avatar']=$ucUser['headimgurl_small'];
            $tempData['sex']=$ucUser['gender'];
            $tempData['mobile']=$ucUser['mobile'];
            
            $tokenInfo = json_decode(http_Post($tempData,C('APP_HOST').'index/getToken'),true);

            if(!empty($tokenInfo['data'])){
               $userInfo= json_decode(http_Post($tokenInfo['data'],C('APP_HOST').'User/userInfo'),true);
  
               $userInfo['data']['token']=$tokenInfo['data']['token'];
               if(!empty($userInfo['data'])){ 
                   session('user', $userInfo['data']);
                   $this->params['uid']=$userInfo['data']['id'];
                   $this->params['token']=$userInfo['data']['token'];
               }
            }
        }
    }
    
    //字段验证
    protected function checkField($rules,$params) {
        if ($this->model->validate($rules)->create($params)===false){
            $this->error($this->model->getError());
        }
    }

    
    //高手登录
    /*
    protected function killer_login($user){
        if(empty($user['killer_id']))return;
        $killerInfo = M('killer')->where(array('id' => $user['killer_id']))->find();
        if(empty($killerInfo)||!$killerInfo['status']) return;
        session('tuser',$killerInfo);
        $killerInfo['last_login_ip']=get_client_ip(0,true);
        $killerInfo['last_login_time']=date("Y-m-d H:i:s");
        M('killer')->save($killerInfo);
        cookie("tlive_username",$user['user_login'],3600*24*30);  
    }*/


    /**
     * 检查jrw的用户状态
     */
    /*
    protected function check_jrw() {
        if (isset($_GET['k']) && !empty($_GET['k'])) {
            //检查用户登录状态
            $data = array(
                'token' => md5(C('TOKEN') . date("Y-m-d", time())),
                'sid' => $_GET['k']
            );
            $result = json_decode(http_Post($data, C('JRW_URL') . '/checkuser.php'), true);
            if ($result['status'] == 1 && $result['data']['islogin'] == 1) {
                $user = M('users')->where(array('jrw_id' => $result['data']['user_info']['id']))->find();
                if (!empty($user)) {
                    $user['token']=$data['token'];
                    $user['sid']=$data['sid'];
                    session('user', $user);
                 
                    $this->killer_login($user);//高手登录
                    
                } else {
                    //先添加
                    $data = array(
                        'user_login' => $result['data']['user_info']['mobile'],
                        'user_email' => '',
                        'mobile' => $result['data']['user_info']['mobile'],
                        'user_nicename' => $result['data']['user_info']['username'],
                        'user_pass' => sp_password('111111'),
                        'last_login_ip' => get_client_ip(0, true),
                        'create_time' => date("Y-m-d H:i:s"),
                        'last_login_time' => date("Y-m-d H:i:s"),
                        'user_status' => 1,
                        "user_type" => 2, //会员
                        "jrw_id" => $result['data']['user_info']['id'],
                    );
                    $ret_id = M("Users")->add($data);
                    if ($ret_id) {
                        $data['id'] = $ret_id;
                        session('user', $data);
                    }
                }
            }else{
                session("user",null);//只有前台用户退出
                session("tuser",null);//高手退出
            }
        }else{
            $userInfo=sp_get_current_user();
            if($userInfo){
                $data = array(
                    'token' => md5(C('TOKEN') . date("Y-m-d", time())),
                    'sid' => $userInfo['sid']
                );
                $result = json_decode(http_Post($data, C('JRW_URL') . '/checkuser.php'), true);
                if($result['status'] != 1||$result['data']['islogin'] != 1){
                    session("user",null);//只有前台用户退出
                    session("tuser",null);//高手退出
                } 
            }
        }
    }*/

    /**
     * 检查用户登录
     */
    protected function check_login() {
        $session_user = session('user');
        if (empty($session_user)) {
            $this->error('您还没有登录！', leuu('user/login/index', array('redirect' => base64_encode($_SERVER['HTTP_REFERER']))));
        }
    }

    /**
     * 检查用户状态
     */
    protected function check_user() {
        $user_status = M('Users')->where(array("id" => sp_get_current_userid()))->getField("user_status");
        if ($user_status == 2) {
            $this->error('您还没有激活账号，请激活后再使用！', U("user/login/active"));
        }

        if ($user_status == 0) {
            $this->error('此账号已经被禁止使用，请联系管理员！', __ROOT__ . "/");
        }
    }

    /**
     * 发送注册激活邮件
     */
    protected function _send_to_active() {
        $option = M('Options')->where(array('option_name' => 'member_email_active'))->find();
        if (!$option) {
            $this->error('网站未配置账号激活信息，请联系网站管理员');
        }
        $options = json_decode($option['option_value'], true);
        //邮件标题
        $title = $options['title'];
        $uid = session('user.id');
        $username = session('user.user_login');

        $activekey = md5($uid . time() . uniqid());
        $users_model = M("Users");

        $result = $users_model->where(array("id" => $uid))->save(array("user_activation_key" => $activekey));
        if (!$result) {
            $this->error('激活码生成失败！');
        }
        //生成激活链接
        $url = U('user/register/active', array("hash" => $activekey), "", true);
        //邮件内容
        $template = $options['template'];
        $content = str_replace(array('http://#link#', '#username#'), array($url, $username), $template);

        $send_result = sp_send_email(session('user.user_email'), $title, $content);

        if ($send_result['error']) {
            $this->error('激活邮件发送失败，请尝试登录后，手动发送激活邮件！');
        }
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $content 模板输出内容
     * @return mixed
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content, $prefix);
    }

    /**
     * 获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀*
     * @return string
     */
    public function fetch($templateFile = '', $content = '', $prefix = '') {
        $templateFile = empty($content) ? $this->parseTemplate($templateFile) : '';
        return parent::fetch($templateFile, $content, $prefix);
    }

    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate($template = '') {

        $tmpl_path = C("SP_TMPL_PATH");
        define("SP_TMPL_PATH", $tmpl_path);
        if ($this->theme) { // 指定模板主题
            $theme = $this->theme;
        } else {
            // 获取当前主题名称
            $theme = C('SP_DEFAULT_THEME');
            if (C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } elseif (cookie('think_template')) {
                    $theme = cookie('think_template');
                }
                if (!file_exists($tmpl_path . "/" . $theme)) {
                    $theme = C('SP_DEFAULT_THEME');
                }
                cookie('think_template', $theme, 864000);
            }
        }

        $theme_suffix = "";

        if (C('MOBILE_TPL_ENABLED') && sp_is_mobile()) {//开启手机模板支持
            if (C('LANG_SWITCH_ON', null, false)) {
                if (file_exists($tmpl_path . "/" . $theme . "_mobile_" . LANG_SET)) {//优先级最高
                    $theme_suffix = "_mobile_" . LANG_SET;
                } elseif (file_exists($tmpl_path . "/" . $theme . "_mobile")) {
                    $theme_suffix = "_mobile";
                } elseif (file_exists($tmpl_path . "/" . $theme . "_" . LANG_SET)) {
                    $theme_suffix = "_" . LANG_SET;
                }
            } else {
                if (file_exists($tmpl_path . "/" . $theme . "_mobile")) {
                    $theme_suffix = "_mobile";
                }
            }
        } else {
            $lang_suffix = "_" . LANG_SET;
            if (C('LANG_SWITCH_ON', null, false) && file_exists($tmpl_path . "/" . $theme . $lang_suffix)) {
                $theme_suffix = $lang_suffix;
            }
        }

        $theme = $theme . $theme_suffix;

        C('SP_DEFAULT_THEME', $theme);

        $current_tmpl_path = $tmpl_path . $theme . "/";
        // 获取当前主题的模版路径
        define('THEME_PATH', $current_tmpl_path);

        $cdn_settings = sp_get_option('cdn_settings');
        if (!empty($cdn_settings['cdn_static_root'])) {
            $cdn_static_root = rtrim($cdn_settings['cdn_static_root'], '/');
            C("TMPL_PARSE_STRING.__TMPL__", $cdn_static_root . "/" . $current_tmpl_path);
            C("TMPL_PARSE_STRING.__PUBLIC__", $cdn_static_root . "/public");
            C("TMPL_PARSE_STRING.__WEB_ROOT__", $cdn_static_root);
        } else {
            C("TMPL_PARSE_STRING.__TMPL__", __ROOT__ . "/" . $current_tmpl_path);
        }


        C('SP_VIEW_PATH', $tmpl_path);
        C('DEFAULT_THEME', $theme);

        define("SP_CURRENT_THEME", $theme);

        if (is_file($template)) {
            return $template;
        }
        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);

        // 获取当前模块
        $module = MODULE_NAME;
        if (strpos($template, '@')) { // 跨模块调用模版文件
            list($module, $template) = explode('@', $template);
        }

        $module = $module . "/";

        // 分析模板文件规则
        if ('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        } elseif (false === strpos($template, '/')) {
            $template = CONTROLLER_NAME . $depr . $template;
        }

        $file = sp_add_template_file_suffix($current_tmpl_path . $module . $template);
        $file = str_replace("//", '/', $file);
        if (!file_exists_case($file))
            E(L('_TEMPLATE_NOT_EXIST_') . ':' . $file);
        return $file;
    }

    /**
     * 设置错误，成功跳转界面
     */
    private function set_action_success_error_tpl() {
        $theme = C('SP_DEFAULT_THEME');
        if (C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
            if (cookie('think_template')) {
                $theme = cookie('think_template');
            }
        }
        //by ayumi手机提示模板
        $tpl_path = '';
        if (C('MOBILE_TPL_ENABLED') && sp_is_mobile() && file_exists(C("SP_TMPL_PATH") . "/" . $theme . "_mobile")) {//开启手机模板支持
            $theme = $theme . "_mobile";
            $tpl_path = C("SP_TMPL_PATH") . $theme . "/";
        } else {
            $tpl_path = C("SP_TMPL_PATH") . $theme . "/";
        }

        //by ayumi手机提示模板
        $defaultjump = THINK_PATH . 'Tpl/dispatch_jump.tpl';
        $action_success = sp_add_template_file_suffix($tpl_path . C("SP_TMPL_ACTION_SUCCESS"));
        $action_error = sp_add_template_file_suffix($tpl_path . C("SP_TMPL_ACTION_ERROR"));
        if (file_exists_case($action_success)) {
            C("TMPL_ACTION_SUCCESS", $action_success);
        } else {
            C("TMPL_ACTION_SUCCESS", $defaultjump);
        }

        if (file_exists_case($action_error)) {
            C("TMPL_ACTION_ERROR", $action_error);
        } else {
            C("TMPL_ACTION_ERROR", $defaultjump);
        }
    }

    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
    protected function ajaxReturn($status = 1, $msg = '', $data = '') {
        parent::ajaxReturn(array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        ));
    }

    protected function uploadImage($savePath) {
        $uploadInfo = C("UPLOAD_INFO");
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = $uploadInfo['uploadImage']['maxSize']; // 设置附件上传大小
        $upload->exts = $uploadInfo['uploadImage']['exts'];    // 设置附件上传类型
        $upload->rootPath = $uploadInfo['uploadImage']['rootPath']; // 设置附件上传根目录
        $upload->savePath = '/' . $savePath; // 设置附件上传（子）目录
        // 上传文件 
        $info = $upload->upload();
        return $info;
    }

    //移动文件
    protected function moveFile($fileName) {
        if (!empty($fileName)) {
            $fileOpera = new FileOpera();
            $source = TMP_UPLOAD . "/" . $fileName;
            $dest = dirname(TMP_UPLOAD) . "/" . MODULE_NAME . "/" . CONTROLLER_NAME . "/" . $fileName;
            $fileOpera->LK_move($source, $dest); //移动文件
            $fileName = "/data/upload/" . MODULE_NAME . "/" . CONTROLLER_NAME . "/" . $fileName;
            return $fileName;
        }
    }

}
