/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getTemplate(dataInfo, ajaxUrl, boxId, append) {
    $.ajax({
        url: ajaxUrl,
        type: 'post',
        data: dataInfo,
        dataType: "html",//dataType: "json",
        async: false,
        success: function (res) {
            if(append){
                $(boxId).append(res);
            }else{
                $(boxId).html(res);
            }
        },
        error: function () {
            return ;
        }
    });
}


