
//全局对象管理器
var dataInfo={
    config:configInfo,
    videoInfo:{},
}

initData();//初始化数据
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版


//初始化数据
function initData(){
    var id=dataInfo.config.loacalUrl.get("id");
    if(!id){
        alert("参数错误！");
        history.go(-1);
    } 
    dataInfo.videoInfo = getRemoteData(mergeArray(configInfo.tokenInfo,{id:id}), configInfo.apiUrl+"Course/getOne");//课程详情
}

$(document).ready(function () {
    initVideo("a1",dataInfo.videoInfo.video);
});

