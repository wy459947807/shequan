//全局对象管理器
var dataInfo = {
    config: configInfo,
    killerInfo: {},
    messageList: {},
    messageItem: {},
    pageLimit: 10,
    giftList: {},
    loginInfo: {
        type: "login",
        room_id: 0,
        client_name: configInfo.userInfo.user_nicename,
        avatar: configInfo.userInfo.avatar,
        role: 2
    },
    sendInfo: {
        type: "say",
        user_id: configInfo.userInfo.id,
        killer_id: configInfo.userInfo.killer_id,
        to_client_id: "all",
        to_client_name: "",
        msg_type: 1,
        attach_url: "",
        message: "",
        is_charge: 0,
        reply_id: 0,
        reply_message: ""
    },

}

initData();//初始化数据
connect();//建立webSoket连接
bindTemplate(dataInfo, "body", "body_tpl");//绑定模版
bindTemplate(dataInfo, "talk_list", "messageList_tpl", 1);//绑定模版

//初始化数据
function initData() {
    var id = dataInfo.config.loacalUrl.get("id");
    if (!id) {
        alert("参数错误！");
        history.go(-1);
    }

    dataInfo.killerInfo = getRemoteData(mergeArray(configInfo.tokenInfo, {id: id}), configInfo.apiUrl + "Killer/killerDetail");
    dataInfo.messageList = getRemoteData(mergeArray(configInfo.tokenInfo, {pageLimit: dataInfo.pageLimit, killer_id: id, reverse: 1}), configInfo.apiUrl + "Message/getMessages");
    dataInfo.giftList = getRemoteData(mergeArray(configInfo.tokenInfo, {pageLimit: 50}), configInfo.apiUrl + "Message/giftList");

    //消息数据
    dataInfo.loginInfo.room_id = id;
    if (configInfo.userInfo.status == 1) {
        dataInfo.loginInfo.role = 1;
    }

}


var ws;
WEB_SOCKET_SWF_LOCATION = "../public/js/live/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
// 连接服务端
function connect() {
    ws = new WebSocket("ws://" + dataInfo.config.webSocket.host + ":7272");
    // 当socket连接打开时，输入用户名
    ws.onopen = onopen;
    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = onmessage;
    ws.onclose = function () {
        // console.log("连接关闭，定时重连");
        connect();
    };
    ws.onerror = function () {
        //console.log("出现错误");
    };
}

// 连接建立时发送登录信息
function onopen() {
    // 登录
    var login_data = JSON.stringify(dataInfo.loginInfo);
    //console.log("websocket握手成功，发送登录数据:"+login_data);
    ws.send(login_data);
}

// 服务端发来消息时
function onmessage(e) {
    //console.log(e.data);
    var data = JSON.parse(e.data);
    if (data == '') {
        return false;
    }
    switch (data['type']) {
        // 服务端ping客户端
        case 'ping':
            ws.send('{"type":"pong"}');
            break;
            // 登录 更新用户列表
        case 'login':
            break;
            // 发言
        case 'say':
            dataInfo.messageList = {list: {0: data}};
            bindTemplate(dataInfo, "talk_list", "messageList_tpl", 1);//绑定模版
            break;
            // 用户退出 更新用户列表
        case 'logout':
            break;
    }
}



//文件上传操作
var options = {
    type: "POST",
    async : false,
    beforeSubmit: function () {
        var loading = layer.load(1, {
            shade: [0.1,'#000'] //0.1透明度的白色背景
        });
    },
    success: function (o) {
        if(o.data.list){
            dataInfo.sendInfo.attach_url=o.data.list[0].local_url;
        }else{
            dataInfo.sendInfo.attach_url=o.data.filename.url;
        }
        layer.closeAll('loading');
        layer.msg(o.msg);
        ws.send(JSON.stringify(dataInfo.sendInfo));//发送消息  
    },
    error: function (data) {
    }
};
  

//事件绑定
$(document).ready(function () {
    
    //搜索
    $("#send_msg").click(function () {
        ws.send(JSON.stringify(dataInfo.sendInfo));
        $('.talk-main').animate({scrollTop: $('.talk-main')[0].scrollHeight}, 1);  //滚动到底部
    });

    $("#msg_area").bind('input propertychange', function () {
        dataInfo.sendInfo.message = $(this).val();
    });

    $(".fileUpload").click(function () {
        dataInfo.sendInfo.msg_type= $(this).attr("data-type");
    });
    
    $(".fileUpload").change(function (event) {
        var formId = $(this).attr("data-form");
        $("#"+formId).ajaxSubmit(options);
    });
});







