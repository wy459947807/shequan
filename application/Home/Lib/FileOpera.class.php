<?php

namespace Home\Lib;

/**
 * 首页
 */
class FileOpera {

    //文件拷贝
    function LK_copy($source, $dest) {
        return @copy($source, $dest) || $this->LK_writeFile($dest, $this->LK_readFile($source), 'wb');
    }

    //文件移动
    function LK_move($source, $dest) {
        if (!is_dir(dirname($dest))) {
            $this->createFolder(dirname($dest));
        }
        if (@copy($source, $dest) || $this->LK_writeFile($dest, $this->LK_readFile($source), 'wb')) {
            $this->LK_del($source);
            return true;
        }
    }

    //文件删除
    function LK_del($var) {
        return strpos($var, '..') === FALSE && is_file($var) && @unlink($var) ? TRUE : FALSE;
    }

    //删除文件夹
    function delDir($dir) {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
        //return strpos($dir, '..') === FALSE && is_dir($dir) && @rmdir($dir) ? TRUE : FALSE;
    }

    function isImg($imgpath) {
        return (strpos($imgpath, '..') !== FALSE || !file_exists($imgpath) || !in_array(Fext($imgpath), array('jpg', 'jpeg', 'bmp', 'gif', 'png')) || (function_exists('getimagesize') && !@getimagesize($imgpath))) ? false : true;
    }

    //文件后缀
    function Fext($filename) {
        return strtolower(trim(substr(strrchr($filename, '.'), 1)));
    }

    //读取文件
    function LK_readFile($filename, $mode = 'rb') {
        strpos($filename, '..') !== FALSE && exit('Access Denied!');
        if ($fp = @ fopen($filename, $mode)) {
            flock($fp, LOCK_SH);
            $filedata = @ fread($fp, filesize($filename));
            fclose($fp);
        }
        return $filedata;
    }

    //写文件
    function LK_writeFile($filename, $content, $mode = 'ab', $chmod = 1) {
        strpos($filename, '..') !== FALSE && exit('Access Denied!');

        $fp = @fopen($filename, $mode);
        if ($fp) {
            flock($fp, LOCK_EX);
            fwrite($fp, $content);
            fclose($fp);
            $chmod && @chmod($filename, 0666);
            return TRUE;
        }
        return FALSE;
    }

//创建文件夹
    function createFolder($path) {
        if (!is_dir($path)) {
            $this->createFolder(dirname($path));
            @mkdir($path);
            @chmod($path, 0777);
            @fclose(@fopen($path . '/index.html', 'w'));
            @chmod($path . '/index.html', 0777);
        }
    }

}
