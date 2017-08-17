;(function($){
	$.talk = {
		init:function(){
			$.talk.common();
			$.talk.play_voice();
			$.talk.gift_scroll();
			$.talk.select_gift();
			$.talk.msg_rights();
			$.talk.select_see();
			$.talk.play_video();
		},
		common:function(){
			$(".talk-list").niceScroll({ 
				cursorcolor:"rgba(177,177,177,0.6)", 
				touchbehavior:false, 
				cursorwidth:"5px", 
				cursorborder:"0", 
				autohidemode:false,
				cursorborderradius:"5px"
			})
		},
		// 播放语音
		play_voice:function(){
			$(document).on("click",".voi_play",function(){
				var self = $(this);
				var voi_src = self.attr("data-val");
				var player = document.getElementById("voi_player");
				if(player.paused){
					player.src = voi_src;
					player.play();
					self.find("i").addClass("playing");
					player.addEventListener("ended",function(){
						self.find("i").removeClass("playing");
					});
				}else{
					if (player.currentSrc == self.attr('data-val')){
						player.pause();
					    self.find('i').removeClass('playing');
					}else{
						player.src = voi_src;
					    player.play();
					    $('.voi_play').find('i').removeClass('playing');
					    self.find('i').addClass('playing');
					    player.addEventListener('ended',function(){
					        self.find('i').removeClass('playing');
					    });
					}
				}
			})
		},
		//礼物滚动显示
		gift_scroll:function(){
			var gift_sum = $(".gift-list").find("li").length;
			var li_width = $(".gift-list").find("li").outerWidth();
			$(".gift-list").width(li_width*gift_sum);
			var scroll_num = Math.ceil(gift_sum/4);
			var i = 1;
			$(".gift-box .right").on("click",function(){
				if(i<scroll_num){
					i++;
					show_gift(i);
				}else{
					return !1;
				}
			});
			$(".gift-box .left").on("click",function(){
				if(i>1){
					i--;
					show_gift(i);
				}else{
					return !1;
				}
			});
			function show_gift(n){
				var box_width = $(".ul-box").width();
				var current_left = -(n-1)*box_width;
				$(".gift-list").animate({left:current_left},300);
			}
		},
		// 选择礼物
		select_gift:function(){
			$(".gift-list > li").on("click",function(){
				var self = $(this);
				self.children("div").addClass("selected");
				self.siblings("li").children("div").removeClass("selected");
				$.talk.yjbao_sum();
			})
			$(".gift-number .plus").on("click",function(){
				var value = parseInt($("#gift_sum").val());
				value++;
				$("#gift_sum").val(value);
				$.talk.yjbao_sum();
			});
			$(".gift-number .reduce").on("click",function(){
				var value = parseInt($("#gift_sum").val());
				if (value>=2) {
					value--;
					$("#gift_sum").val(value);
					$.talk.yjbao_sum();
				}else{
					layer.msg('最少送一个礼物！');
				}
			});
			$("#gift_sum").on("change",function(){
				$.talk.yjbao_sum();
			})
		},
		// 送礼物有需要赢家宝数量
		yjbao_sum:function(){
			var bao_select = $(".gift-list").find('div.selected');
			if (bao_select.length==0) {
				layer.msg('请选择礼物！');
				return !1;
			}
			var bao_price = parseInt(bao_select.children("p").text());
			var bao_num = $("#gift_sum").val();
			if (!/^[0-9]*$/.test(bao_num)) {
				layer.msg('请输入阿拉伯数字！');
				return !1;
			}
			var bao_sum = bao_price * bao_num;
			$("#bao_sum").html(bao_sum);
		},
		// 发送消息付费或免费
		msg_rights:function(){
			$(document).on("click",".msg-rights li",function(){
				var self = $(this);
				self.addClass("selected").siblings("li").removeClass("selected");
				if (self.hasClass("unfree")){
					self.css("background-image","url(/themes/frontend/Public/home/images/home/icon_unfree2.png)")
				}else{
					self.css("background-image","url(/themes/frontend/Public/home/images/home/icon_free2.png)")
				}
				if (self.siblings("li").hasClass("unfree")){
					self.siblings("li").css("background-image","url(/themes/frontend/Public/home/images/home/icon_unfree.png)")
				}else{
					self.siblings("li").css("background-image","url(/themes/frontend/Public/home/images/home/icon_free.png)")
				}
			})
		},
		// 送出礼物的动画效果
		gift_effect:function(){
			$(document).on("click",".send-gift",function(){
				var gift_num = parseInt($("#gift_sum").val());
				var gift_src = $('.gift-list .selected').children("img").attr("src");
				for(var i = 0;i < gift_num;i++){
					var gift = new Image();
					gift.src = gift_src;
					$(".talk-list").append(gift);
					setTimeout(function(){
						gift.remove();
					},3000);
				}
			})
		},
		// 选择看老师还是看全部
		select_see:function(){
			$(".see-teacher input").on("click",function(){
				$.talk.see_teacher();
			})
		},
		see_teacher:function(){
			if ($(".see-teacher input").prop("checked")) {
				$(".userwz").hide();
				$(".uservoice").hide();
			}else{
				$(".userwz").show();
				$(".uservoice").show();
			}
		},
		// 点击视频弹窗播放
		play_video:function(){
			$(document).on('click','.info video',function(e){
				e.preventDefault();
				var voice_player = document.getElementById('voi_player');
				if (voice_player.play) {
					voice_player.pause();
				}
				var video_url = $(this).attr('src');
				$('#video_player').parent('.video_bg').show();
				var video_player = $('#video_player').get(0);
				video_player.src = video_url;
				video_player.play();
			});
			$(document).on('click','.video_bg .vid_close',function(event){
				event.preventDefault();
				var video_player = $('.video_bg').find('video')[0];
				if (video_player.play) {
					video_player.pause();
				}
				$(this).parents('.video_bg').fadeOut('fast');
			})
		}
	};
	$.talk.init();
})(jQuery);




