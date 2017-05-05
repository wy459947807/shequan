<?php

namespace Home\Controller;

use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {

    //首页
    public function index() {
        if (!IS_POST) {
            $listInfo = D('killer')->getList(array("status" => 1, "orderType" => 0, "page" => 1, "pageLimit" => 30)); //综合排序

            $tadayTopList = D('killer')->getTopList(array("orderType" => 1, "dateTime" => date("Y-m-d H:i:s"))); //今日推荐
            $gupiaoTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 1)); //股票高手榜
            $qihuoTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 2)); //期货高手榜
            $waihuiTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 3)); //外汇高手榜
            $waipanTopList = D('killer')->getTopList(array("orderType" => 2, "adeptType" => 4)); //外盘高手榜

            if (empty($tadayTopList['data'])) {
                $tadayTopList = D('killer')->getTopList(array("orderType" => 1)); //今日推荐
            }

            $this->assign('tadayTopList', $tadayTopList['data']);    //今日推荐
            $this->assign('gupiaoTopList', $gupiaoTopList['data']);  //股票高手榜
            $this->assign('qihuoTopList', $qihuoTopList ['data']);  //期货高手榜
            $this->assign('waihuiTopList', $waihuiTopList['data']);  //外汇高手榜
            $this->assign('waipanTopList', $waipanTopList['data']);  //外盘高手榜

            $this->assign('killerList', $listInfo['data']['list']);  //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $this->display(":home");
        } else {
            $data = I('post.');
            $listInfo = D('killer')->getList($data);                   //排序列表
            $this->assign('killerList', $listInfo['data']['list']);    //列表信息
            $this->assign('pageInfo', $listInfo['data']['pageInfo']); //分页信息
            $html = $this->fetch(":home_ajax");
            exit($html);
        }
    }

    public function ajaxPage() {
        if (IS_POST) {
            $data = I('post.');
            $this->assign('pageInfo', $data); //分页信息
            $html = $this->fetch(":page");
            exit($html);
        }
    }

    public function test() {
        if (IS_POST) {
            $this->ajaxReturn(200, "666", "");
        } else {
            $this->display(":test");
        }
    }

    //处理文件上传
    public function uploadImg() {
        /**
         * upload.php
         *
         * Copyright 2009, Moxiecode Systems AB
         * Released under GPL License.
         *
         * License: http://www.plupload.com/license
         * Contributing: http://www.plupload.com/contributing
         */
        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Settings
        $targetDir = str_replace('\\', '/', dirname(dirname(__FILE__))) . '/../../data/upload/tmp';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        @set_time_limit(5 * 60);

        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($targetDir))
            @mkdir($targetDir);
        if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory:"'.$targetDir.'}, "id" : "id"}');
        }
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($in);
                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off 
            rename("{$filePath}.part", $filePath);
        }
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }




}
