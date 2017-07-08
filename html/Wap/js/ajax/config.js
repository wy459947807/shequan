
//站点配置信息
var configInfo={
    apiUrl:"http://shequan.10jrw.com/index.php/app/",
    serviceUrl:"http://shequan.10jrw.com/", 
    testUser:{
        jrw_id:"8074",
        user_nicename:"汪勇",
        avatar:"",
        sex:1,
        mobile:"18739178207",
    },
    tokenInfo:{},
    userInfo:{}, 
}

initConfig();//初始化配置信息

function initConfig(){
    configInfo.tokenInfo = getRemoteData(configInfo.testUser,configInfo.apiUrl+"Index/getToken");
    configInfo.userInfo  = getRemoteData(configInfo.tokenInfo,configInfo.apiUrl+"User/userInfo"); 
}