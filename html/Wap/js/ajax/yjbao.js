
//全局对象管理器
var dataInfo={
    config:configInfo,
    courseList:{},
    searchData:{},
    courseItem:{},
    subscribes:{},
    pageLimit:3,
    subscribeItem:{},
    unitList:{
        0:"条",
        1:"天",
        2:"周",
        3:"月",
        4:"季",
        5:"年",
    },
    subscribeType:'',
    subscribeUrl:'',
   
}

initData();//初始化数据
bindTemplate(dataInfo, "userInfo", "userInfo_tpl");//绑定模版
bindTemplate(dataInfo, "courseList", "courseList_tpl");//绑定模版
bindTemplate(dataInfo, "subscribes", "subscribes_tpl");//绑定模版
bindTemplate(dataInfo, "nav", "nav_tpl");//绑定模版
//初始化数据
function initData(){
    //dataInfo.test= mergeArray(configInfo.tokenInfo,{aaa:"555",bbb:"666"});

    dataInfo.courseList = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userCourse");//最新消息
    
    if(dataInfo.config.userInfo.status==1){
        dataInfo.subscribeType="获得";
        dataInfo.subscribeUrl=configInfo.apiUrl+"User/killerSubscribe";
    }else{
        dataInfo.subscribeType="消耗"; 
        dataInfo.subscribeUrl=configInfo.apiUrl+"User/userSubscribe";
    }
    
    dataInfo.subscribes = getRemoteData(mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit}),dataInfo.subscribeUrl);//最新消息
}


//事件绑定
$(document).ready(function(){
    //搜索
    $(".serch-btn").click(function(){ 
        $('.mybao-info').attr('data-page',1);
        dataInfo.searchData=$("#formSearch").serializeObject();
        var postData=mergeArray(configInfo.tokenInfo,dataInfo.searchData);
        var retData= getRemoteData(mergeArray(postData,{pageLimit:dataInfo.pageLimit}), configInfo.apiUrl+"User/userCourse",1);//搜索订单
        if(retData.status==1){
            dataInfo.courseList=retData.data;
            bindTemplate(dataInfo, "courseList", "courseList_tpl");//绑定模版
        }else{
            layer.msg(retData.msg);
        }
    });
});