/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var serviceUrl="http://shequan.10jrw.com/";//远程服务器地址
var stateInfo = {
    curObj: new Object(), //当前元素对象
    isLock: 0, //锁定当前操作
}
var windowList = {};//窗口句柄列表

//初始化页面控件
$(document).ready(function () {
    formInit(".form");        //初始化表单
    $(".layer_close").click(function () {
        layer.closeAll();
    });

});

//获取远程数据
function getRemoteData(dataInfo, ajaxUrl, isInfo) {
    
    var method = "post";//默认post提交方式
    if (dataInfo['method']) {
        method = dataInfo['method'];
        delete  dataInfo['method'];
    }

    var retData = {}
    $.ajax({
        url: ajaxUrl,
        type: method,
        data: dataInfo,
        dataType: "json", //dataType: "html",
        async: false,
        success: function (res) {
            if (isInfo) {
                retData = res;
            } else {
                retData = res.data;
            }
        },
        error: function () {
            return;
        }
    });

    return retData;
}

//格式话日期
template.helper('dateFormat', function(dateTime, formatTime) {
    
    var reg = /^\+?[1-9][0-9]*$/;
    if(reg.test(dateTime)){
        return moment(parseInt(dateTime)*1000).format(formatTime);
    }
    
    return moment(dateTime).format(formatTime);
    
});


//格式化网址
template.helper('urlFormat', function(url) {
    var reg = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/;
    if(reg.test(url)){
        return url;
    }
    return serviceUrl+url;
});




function bindTemplate(data, boxId, tempId, append) {
    if (boxId) {
        var list_tpl = template(tempId, data);
        if (!append) {
            $('#' + boxId).html(list_tpl);
        } else if(append==1) {
            $('#' + boxId).append(list_tpl);
        }else{
            $('#' + boxId).prepend(list_tpl);
        }
    }
}

//计算数组长度
function count(obj) {
    var objType = typeof obj;
    if (objType == "string") {
        return obj.length;
    } else if (objType == "object") {
        var objLen = 0;
        for (var i in obj) {
            objLen++;
        }
        return objLen;
    }
    return false;
}

//合并数组
function mergeArray(arrayA,arrayB){
    var tempArray={};
    for(index in arrayA){
        tempArray[index]=arrayA[index]
    }
    
    for(index in arrayB){
        tempArray[index]=arrayB[index]
    }
    return tempArray;
}


//获取表单数据
$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


//上传到本地
function uploadLocal(uploader) {
    var uploaderA = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: uploader, // you can pass in id...
        url: serviceUrl+"index.php/app/Index/uploadLocal",
        max_file_size: '100mb',
        unique_names: true,
        filters: [{
                title: "文件类型(*)",
                extensions: "*"
            }],
        flash_swf_url: '__TMPL__Public/home/lib/plupload/Moxie.swf',
        silverlight_xap_url: '__TMPL__Public/home/lib/plupload/Moxie.xap'
    });
    uploaderA.bind('Init', function (up, params) {
    });
    uploaderA.init();
    uploaderA.bind('FileUploaded', function (up, file, responseObject) { 
        var res = JSON.parse(responseObject.response);//获取服务器返回数据
        plupload_callback(uploader,res);//回调函数
    });
    
    uploaderA.bind('UploadProgress', function (up, files) {
        var percent = files.percent; 
        //layer.alert("正在上传中：进度："+percent+"%");
    });
    
    uploaderA.bind('UploadFile', function (up, files) {
        var loading = layer.load(1, {
            shade: [0.1,'#000'] //0.1透明度的白色背景
        });
    });
    
    uploaderA.bind('FilesAdded', function (up, files) {
        uploaderA.start();
        //e.preventDefault();
    });
}


/**
 * 弹窗
 * @param {type} winId 窗口ID
 * @param {type} width 窗口宽度
 * @param {type} height 窗口高度
 * @returns {html}
 */
