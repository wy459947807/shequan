
//站点配置信息
var configInfo={
    loacalUrl : new LG.URL(window.location.href),
    apiUrl:"http://shequan.10jrw.com/index.php/app/",
    serviceUrl:serviceUrl, 
    loginUser:{
        jrw_id:"1252",
        user_nicename:"测试帐号",
        avatar:"",
        sex:1,
        mobile:"18739178217",
    },
    tokenInfo:{},
    userInfo:{}, 
    webSocket:{
        host:"120.26.118.169",
    },
    
}

initConfig();//初始化配置信息

function initConfig(){
    //configInfo.tokenInfo = getRemoteData(configInfo.testUser,configInfo.apiUrl+"Index/getToken");
    configInfo.loginUser= getRemoteData({},"api/userInfo.php");
    if(configInfo.loginUser!=""){
        $.cookie('user', JSON.stringify(configInfo.loginUser));
    }
    
    if ($.cookie('user')) {
        configInfo.loginUser = JSON.parse($.cookie('user'));
    }  
 
    configInfo.tokenInfo = getRemoteData(configInfo.loginUser,configInfo.apiUrl+"Index/getToken");
    if(!configInfo.tokenInfo){
        alert("您还没有登录,请先登录！");
        window.location.href="http://m.10jrw.com/user/login.html?request_uri="+configInfo.loacalUrl.host;
        //window.location.href="http://www.10jrw.com/ulogin.html";
    }
    configInfo.userInfo  = getRemoteData(configInfo.tokenInfo,configInfo.apiUrl+"User/userInfo"); 
}