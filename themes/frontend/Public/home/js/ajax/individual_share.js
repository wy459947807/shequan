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
    emotion:$.circle.util.emotion_bag(),
    alertInfo:{
        type:"",
        gift_id:0,
        gift_img:"",
        expend:0,
        win_coin:configInfo.userInfo.win_coin,
    }
    

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
WEB_SOCKET_SWF_LOCATION = "/public/js/live/WebSocketMain.swf";
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
            $('.talk-box').animate({scrollTop: $('.talk-box')[0].scrollHeight}, 1);  //滚动到底部
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
        //layer.msg(o.msg);
        sendMsg();//发送消息  
    },
    error: function (data) {
    }
};
  

//事件绑定
$(document).ready(function () {
    
    //搜索
    $("#send_msg").click(function () { 
        dataInfo.sendInfo.msg_type=1;
        dataInfo.sendInfo.message=$("#msg_area").val();
        sendMsg();
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
    
    
    //选择礼物
    $(".gift-list li").click(function () {
        dataInfo.alertInfo.gift_id=$(this).attr("data-id");
        dataInfo.alertInfo.type="gift";
        dataInfo.alertInfo.gift_img=$(this).attr("data-img");
        dataInfo.alertInfo.expend=$(this).attr("data-expend");
        /*
        bindTemplate(dataInfo, "alertBox", "alertBox_tpl");//绑定模版
        getLayerTemplate("alertA",'',"520px");*/
    });
    
    //发送礼物
    $("#send-gift").click(function () {
        
        var expendCoin= parseInt($("#bao_sum").html());
        var giftSum = parseInt($("#gift_sum").val());
        if(!expendCoin){
            layer.msg("请选择礼物！");
            return;
        }
        
        if(configInfo.userInfo.win_coin<expendCoin){
            dataInfo.alertInfo.expend=expendCoin;
            bindTemplate(dataInfo, "alertBox", "alertBox_tpl");//绑定模版
            getLayerTemplate("alertB",'',"520px");//赢家宝不足
            return;
        }
       
  
        if(dataInfo.alertInfo.type=="gift"){
            
            for(var i = 0;i < giftSum;i++){
                var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo, {killer_id: dataInfo.killerInfo.id,gift_id:dataInfo.alertInfo.gift_id}), configInfo.apiUrl + "Message/sendGift",1);
                dataInfo.sendInfo.msg_type=4;
                dataInfo.sendInfo.attach_url=dataInfo.alertInfo.gift_img;
                sendMsg(1,0);
            }
            
            var gift = new Image();
            var gift_src = $('.gift-list .selected').children("img").attr("src");
            gift.src = gift_src;
            $(".talk-list").append(gift);
            setTimeout(function(){
                    gift.remove();
            },3000);

        }
        
        /*
        bindTemplate(dataInfo, "alertBox", "alertBox_tpl");//绑定模版
        getLayerTemplate("alertA",'',"520px");*/
    });
    
    
    $(".unread").click(function () {
        $('.talk-box').animate({scrollTop: 0}, 1);  //滚动到顶部
    });
    
    
    $(".unfree").click(function () {
        dataInfo.sendInfo.is_charge=1;
    });
    
    
    $(".free").click(function () {
        dataInfo.sendInfo.is_charge=0;
    });
    

});

function expendCoin(){
    if(dataInfo.alertInfo.type=="gift"){
        var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo, {killer_id: dataInfo.killerInfo.id,gift_id:dataInfo.alertInfo.gift_id}), configInfo.apiUrl + "Message/sendGift",1);
        if(retInfo.status==1){
            dataInfo.sendInfo.msg_type=4;
            dataInfo.sendInfo.attach_url=dataInfo.alertInfo.gift_img;
            sendMsg(1,0);
            layer.closeAll();
        }else{
            layer.closeAll();
            getLayerTemplate("alertB",'',"520px");
        }
    }
}

function checkMsg(val,id){
    var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo, {id: id}), configInfo.apiUrl + "Message/messageInfo",1);
    if(retInfo.status==1){
        $(val).hide();
        $(val).siblings('.info').show();
        var newNum = parseInt($('#new_num').html()); 
        $('#new_num').html(newNum-1);
    }else{
        bindTemplate(dataInfo, "alertBox", "alertBox_tpl");//绑定模版
        getLayerTemplate("alertC","","520px");
    }
}

//发送消息
function sendMsg(step,is_charge){
    /*
    if(!step){
        if(dataInfo.config.userInfo.killer_id==dataInfo.killerInfo.id){
            getLayerTemplate("alertD");
            return;
        }
    }
    layer.closeAll();
    dataInfo.sendInfo.is_charge=0;*/
    dataInfo.sendInfo.reply_id=0;
    dataInfo.sendInfo.to_client_name="";
    if(is_charge){
        dataInfo.sendInfo.is_charge=is_charge
    }
    
    var msgCon=$("#msg_area").val();
    if(msgCon==""&&dataInfo.sendInfo.msg_type==1){
        layer.msg("消息内容不能为空！");
        return;
    }
    
    ws.send(JSON.stringify(dataInfo.sendInfo)); 
 
}

function replyMsg(){
    var replyCon=$("#replyCon").val();
    if(replyCon==""){
        layer.msg("回复内容不能为空！");
        return;
    }
    dataInfo.sendInfo.message=replyCon;
    ws.send(JSON.stringify(dataInfo.sendInfo)); 
    layer.closeAll();
}

function openReply(id,name){
    dataInfo.sendInfo.reply_id=id;
    dataInfo.sendInfo.to_client_name=name;
    getLayerTemplate("alertE",'',"520px");
}

//关注高手
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