function getLayerTemplate(winId, title, width, zIndex) {
    if (!zIndex) {
        zIndex = 1000;
    }
    if (!width) {
        width = "90%";
    }
    
    var closeBtn=1;
    if(!title){
        title=false;
        closeBtn=0;
    }
    

    var layerName = layer.open({
        type: 1,
        title: title,
        closeBtn: closeBtn,
        shadeClose: true,
        zIndex: zIndex,
        shade: 0.3,
        area: [width],
        //area: [width + 'px'],
        content: $('#' + winId)
    });
    //layer.iframeAuto(layerName);
    //layer.autoArea(layerName);

    if (winId) {
        windowList[winId] = layerName;//注册窗口
    }
}

/**
 * 关闭指定窗口
 * @param {type} windowId
 * @returns {undefined}
 */
function closeWindow(windowId) {
    layer.close(windowList[windowId]);
}

//页面跳转
function jumpPage(dataInfo) {
    var myurl = new LG.URL(window.location.href);

    for (var index in dataInfo) {
        myurl.set(index, dataInfo[index]);
    }

    //alert (myurl.url());
    window.location.href = myurl.url();
}


//加载视频插件
function initVideo(videoId,src){
    var videoType= src.substring(src.lastIndexOf('.') + 1);
    if(videoType=="m3u8"){
        var flashvars={
		f:'../Public/js/ckplayer/m3u8/m3u8.swf',
		a:src,
		s:4,
		c:0,
		//i:'http://www.ckplayer.com/static/images/cqdw.jpg'
		};
	var video=[src];
	CKobject.embed('../Public/js/ckplayer/ckplayer.swf','a1','ckplayer_a1','100%','100%',false,flashvars,video)	
    }else{
        var flashvars={
            f:src,
            c:0
        };
        var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
        CKobject.embedSWF('../Public/js/ckplayer/ckplayer.swf',videoId,'ckplayer_a1','100%','100%',flashvars,params);
    }
}


/*******************************以下为表单处理***********************************/

//表单自定义验证
var datatype = {
    "price": /^\d+(?:\.\d{1,2})?$/,
    "intNum": /^[1-9]\d*$/,
    "time": /^(([01]?[0-9])|(2[0-3])):[0-5]?[0-9]$/,
    "dateCompar": function (gets, obj, curform, regxp) {

        if (gets == "") {
            //$.Tipmsg.w['dateCompar'] = "请选择日期！";
            return false;
        }
        var begintime = new Date(curform.find("input[name='begintime']").val());
        var endtime = new Date(curform.find("input[name='endtime']").val());
        if (begintime > endtime) {
            //$.Tipmsg.w['dateCompar'] = "结束日期不能小于开始日期！";
            return false;
        }
        return true;
    }
}


//初始化表单
function formInit(formId, url, noAjax) {
    var ajaxPost = true;
    if (noAjax) {
        ajaxPost = false;
    }
    $(formId).Validform({
        tipSweep: true,
        tiptype: function (msg, o, cssctl) {
            if (!o.obj.is("form")) {
                if (o.type == 3) {
                    layer.tips(msg, o.obj, {time: 2000, tips: [3, '#c00']});
                }
            }
        },
        datatype: datatype,
        beforeSubmit: function (re) {
        },
        ajaxPost: ajaxPost,
        callback: function (data) {
            if (ajaxPost) {
                if (data.status == 200) {
                    
                    //layer.alert(data.msg, {title: '温馨提示', icon: 1});
                    layer.msg(data.msg);
                    if (url) {
                        window.location = url;
                    } else {
                        //window.location.reload();
                    }
                } else {
                    //layer.alert(data.msg, {title: '温馨提示', icon: 2});
                    layer.msg(data.msg);
                }
            }

            //window.location.reload();
        },
    });

}


function callBackForm(formId, template, display, append) {
    $(formId).Validform({
        tipSweep: true,
        tiptype: function (msg, o, cssctl) {
            if (!o.obj.is("form")) {
                if (o.type == 3) {
                    layer.tips(msg, o.obj, {time: 2000, tips: [3, '#c00']});
                }
            }
        },
        datatype: datatype,
        beforeSubmit: function (re) {
        },
        ajaxPost: true,
        callback: function (data) {
            if (!display) {
                display = "return";
            }
        },
    });
}
