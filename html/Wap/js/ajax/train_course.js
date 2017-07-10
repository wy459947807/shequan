
//全局对象管理器
var dataInfo={
    config:configInfo,
    course:{},
}

initData();//初始化数据

bindTemplate(dataInfo, "course", "course_tpl");//绑定模版

//初始化数据
function initData(){
    var id=dataInfo.config.loacalUrl.get("id");
    if(!id){
        alert("参数错误！");
        history.go(-1);
    }
    dataInfo.course = getRemoteData({id:id}, configInfo.apiUrl+"Course/getOne");//课程详情
    
}

