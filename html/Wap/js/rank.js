;(function($){
	$.circle.rank = {
		settings:{

		},
		init:function(){
			$.circle.rank.rank_tab();
			$.circle.rank.rank_loadmore();
		},
		//高手切换
		rank_tab:function(){
			$(document).on('click','.rank-tab>li',function(){
				var self = $(this),
					n = $('.rank-tab>li').index(self);
				self.addClass('on').siblings('li').removeClass('on');
				self.parents('.ace-rank').find('div.rank-item').eq(n).show().siblings('.rank-item').hide();
			})
		},
		rank_loadmore:function(){
			$(window).scroll(function(){
				var data_type = $('.rank-tab').find('li.on').attr('data-val'),
					container = $('.rank-main').find('.rank-item').eq(data_type),
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
		},
		view_loadmore:function(){
			$(window).scroll(function(){
				var container = $('.new-view'),
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
                    },'json');
                }
			})
		}
	};
	$.circle.rank.init();
})(jQuery);

