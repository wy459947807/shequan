
//全局对象管理器
var dataInfo={
    config:configInfo,
    order:{},
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
}


//事件绑定
$(document).ready(function(){
    //报名提交
    $("#go_pay").click(function(){
        var payType=$("input[name=pay]:checked").val();
     
        if(payType=="alipay"){
           
        }else if(payType=="weixin"){
            var payInfo={
                body:dataInfo.order.order_name,
                orderNo:dataInfo.order.order_sn,
                total_fee:dataInfo.order.total_money,
                trade_type:"MWEB",
                scene_info:'{"h5_info":{"type":"Android","app_name":"十年赢家网","package_name":"com.bm.shinianjinrong"}}',
            }
            
            var retData= getRemoteData(mergeArray(configInfo.tokenInfo,payInfo), configInfo.apiUrl+"Payment/wechatInfo",1);//提交订单
            
            //window.location.href="https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id="+retData.data.prepayid+"&package="+retData.data.package;
        }
        
        /*
        var orderData=configInfo.tokenInfo;
        orderData['course_ids']={0:dataInfo.course.id};
        orderData['course_num']={0:1};

        var retData= getRemoteData(orderData, configInfo.apiUrl+"Order/submitOrder",1);//提交订单
        if(retData.status==1){
            window.location.href="pay.html?order_sn="+retData.data.order_sn;
        }else{
            layer.msg(retData.msg);
        }*/

    });
});


