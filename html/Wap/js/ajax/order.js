
//全局对象管理器
var dataInfo={
    config:configInfo,
    course:{},
}

initData();//初始化数据

bindTemplate(dataInfo, "course", "course_tpl");//绑定模版

//初始化数据
function initData(){
    var id=dataInfo.config.loacalUrl.get("id");
    if(!id){
        alert("参数错误！");
        history.go(-1);
    }
    dataInfo.course = getRemoteData({id:id}, configInfo.apiUrl+"Course/getOne");//课程详情
}


//事件绑定


$(document).ready(function(){
    //报名提交
    $("#go_pay").click(function(){
        if (!$('.agree input').is(':checked')) {
            layer.msg('请先同意网络服务条款！');
            return;
        }
        var orderData=configInfo.tokenInfo;
        orderData['course_ids']={0:dataInfo.course.id};
        orderData['course_num']={0:1};

        var retData= getRemoteData(orderData, configInfo.apiUrl+"Order/submitOrder",1);//提交订单
        if(retData.status==1){
            window.location.href="pay.html?order_sn="+retData.data.order_sn;
        }else{
            layer.msg(retData.msg);
        }

    });
});


