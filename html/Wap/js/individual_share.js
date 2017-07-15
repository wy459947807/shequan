
$('.talk-main').animate({scrollTop: $('.talk-main')[0].scrollHeight}, 1);  //滚动到底部
//滚动事件
$('.talk-main').scroll(function () {
    var container = $('.talk-main'),
        page = container.attr('data-page');
    if (page == '' || page == null)
        return ;
    var pageH = $(document.body).height();
    var scrollT = container.scrollTop();
    var winH = $(window).height();
    var stopstatus = container.attr("data-load");

    if (scrollT==0 && stopstatus != 'false') {
        container.find('.load-more').css('background', '#f1f1f1').text('正在努力加载数据...');
        page++;
        container.attr("data-load", "false");
        $.post(configInfo.apiUrl + "Message/getMessages", mergeArray(mergeArray(configInfo.tokenInfo, {pageLimit: dataInfo.pageLimit,page: page, killer_id: dataInfo.killerInfo.id, reverse: 1})), function (result) {
            if (result.status == 1) {
                container.attr("data-load", "true");
                container.attr("data-page", page);
                container.find('.load-more').css('background', '#fff').text('');
                var tmpData, str = "";
 
                if (result.data.list) {
                    //模版数据处理
                    dataInfo.messageList = result.data;
                    bindTemplate(dataInfo, "talk_list", "messageList_tpl", 2);//绑定模版
                    $('.talk-main').animate({ scrollTop: 5 }, 1);  
                }

                if (str != '') {
                    container.find('ul').append(str);
                } else {
                    container.find('.load-more').css('background', '#f1f1f1').text('没有更多数据').show();
                }
            } else {
                container.attr("data-load", "true");
                $.circle.util.tips(tesult.msg);
            }
        }, 'json');
    }
    
    
})