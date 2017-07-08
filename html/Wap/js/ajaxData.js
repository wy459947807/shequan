/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//全局对象管理器
var dataInfo={
    question:{},
    subject:{},
    answers:{},
    user:{},
    result:{}
}
//初始化用户信息
$.cookie('user',"");
dataInfo.user=Weixin.user;//初始化用户信息
initData();//初始化数据
bindTemplate(dataInfo, "test", "test_tpl");//模版绑定 
//初始化数据
function initData(){
    //获取问题信息
    dataInfo.question = getRemoteData({id:4}, "/index.php/Home/index/getQuestion");
    //获取题目列表
    dataInfo.subject=getRemoteData({question_id:4},"/index.php/Home/index/getSubject");
}
//选择答案
function answer_subject(val){
    //alert($(val).parents("dl").find("dd").index($(val).parent().parent()));
    var item= $(val).parents("dl").find("dd").index($(val));
    var index= $(val).attr("data-id");
    dataInfo.answers[index]=item;
}
//重置答案
function reset_answers(){
    dataInfo.answers={};
}
//提交答案
function submit_answer(){
    var data={
        question_id:dataInfo.question.id,
        user_id:dataInfo.user.id,
        options:dataInfo.answers
    };
    //alert(dataInfo.answers[0]);
    dataInfo.subject=getRemoteData(data,"/index.php/Home/index/submitAnswer");
    getResult();
}
//获取结果
function getResult(){
    dataInfo.result=getRemoteData({user_id:dataInfo.user.id},"/index.php/Home/index/getResult");
    bindTemplate(dataInfo, "result", "result_tpl");//模版绑定
}


