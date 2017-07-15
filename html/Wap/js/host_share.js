;
(function ($) {
    $.circle.share = {
        settings: {

        },
        init: function () {
            $.circle.share.share_tab();
            $.circle.share.share_loadmore();
        },
        //高手切换
        share_tab: function () {
            $(document).on('click', '.share-tab>li', function () {
                var self = $(this),
                        n = $('.share-tab>li').index(self);
                self.addClass('on').siblings('li').removeClass('on');
                self.parents('.circle-share').find('div.share-item').eq(n).show().siblings('.share-item').hide();
            })
        },
        share_loadmore: function () {
            $(window).scroll(function () {
                var data_type = $('.share-tab').find('li.on').attr('data-val'),
                    index=$('.share-tab').find('li.on').index(),
                    container = $('.circle-share').find('.share-item').eq(index),
                    page = container.attr('data-page');
  
                if (page == '' || page == null)
                    return !1;
                var pageH = $(document.body).height();
                var scrollT = $(window).scrollTop();
                var winH = $(window).height();
                var stopstatus = container.attr("data-load");
                if (scrollT / (pageH - winH) > 0.95 && stopstatus != 'false') {
                    container.find('.load-more').css('background', '#f1f1f1').text('正在努力加载数据...');
                    page++;
                    container.attr("data-load", "false");
                    $.post(configInfo.apiUrl+"Killer/getKillers", mergeArray(configInfo.tokenInfo,{pageLimit:dataInfo.pageLimit,page:page,orderType:data_type,status:1}), function (result) {
                        if (result.status == 1) {
                            container.attr("data-load", "true");
                            container.attr("data-page", page);
                            container.find('.load-more').css('background', '#fff').text('');
                            var tmpData, str = "";
                            
                            
                            if (result.data.list) {
                                //模版数据处理
                                dataInfo.shareItem = result.data;
                                bindTemplate(dataInfo, "shareItem"+data_type, "shareItem_tpl", 1);//绑定模版
                            }
      
                            if (str != '') {
                                container.find('ul').append(str);
                            } else {
                                container.find('.load-more').css('background', '#f1f1f1').text('没有更多数据').show();
                            }
                        } else {
                            container.attr("data-load", "true");
                            $.circle.tips(tesult.msg);
                        }
                    }, 'json')
                }
            })
        }
    };
    $.circle.share.init();
})(jQuery);

