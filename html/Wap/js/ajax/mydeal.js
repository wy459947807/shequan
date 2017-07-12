
//全局对象管理器
var dataInfo={
    config:configInfo,
    orderList:{},
    orderItems:{},
    searchData:{},
    courseItem:{},
    pageLimit:10,
}

initData();//初始化数据
bindTemplate(dataInfo, "userInfo", "userInfo_tpl");//绑定模版
bindTemplate(dataInfo, "orderList", "orderList_tpl");//绑定模版


//初始化数据
function initData(){
    //dataInfo.test= mergeArray(configInfo.tokenInfo,{aaa:"555",bbb:"666"});
    dataInfo.orderList = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userOrder");//最新消息
}


//事件绑定
$(document).ready(function(){
    //搜索
    $(".serch-btn").click(function(){ 
        $('.deal-info').attr('data-page',1);
        dataInfo.searchData=$("#formSearch").serializeObject();
        var postData=mergeArray(configInfo.tokenInfo,dataInfo.searchData);
        var retData= getRemoteData(mergeArray(postData,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userOrder",1);//搜索订单
        if(retData.status==1){
            dataInfo.orderList=retData.data;
            bindTemplate(dataInfo, "orderList", "orderList_tpl");//绑定模版
        }else{
            layer.msg(retData.msg);
        }
    });
});