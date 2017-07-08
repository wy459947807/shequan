<?php
!defined('IN_APP') && exit('Access Denied');
define('UC_API', 'http://graph.10jrw.com/UCenter/synlogin');  //服务端请求地址 [同步登陆 同步登出的请求地址都是这一个]
define('UC_IP', '');
class base {

	var $time;
	var $cache = array();

	function __construct() {
		$this->base();
	}

	function base() {
		$this->init_var();
	}

	function init_var() {
		$this->time = time();
	}

	

    function uc_api_post($module, $action, $arg = array()) {
        $s = $sep = '';
        foreach($arg as $k => $v) {
            $k = urlencode($k);
            if(is_array($v)) {
                $s2 = $sep2 = '';
                foreach($v as $k2 => $v2) {
                    $k2 = urlencode($k2);
                    $s2 .= "$sep2{$k}[$k2]=".urlencode(uc_stripslashes($v2));
                    $sep2 = '&';
                }
                $s .= $sep.$s2;
            } else {
                $s .= "$sep$k=".urlencode(uc_stripslashes($v));
            }
            $sep = '&';
        }
        $postdata = $this->uc_api_requestdata($module, $action, $s);
        return $this->uc_fopen2(UC_API, 500000, $postdata, '', TRUE, UC_IP, 20);
    }


    function uc_api_requestdata($module, $action, $arg='', $extra='') {
        $input = $this->uc_api_input($arg);
        $post = "ucm=$module&uca=$action&inajax=2&release=".UC_CLIENT_RELEASE."&input=$input&appid=".$this->appid.$extra;
        return $post;
    }


    function uc_api_input($data) {
        $s = urlencode(authcode($data.'&agent='.md5($_SERVER['HTTP_USER_AGENT'])."&time=".time(), 'ENCODE', $this->appsecret));
        return $s;
    }


    function uc_fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
        $__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
        if($__times__ > 2) {
            return '';
        }
        $url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
        return $this->uc_fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
    }


    function uc_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
        $return = '';
        $matches = parse_url($url);
        !isset($matches['host']) && $matches['host'] = '';
        !isset($matches['path']) && $matches['path'] = '';
        !isset($matches['query']) && $matches['query'] = '';
        !isset($matches['port']) && $matches['port'] = '';
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        if($post) {
            $out = "POST $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= 'Content-Length: '.strlen($post)."\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cache-Control: no-cache\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
            $out .= $post;
        } else {

            $out = "GET $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }

        if(function_exists('fsockopen')) {
            $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        } elseif (function_exists('pfsockopen')) {
            $fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        } else {
            $fp = false;
        }

        if(!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if(!$status['timed_out']) {
                while (!feof($fp)) {
                    if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                        break;
                    }
                }

                $stop = false;
                while(!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                    $return .= $data;
                    if($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }
            @fclose($fp);
            return $return;
        }
    }
}

?>