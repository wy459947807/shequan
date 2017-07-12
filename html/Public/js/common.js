/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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





function bindTemplate(data, boxId, tempId, append) {
    if (boxId) {
        var list_tpl = template(tempId, data);
        if (!append) {
            $('#' + boxId).html(list_tpl);
        } else {
            $('#' + boxId).append(list_tpl);
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

