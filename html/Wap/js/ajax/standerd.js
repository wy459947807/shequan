
//全局对象管理器
var dataInfo={
    config:configInfo,
    killerInfo:{},
    unitList:{
        0:"条",
        1:"天",
        2:"周",
        3:"月",
        4:"季",
        5:"年",
    },
    subscribeInfo:{
        killer_id:0,
        num:1,
        type:0,
    },
    
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
    dataInfo.killerInfo = getRemoteData(mergeArray(configInfo.tokenInfo,{id:id}), configInfo.apiUrl+"Killer/killerDetail");//获取老师详情
    if(!dataInfo.killerInfo.subscribe){
        alert("该老师还没有设置订阅标准，暂时不能订阅！");
        history.go(-1);
    }
    
    dataInfo.subscribeInfo.killer_id=id;
    
}


//事件绑定
$(document).ready(function(){
    //输入事件
    $(".num").bind('input propertychange', function () {
        var re =/^[1-9]+[0-9]*]*$/;
        if (!re.test($(this).val())) {
            layer.msg("请输入大于0的正整数！");
            $(this).val(1);
        }  
        dataInfo.subscribeInfo.num = parseInt($(this).val());
        dataInfo.subscribeInfo.type = parseInt($(this).parents("tr").attr("data-type"));
        
        var coin_item=parseInt($(this).parents("tr").find(".coin_item").html());
        var sum_item=dataInfo.subscribeInfo.num*coin_item;
        $(this).parents("tr").find(".sum_item").html(sum_item);
        $("#sum_yjbao").html(sum_item); 
    });
    
    //点击事件
    $(".check_item").click(function (){
        dataInfo.subscribeInfo.num = parseInt($(this).find(".num").val());
        dataInfo.subscribeInfo.type = parseInt($(this).attr("data-type"));
        var coin_item=parseInt($(this).find(".coin_item").html());
        var sum_item=dataInfo.subscribeInfo.num*coin_item;
        $("#sum_yjbao").html(sum_item); 
    });
    
    $("#pay_yjbao").click(function (){
        var retInfo = getRemoteData(mergeArray(configInfo.tokenInfo,dataInfo.subscribeInfo), configInfo.apiUrl + "User/subscribeKiller",1);
        if(retInfo.status==1){
            layer.msg(retInfo.msg);  
            dataInfo.userInfo  = getRemoteData(configInfo.tokenInfo,configInfo.apiUrl+"User/userInfo"); 
            $("#user_win_coin").html(dataInfo.userInfo.win_coin);

        }else{
            getLayerTemplate("baotip-box");
        }
    });
});