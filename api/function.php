<?php
function daddslashes($string, $force = 0, $strip = FALSE) {
    if(!get_magic_quotes_gpc() || $force) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}


function getgpc($k, $var='R') {
    switch($var) {
        case 'G': $var = &$_GET; break;
        case 'P': $var = &$_POST; break;
        case 'C': $var = &$_COOKIE; break;
        case 'R': $var = &$_REQUEST; break;
    }
    return isset($var[$k]) ? $var[$k] : NULL;
}


function uc_stripslashes($string) {
    if(get_magic_quotes_gpc()) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = uc_stripslashes($val);
            }
        } else {
            $string=stripslashes($string);
        }
    }
    return $string;
}



function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	 $ckey_length = 4;
	 $key = md5($key != '' ? $key : "ae3c14da832e6d8d");
	 $keya = md5(substr($key, 0, 16));
	 $keyb = md5(substr($key, 16, 16));
	 $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	 $cryptkey = $keya.md5($keya.$keyc);
	 $key_length = strlen($cryptkey);

	 $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	 $string_length = strlen($string);

	 $result = '';
	 $box = range(0, 255);

	 $rndkey = array();
	 for($i = 0; $i <= 255; $i++) {
		  $rndkey[$i] = ord($cryptkey[$i % $key_length]);
	 }

	 for($j = $i = 0; $i < 256; $i++) {
		  $j = ($j + $box[$i] + $rndkey[$i]) % 256;
		  $tmp = $box[$i];
		  $box[$i] = $box[$j];
		  $box[$j] = $tmp;
	 }

	 for($a = $j = $i = 0; $i < $string_length; $i++) {
		  $a = ($a + 1) % 256;
		  $j = ($j + $box[$a]) % 256;
		  $tmp = $box[$a];
		  $box[$a] = $box[$j];
		  $box[$j] = $tmp;
		  $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	 }

	 if($operation == 'DECODE') {
		  if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
		  } else {
				return '';
		  }
	 } else {
		  return $keyc.str_replace('=', '', base64_encode($result));
	 }
}
?>