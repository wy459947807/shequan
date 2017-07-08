;(function($){
	$.circle.share = {
		settings:{

		},
		init:function(){
			$.circle.share.share_tab();
			$.circle.share.share_loadmore();
		},
		//高手切换
		share_tab:function(){
			$(document).on('click','.share-tab>li',function(){
				var self = $(this),
					n = $('.share-tab>li').index(self);
				self.addClass('on').siblings('li').removeClass('on');
				self.parents('.circle-share').find('div.share-item').eq(n).show().siblings('.share-item').hide();
			})
		},
		share_loadmore:function(){
			$(window).scroll(function(){
				var data_type = $('.share-tab').find('li.on').attr('data-val'),
					container = $('.circle-share').find('.share-item').eq(data_type),
					page = container.attr('data-page');
				if(page=='' || page==null) return !1;
				var pageH = $(document.body).height();
                var scrollT = $(window).scrollTop();
                var winH = $(window).height();
                var stopstatus=container.attr("data-load");
                if(scrollT/(pageH-winH)>0.95 && stopstatus != 'false'){
                	container.find('.load-more').css('background','#f1f1f1').text('正在努力加载数据...');
                    page++;
                    container.attr("data-load","false");
                    $.post('url',{padg:page,data_type:data_type},function(result){
                    	if (result.status==1) {
                    		container.attr("data-load","true");
                    		container.attr("data-page",page);
                    		container.find('.load-more').css('background','#fff').text('');
                    		var tmpData,str="";
                    		for(var i = 0;i < result.data.length;i++){
								tmpData = result.data[i];
								str+='<li><span>'+tmpData.no+'</span>';
								str+='<div class="pic"><img src="images/'+tmpData.img+'" alt=""></div>';
								str+='<div class="txt"><strong>'+tmpData.name+'</strong><p>'+tmpData.info+'</p></div></li>';
							}
							if (str != '') {
								container.find('ul').append(str);
							}else{
								container.find('.load-more').css('background','#f1f1f1').text('没有更多数据').show();
							}
                    	}else{
                    		container.attr("data-load","true");
                    		$.circle.tips(tesult.msg);
                    	}
                    },'json')
                }
			})
		}
	};
	$.circle.share.init();
})(jQuery);

