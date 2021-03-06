
//全局对象管理器
var dataInfo={
    config:configInfo,
    adverts:{},
    newMessages:{},
    killerRec:{},
    killerShare:{},
    killerRank:{},
    emotion:$.circle.util.emotion_bag(),
}

initData();//初始化数据
bindTemplate(dataInfo, "adverts", "adverts_tpl");//绑定模版
bindTemplate(dataInfo, "newMessages", "newMessages_tpl");//绑定模版
bindTemplate(dataInfo, "killerRec", "killerRec_tpl");//绑定模版
bindTemplate(dataInfo, "killerShare", "killerShare_tpl");//绑定模版
bindTemplate(dataInfo, "killerRank", "killerRank_tpl");//绑定模版
bindTemplate(dataInfo, "ad", "ad_tpl");//绑定模版

//初始化数据
function initData(){
    dataInfo.adverts = getRemoteData({}, configInfo.apiUrl+"Index/getAdverts");  //广告
    dataInfo.newMessages = getRemoteData({pageLimit:5,role:1,msg_type:1,is_charge:0}, configInfo.apiUrl+"Message/getMessages");//最新消息
    dataInfo.killerRec = getRemoteData(configInfo.tokenInfo, configInfo.apiUrl+"Killer/tadayRec");  //高手推荐
    dataInfo.killerShare = getRemoteData(configInfo.tokenInfo, configInfo.apiUrl+"Killer/getKillers");  //圈主分享 
    dataInfo.killerRank["gupiao"] = getRemoteData({adeptType:1}, configInfo.apiUrl+"Killer/getRank");  //高手排行
    dataInfo.killerRank["qihuo"] = getRemoteData({adeptType:2}, configInfo.apiUrl+"Killer/getRank");  //高手排行
    dataInfo.killerRank["waihui"] = getRemoteData({adeptType:3}, configInfo.apiUrl+"Killer/getRank");  //高手排行
    
}


function focusKiller(obj,id){
    var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo, {id: id}), configInfo.apiUrl + "Killer/focusKiller",1);
    layer.msg(retInfo.msg);
    //setTimeout("window.location.reload()",2000);//延时两秒刷新页面
    if(retInfo.status==1){
        if(retInfo.msg.indexOf("取消") > -1){
            $(obj).html("+关注");
        }else{
            $(obj).html("已关注");
        }
    }
}



