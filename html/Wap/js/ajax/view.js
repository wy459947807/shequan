
//全局对象管理器
var dataInfo={
    config:configInfo,
    messages:{},
    emotion:$.circle.util.emotion_bag(),
}

initData();//初始化数据
bindTemplate(dataInfo, "messages", "messages_tpl");//绑定模版


//初始化数据
function initData(){
    dataInfo.messages = getRemoteData({pageLimit:20,role:1,msg_type:1,is_charge:0}, configInfo.apiUrl+"Message/getMessages");//最新消息
}

