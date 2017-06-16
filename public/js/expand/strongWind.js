/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
                    layer.alert(data.msg, {title: '温馨提示', icon: 1});

                    if (url) {
                        window.location = url;
                    } else {
                        //window.location.reload();
                    }
                } else {
                    layer.alert(data.msg, {title: '温馨提示', icon: 2});
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

    var layerName = layer.open({
        type: 1,
        title: title,
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


/**
 * 获取url参数值
 * @param {string} url 需要获取参数的url
 * @param {string} name 需要获取的参数名
 * @return {string} 返回获取的参数名
 */
function getUrlParam(name, url) {
    if (!name) {
        return '';
    }
    url = url || location.search;
    name = name.replace(/(?=[\\^$*+?.():|{}])/, '\\');
    var reg = new RegExp('(?:[?&]|^)' + name + '=([^?&#]*)', 'i');
    var match = url.match(reg);
    return !match ? '' : match[1];
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

/**
 * 提示框
 * @returns {undefined}
 */
function layer_tip(reInfo, refresh) {

    if (!reInfo)
        return;
    var msg = "出现错误，请重试！%>_<%";
    var icon = 2;
    if (reInfo) {
        msg = reInfo.msg ? reInfo.msg : "您无权操作！";
        icon = reInfo.status == 200 ? 1 : 2;
    }

    layer.alert(msg, {
        title: '温馨提示',
        icon: icon
    });

    if (refresh) {
        setTimeout("window.location.reload()", 1500);
    }

}

function getRemoteData(dataInfo, ajaxUrl, refresh) {
    var retData = {}
    $.ajax({
        url: ajaxUrl,
        type: 'post',
        data: dataInfo,
        dataType: "json", //dataType: "html",
        async: false,
        success: function (res) {
            layer_tip(res, refresh);
            retData = res;
        },
        error: function () {
            return;
        }
    });

    return retData;
}

function getTemplate(dataInfo, ajaxUrl, boxId, append) {
    var method = "post";
    if (dataInfo['method']) {
        method = dataInfo['method'];
        delete  dataInfo['method'];
    }

    $.ajax({
        url: ajaxUrl,
        type: method,
        data: dataInfo,
        dataType: "html", //dataType: "json",
        async: false,
        success: function (res) {
            if (append) {
                $(boxId).append(res);
            } else {
                $(boxId).html(res);
            }
        },
        error: function () {
            return;
        }
    });
}

function initUploadImage(uploader, imageId, inputId) {
    var uploadPath = "/data/upload/tmp/";
    var uploaderA = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: uploader, // you can pass in id...
        url: "index.php?g=api&m=plupload&a=index",
        max_file_size: '4mb',
        unique_names: true,
        filters: [{
                title: "文件类型(jpg,jpeg,png,gif)",
                extensions: "jpg,jpeg,png,gif"
            }],
        flash_swf_url: '__TMPL__Public/home/lib/plupload/Moxie.swf',
        silverlight_xap_url: '__TMPL__Public/home/lib/plupload/Moxie.xap'
    });
    uploaderA.bind('Init', function (up, params) {
    });
    uploaderA.init();
    uploaderA.bind('FileUploaded', function (up, file) {

        $('#' + imageId).attr('src', uploadPath + plupload.xmlEncode(file.target_name)).show();
        $('#' + inputId).val(plupload.xmlEncode(file.target_name));

    });
    uploaderA.bind('FilesAdded', function (up, files) {
        uploaderA.start();
        e.preventDefault();
    });
}


//上传到云端
function uploadCloud(uploader) {
    var uploadPath = "/data/upload/tmp/";
    var uploaderA = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: uploader, // you can pass in id...
        url: "index.php?g=api&m=cloud&a=uploadCloud",
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
        
        /*
         res={
            status:"200",
            msg:'上传成功！',
            data:{
               file:{
                  hash:"FlTC8aHrbxLWgaXHB4QhpVAM7gKt",
                  key:"592a648858f7e.jpg",
                  url:"http://oqhjh5opr.bkt.clouddn.com/592a648858f7e.jpg",
               }
            }
         }*/
    });
    
    uploaderA.bind('UploadProgress', function (up, files) {
        var percent = files.percent; 
        //layer.alert("正在上传中：进度："+percent+"%");
        var loading = layer.load(1, {
            shade: [0.1,'#000'] //0.1透明度的白色背景
        });

    });
    
    uploaderA.bind('FilesAdded', function (up, files) {
        uploaderA.start();
        //e.preventDefault();
    });
}


//上传到云端
function uploadCloudVideo(uploader) {
    var uploadPath = "/data/upload/tmp/";
    var uploaderA = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: uploader, // you can pass in id...
        url: "index.php?g=api&m=cloud&a=uploadCloudVideo",
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
        var loading = layer.load(1, {
            shade: [0.1,'#000'] //0.1透明度的白色背景
        });

    });
    
    uploaderA.bind('FilesAdded', function (up, files) {
        uploaderA.start();
        //e.preventDefault();
    });
}

//加载视频插件
function initVideo(videoId,src){
    var videoType= src.substring(src.lastIndexOf('.') + 1);
    if(videoType=="m3u8"){
        var flashvars={
		f:'public/js/expand/ckplayer/m3u8/m3u8.swf',
		a:src,
		s:4,
		c:0,
		//i:'http://www.ckplayer.com/static/images/cqdw.jpg'
		};
	var video=[src];
	CKobject.embed('public/js/expand/ckplayer/ckplayer.swf','a1','ckplayer_a1','100%','100%',false,flashvars,video)	
    }else{
        var flashvars={
            f:src,
            c:0
        };
        var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
        CKobject.embedSWF('public/js/expand/ckplayer/ckplayer.swf',videoId,'ckplayer_a1','100%','100%',flashvars,params);
    }
}


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


