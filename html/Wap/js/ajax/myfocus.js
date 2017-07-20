
//全局对象管理器
var dataInfo={
    config:configInfo,
    fansItem:{},
    pageLimit:25,

}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.fansItem = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userFocus");//我得粉丝
}

