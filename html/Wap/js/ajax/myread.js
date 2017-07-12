
//全局对象管理器
var dataInfo={
    config:configInfo,
    subscribeInfo:{},
    subscribeItem:{},
    pageLimit:8,
    unitList:{
        0:"条",
        1:"天",
        2:"周",
        3:"月",
        4:"季",
        5:"年",
    },
    formData:{}
}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){

    if(dataInfo.config.userInfo.status==1){
        dataInfo.subscribeInfo=getRemoteData(configInfo.tokenInfo, configInfo.apiUrl+"User/getSubscribe");//我的订阅标准
    }
    
    dataInfo.subscribeItem=getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userSubscribe");//我的订阅标准
    
}


//事件绑定
$(document).ready(function(){
    //搜索
    $("#standard_data").click(function(){   
        dataInfo.formData=$("#formSubscribe").serializeObject();
        var retData= getRemoteData(mergeArray(configInfo.tokenInfo,dataInfo.formData), configInfo.apiUrl+"User/setSubscribe",1);//搜索订单
        layer.msg(retData.msg);
    });
});
