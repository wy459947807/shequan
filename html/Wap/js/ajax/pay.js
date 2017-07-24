
//全局对象管理器
var dataInfo={
    config:configInfo,
    order:{},
    payInfo:{},
    parameter:{},
    lock:0,
    
}

initData();//初始化数据

bindTemplate(dataInfo, "body", "body_tpl");//绑定模版

//初始化数据
function initData(){
    var order_sn=dataInfo.config.loacalUrl.get("order_sn");
    if(!order_sn){
        alert("参数错误！");
        history.go(-1);
    }

    var orderData=configInfo.tokenInfo;
    orderData['order_sn']=order_sn;
    dataInfo.order = getRemoteData(orderData, configInfo.apiUrl+"Order/orderDetail");//订单详情
    dataInfo.parameter=getRemoteData({}, configInfo.apiUrl+"Index/index");//公共参数
    dataInfo.payInfo={ 
        body:dataInfo.order.order_name,//订单名称
        out_trade_no:dataInfo.order.order_sn,//订单编号
        total_fee:dataInfo.order.total_money*100,//总金额(不能超过5000000)
        attach:dataInfo.order.order_sn,//附加信息
        mch_create_ip:dataInfo.parameter.ip,//获取ip
        method:"submitOrderInfo",//操作类型
        time_expire:"",	
        time_start:"",	
    }

}



//检测支付状态
window.setInterval(chackOrder,3000);//支付状态检测
function chackOrder(){
    var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo,{order_sn:dataInfo.order.order_sn}), configInfo.apiUrl+"Order/orderDetail");//订单详情
    if(retInfo.status==2&&dataInfo.lock==0){
        dataInfo.lock=1;
        layer.alert("支付成功！", {
           closeBtn: 0
        }, function(){
            window.location.href="/";
        });

    }
} 


//事件绑定
$(document).ready(function(){
    //报名提交
    $("#go_pay").click(function(){
    
        var payType=$("input[name=pay]:checked").val();
        if(payType=="alipay"){
            $.post('pay/aliPay/request.php',dataInfo.payInfo, function (res) {
                if (typeof (res) === 'string') {
                    retData = JSON.parse(res);
                }
                
                if (retData.status === 500) {
                    layer.alert(retData.msg);
                    return;
                }
                
                $("#pay_code_img").attr('src',retData.code_img_url);
                getLayerTemplate("payBox","请打开支付宝客户端扫描二维码支付");
                
            });
        }else if(payType=="weixin"){
            $.post('pay/wechatPay/request.php',dataInfo.payInfo, function (res) {
                if (typeof (res) === 'string') {
                    retData = JSON.parse(res);
                }
                if (retData.status === 500) {
                    layer.alert(retData.msg);
                    return;
                }
              
                $("#pay_code_img").attr('src',retData.code_img_url);
                getLayerTemplate("payBox","请打开微信客户端扫描二维码支付");
                
            });
        }

    });
});


