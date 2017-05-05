/**
 * Created by Administrator on 2017/1/4 0004.
 */
$(function(){
    //这里面的东西都会被清空的
    var href = window.location.href;
    if(href.indexOf("index") != -1){
        $("#nav_one_1").css({"color":"red"});
    }else if(href.indexOf("penxunkecheng") != -1){
        $("#nav_one_2").css({"color":"red"});
    }else if(href.indexOf("user") != -1){
        $("#nav_one_3").css({"color":"red"});
    }else if(href.indexOf("farmerkiller") != -1){
        $("#nav_one_4").css({"color":"red"});
    }else if(href.indexOf("help_page") != -1){
        $("#nav_one_5").css({"color":"red"});
    }else{
        $("#nav_one_1").css({"color":"red"});
    }

    $("#user_list").find("li").click(function(){
        var _index = $(this).index()+1;
        $(this).addClass("act").siblings().removeClass("act");
        $("#usercon_"+_index).show().siblings(".col-md-9").hide();
    });
    $("#help_left_list").find("li").click(function(){
        var _index = $(this).index()+1;
        $(this).addClass("act").siblings().removeClass("act");
        $(".help_content_"+_index).show().siblings(".col-md-9").hide();
    });
    function tanchu(content){
        layer.open({
            title: '信息'
            ,content: content
        });
    }
    $("#user_tunchu").click(function(){
        //询问框
        layer.confirm('您确定要推出么？', {
            btn: ['嗯，退出','我在看看'] //按钮
        });
    });

});
















































































































