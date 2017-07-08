;(function($){
	$.circle.index = {
		settings:{

		},
		init:function(){
			$.circle.index.ban_scroll();
			$.circle.index.recommend_width();
			$.circle.index.rank_tab();
		},
		//头部轮播
		ban_scroll:function(){
			$('.ban-list').slick({
			    dots: true,
			    arrows:false,
			    autoplay: true,
			    autoplaySpeed: 2000
			});
		},
		//高手推荐宽度
		recommend_width:function(){
			var li_width = $('.ace-recommend li').outerWidth(true),
				len = $('.ace-recommend li').length,
				ul_width = li_width*len;
			$('.ace-recommend ul').width(ul_width);
		},
		//高手切换
		rank_tab:function(){
			$(document).on('click','.rank-tab>li',function(){
				var self = $(this),
					n = $('.rank-tab>li').index(self);
				self.addClass('on').siblings('li').removeClass('on');
				self.parents('.ace-rank').find('.rank-item').eq(n).show().siblings('.rank-item').hide();
			})
		}
	};
	$.circle.index.init();
})(jQuery);