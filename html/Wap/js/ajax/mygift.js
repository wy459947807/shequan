
//全局对象管理器
var dataInfo={
    config:configInfo,
    giftItem:{},
    pageLimit:12,
    
    
}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.giftItem = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/killerGift");//最新消息
}

