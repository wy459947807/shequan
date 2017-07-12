
//全局对象管理器
var dataInfo={
    config:configInfo,
    order:{},
    payType:{
        1:'未支付',
        2:'支付宝',
        3:'微信',
        4:'网上银行',
        5:'苹果支付',
    },
}

initData();//初始化数据
bindTemplate(dataInfo, "userInfo", "userInfo_tpl");//绑定模版
bindTemplate(dataInfo, "order", "order_tpl");//绑定模版

//初始化数据
function initData(){
    var order_sn=dataInfo.config.loacalUrl.get("order_sn");
    if(!order_sn){
        alert("参数错误！");
        history.go(-1);
    }
    dataInfo.order = getRemoteData(mergeArray(configInfo.tokenInfo,{order_sn:order_sn}), configInfo.apiUrl+"Order/orderDetail");//订单详情
}

