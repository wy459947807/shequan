
//全局对象管理器
var dataInfo={
    config:configInfo,
    killerRank:{},
    items:{},
    page:1,
    pageLimit:10,
}

initData();//初始化数据
bindTemplate(dataInfo, "killerRank", "killerRank_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.killerRank["gupiao"] = getRemoteData({adeptType:1,pageLimit:dataInfo.pageLimit}, configInfo.apiUrl+"Killer/getRank");  //股票排行
    dataInfo.killerRank["qihuo"] = getRemoteData({adeptType:2,pageLimit:dataInfo.pageLimit}, configInfo.apiUrl+"Killer/getRank");  //期货排行
    dataInfo.killerRank["waihui"] = getRemoteData({adeptType:3,pageLimit:dataInfo.pageLimit}, configInfo.apiUrl+"Killer/getRank");  //外汇排行
}

