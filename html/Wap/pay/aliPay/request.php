<?php

/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 * 
 * ================================================================
 */
require('Utils.class.php');
require('config/config.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');
require(dirname(__FILE__) .'/../payBack/PayBack.class.php');

Class Request {

    //$url = 'http://192.168.1.185:9000/pay/gateway';

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;
    private $payBack=null;

    public function __construct() {
        $this->Request();
    }

    public function Request() {
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = new Config();
        $this->payBack = new PayBack();

        $this->reqHandler->setGateUrl($this->cfg->C('url'));
        $this->reqHandler->setKey($this->cfg->C('key'));
    }

    public function index() {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : 'submitOrderInfo';
        switch ($method) {
            case 'submitOrderInfo'://提交订单
                $this->submitOrderInfo();
                break;
            case 'queryOrder'://查询订单
                $this->queryOrder();
                break;
            case 'submitRefund'://提交退款
                $this->submitRefund();
                break;
            case 'queryRefund'://查询退款
                $this->queryRefund();
                break;
            case 'callback':
                $this->callback();
                break;
        }
    }

    /**
     * 提交订单信息
     */
    public function submitOrderInfo() {
        $this->reqHandler->setReqParams($_POST, array('method'));
        $this->reqHandler->setParameter('service', 'pay.alipay.native'); //接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg->C('mchId')); //必填项，商户号，由平台分配
        $this->reqHandler->setParameter('notify_url', $this->cfg->C('notify_url'));
        $this->reqHandler->setParameter('version', $this->cfg->C('version'));
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand())); //随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign(); //创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //var_dump($data);

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    echo json_encode(array('code_img_url' => $this->resHandler->getParameter('code_img_url'),
                        'code_url' => $this->resHandler->getParameter('code_url')));
                    exit();
                } else {
                    echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code') . ' Error Message:' . $this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }

    /**
     * 查询订单
     */
    public function queryOrder() {
        $this->reqHandler->setReqParams($_POST, array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if (empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])) {
            echo json_encode(array('status' => 500,
                'msg' => '请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version', $this->cfg->C('version'));
        $this->reqHandler->setParameter('service', 'unified.trade.query'); //接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg->C('mchId')); //必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand())); //随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign(); //创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                $res = $this->resHandler->getAllParameters();
                Utils::dataRecodes('查询订单', $res);
                //支付成功会输出更多参数，详情请查看文档中的7.1.4返回结果
                echo json_encode(array('status' => 200, 'msg' => '查询结果请查看result.txt文件！', 'data' => $res));
                exit();
            }
            echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }

    /**
     * 提交退款
     */
    public function submitRefund() {
        $this->reqHandler->setReqParams($_POST, array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if (empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])) {
            echo json_encode(array('status' => 500,
                'msg' => '请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version', $this->cfg->C('version'));
        $this->reqHandler->setParameter('service', 'unified.trade.refund'); //接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg->C('mchId')); //必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand())); //随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id', $this->cfg->C('mchId')); //必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign(); //创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters()); //将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    /* $res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                      'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                      'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                      'refund_id'=>$this->resHandler->getParameter('refund_id'),
                      'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                      'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                      'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee')); */
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款', $res);
                    echo json_encode(array('status' => 200, 'msg' => '提交退款成功,请查看result.txt文件！', 'data' => $res));
                    exit();
                } else {
                    echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code') . ' Error Message:' . $this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }

    /**
     * 异步通知回调方法
     */
    public function callback() {
      
        
        $xml = file_get_contents('php://input');
        //$res = Utils::parseXML($xml);
        //file_put_contents('1.txt',$xml);//检测是否执行callback方法，如果执行，会生成1.txt文件，且文件中的内容就是通知参数
        $this->resHandler->setContent($xml);
        //var_dump($this->resHandler->setContent($xml));
        $this->resHandler->setKey($this->cfg->C('key'));
        
        if ($this->resHandler->isTenpaySign()) {
            if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                //echo $this->resHandler->getParameter('status');
                //此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。


                //Utils::dataRecodes('接口回调,返回通知参数', $this->resHandler->getAllParameters());   
                $retArray=$this->resHandler->getAllParameters();
                
                //$order_sn = trim($retArray['attach']);
                $retArray['pay_type']=2;
                $this->payBack->payback($retArray);

                //file_put_contents('2.txt', 1); //如果生成2.txt,说明前一步的输出success是有执行
             
            } else {
                exit("failure");
            }
        } else {
            exit("failure");
        }
    }
    
     

}

$req = new Request();
$req->index();
?>