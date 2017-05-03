/**
 * Created by zry on 2017/1/19.
 */
;(function($){
    $.tougu.home = {
        settings: {
            //toggle_lector:".toggle_lector"
        },
        init: function(options){
            options && $.extend($.tougu.home.settings, options);
            $.tougu.home.jump();
        },
        jump:function() {
            $("#today_recommend").find(".swiper-slide").click(function(){
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            $("#home_tab_content").find(".home_item").click(function(){
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            //$("#penxunkecheng").find(".item").click(function(){
            //    window.location.href = "http://www.yingjiaziben.com/mijiangaoshou/kecheng_list.php"
            //});
        }

    }

    $.tougu.home.init(); //用户
})(jQuery);