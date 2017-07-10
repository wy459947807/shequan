
//全局对象管理器
var dataInfo={
    config:configInfo,
    teacher:{},
    course:{},
}

initData();//初始化数据
bindTemplate(dataInfo, "teacher", "teacher_tpl");//绑定模版
bindTemplate(dataInfo, "course", "course_tpl");//绑定模版

//初始化数据
function initData(){
    var id=dataInfo.config.loacalUrl.get("id");
    if(!id){
        alert("参数错误！");
        history.go(-1);
    }
    dataInfo.teacher = getRemoteData({id:id}, configInfo.apiUrl+"Killer/killerDetail");//老师详情
    dataInfo.course = getRemoteData({killer_id:id}, configInfo.apiUrl+"Course/getList");//课程列表
    
}

