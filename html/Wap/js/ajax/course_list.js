
//全局对象管理器
var dataInfo={
    config:configInfo,
    courseList:{},
}

initData();//初始化数据
bindTemplate(dataInfo, "courseList", "courseList_tpl");//绑定模版


//初始化数据
function initData(){
    var tempData=configInfo.tokenInfo;
    tempData['pageLimit']=12;
    dataInfo.courseList = getRemoteData(tempData, configInfo.apiUrl+"Course/getList");  //课程列表
}

