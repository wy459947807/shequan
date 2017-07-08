;(function($){
	$.circle.user = {
		settings:{

		},
		init:function(){
			
		},
		//我的订阅切换
		read_tab:function(){
			$('.read-nav li').click(function(){
    			var n=$('.read-nav li').index($(this));
    			$(this).addClass('on').siblings('li').removeClass('on');
    			$(this).parents('.read-main').find('.item').eq(n).show().siblings('.item').hide();
    		})
		},
		//提交订阅标准数据
		send_data:function(){
			$(document).on('click','#standard_data',function(){
				var one = $.trim($('input[data-id="one"]').val());
				var day = $.trim($('input[data-id="day"]').val());
				var week = $.trim($('input[data-id="week"]').val());
				var month = $.trim($('input[data-id="month"]').val());
				var quarter = $.trim($('input[data-id="quarter"]').val());
				var year = $.trim($('input[data-id="year"]').val());
				if ($.circle.util.empty(one)&&$.circle.util.empty(day)&&$.circle.util.empty(week)&&$.circle.util.empty(month)&&$.circle.util.empty(quarter)&&$.circle.util.empty(year)) {
					$.circle.util.tips('请至少填写一个数据');
					return !1;
				}
				$.post('url',{},);
			})
		},
		//上拉加载更多我的粉丝
		fan_loadmore:function(){
			$(window).scroll(function(){
				var container = $('.fans-main'),
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
                    $.post('url',{padg:page},function(result){
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
                    		$.circle.util.tips(tesult.msg);
                    	}
                    },'json');
                }
			})
		},
		//赢家宝页面切换
		bao_tab:function(){
			$(document).on('click','.bao-nav li',function(){
				$(this).addClass('on').siblings('li').removeClass('on');
				var n=$('.bao-nav li').index($(this));
				$(this).parents('.bao-main').find('.bao-item').eq(n).show().siblings('.bao-item').hide();
			})
		},
		// 按日期搜索我购买的课程
		search_course:function(){
			$(document).on('click','.serch-btn',function(){
				var self = $(this),
				par = self.parents('.search');
				var start_time = par.find('input#begin_time').val();
				var end_time = par.find('input#end_time').val();
				if (start_time==''||end_time=='') {
					$.circle.util.tips('请选择日期！');
					return !1;
				}
				$.post('url',{u_id:u_id,start_time:start_time,end_time:end_time},function(status){
					if (result.status==1) {

					}else{
						$.circle.util.tips(result.msg);
					}
				},'json');
			})
		},
		//加载更多我的赢家宝
		yjbao_load:function(){
			$(window).scroll(function(){
				var container = $('.mybao-info'),
					page = container.attr('data-page');
				if(page=='' || page==null) return !1;
				var pageH = $(document.body).height();
                var scrollT = $(window).scrollTop();
                var winH = $(window).height();
                var stopstatus=container.attr("data-load");
                if(scrollT/(pageH-winH)>0.95 && stopstatus != 'false'){
                	container.siblings('.load-more').css('background','#f1f1f1').text('正在努力加载数据...');
                    page++;
                    container.attr("data-load","false");
                    $.post('url',{padg:page},function(result){
                    	if (result.status==1){
                    		container.attr("data-load","true");
                    		container.attr("data-page",result.page);
                    		container.siblings('.load-more').css('background','#fff').text('');
                    		var tmpData,str="";
                    		for(var i = 0;i < result.data.length;i++){
								tmpData = result.data[i];
								str+='<li><span>'+tmpData.no+'</span>';
								str+='<div class="pic"><img src="images/'+tmpData.img+'" alt=""></div>';
								str+='<div class="txt"><strong>'+tmpData.name+'</strong><p>'+tmpData.info+'</p></div></li>';
							}
							if (str != '') {
								container.append(str);
							}else{
								container.siblings('.load-more').css('background','#f1f1f1').text('没有更多数据').show();
							}
                    	}else{
                    		container.attr("data-load","true");
                    		$.circle.util.tips(tesult.msg);
                    	}
                    },'json');
                }
			})
		},
		//加载更多我的礼物
		gift_load:function(){
			$(window).scroll(function(){
				var container = $('.gift-main'),
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
                    $.post('url',{padg:page},function(result){
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
                    		$.circle.util.tips(tesult.msg);
                    	}
                    },'json');
                }
			})
		}
	};
})(jQuery);

