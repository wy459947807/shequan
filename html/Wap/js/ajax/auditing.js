
//全局对象管理器
var dataInfo={
    config:configInfo,
    killerInfo:{},
    pageLimit:25,
    adeptArray:{
        1:"股票",
        2:"期货",
        3:"外汇",
        4:"外盘",
    },
    certifyArray:{
        1:"证券交易",
        2:"证券市场基本法律法规",
        3:"证券市场基础知识",
        4:"证券投资顾问业务",
        5:"证券投资咨询业务",
        6:"其它",
    }
}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.killerInfo = getRemoteData(configInfo.tokenInfo, configInfo.apiUrl+"Killer/killerInfo");//我得粉丝
}

