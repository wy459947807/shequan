//全局对象管理器
var dataInfo={
    config:configInfo,
    shareList:{},
    shareItem:{},
    pageLimit:10,

}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.shareList["renqi"] = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit,orderType:3,status:1}), configInfo.apiUrl+"Killer/getKillers");
    dataInfo.shareList["redu"] = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit,orderType:1,status:1}), configInfo.apiUrl+"Killer/getKillers");
    dataInfo.shareList["fensi"] = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit,orderType:2,status:1}), configInfo.apiUrl+"Killer/getKillers");
}

