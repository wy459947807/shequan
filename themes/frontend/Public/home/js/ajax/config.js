
//站点配置信息
var configInfo={
    loacalUrl : new LG.URL(window.location.href),
    apiUrl:"http://shequan.10jrw.com/index.php/app/",
    serviceUrl:"http://shequan.10jrw.com/", 
    tokenInfo:{},
    userInfo:{}, 
    webSocket:{
        host:"120.26.118.169",
    },
    
}

initConfig();//初始化配置信息

function initConfig(){
    configInfo.userInfo= getRemoteData({},"/index.php/Home/Index/getUserInfo");
    if(!configInfo.userInfo){
        alert("您还没有登录,请先登录！");
        //window.location.href="http://m.10jrw.com/user/login.html?request_uri="+configInfo.loacalUrl.host;
        window.location.href="http://www.10jrw.com/ulogin.html?request_uri="+configInfo.loacalUrl.host;
    }
    
    configInfo.tokenInfo = {
        uid:configInfo.userInfo.id,
        token:configInfo.userInfo.token,
    };
}