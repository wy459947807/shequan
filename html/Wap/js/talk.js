;(function($){
	$.circle.talk = {
		settings:{
			send_msg_btn:'input#send_msg',
			msg_autoScroll:true,
			is_send_msg:false,
			msg_area:'input#msg_area'
		},
		init:function(){
			$.circle.talk.commen();
			$.circle.talk.follow_teach();
			$.circle.talk.select_look();
			$.circle.talk.send_gift();
			$.circle.talk.camera_show();
			$.circle.talk.msg_isfree();
			$.circle.talk.send_msg();
		},
		commen:function(){
			//介绍详情
			var s = $.circle.talk.settings;
			$(document).on('click','.introduc > span',function(){
				$(this).parent('.introduc').find('.info').toggle();
			});
			function count_height(){
				// 中间聊天部分高度
				var header_h = $('.yj_header').outerHeight(true),
				    top_h = $('.share-top').outerHeight(true),
				    tit_h = $('.share-tit').outerHeight(true),
				    send_h = $('.send-msg').outerHeight(true),
				    win_h = $(window).outerHeight(true);
			    var talk_h = (win_h-header_h-top_h-tit_h-send_h)+'px';
			    $('.talk-main').height(talk_h);
			};
			count_height();
			function edite_width(){
				//输入框的宽度，发送礼物按钮隐藏时间，需再次调用
				var p_right=$('.btn-right').width()+2;
				$('.send-msg .row2').css('padding-right',p_right+'px');
			};
			edite_width();
			//发送消息之后隐藏发送按钮
			$(s.msg_area).on("focus",function(){
				if(!$.circle.util.empty($(s.msg_area).val())){
					$('.btn-area').hide();
					$(s.send_msg_btn).show();
					edite_width();
				}else{
					$('.btn-area').show();
					$(s.send_msg_btn).hide();
					edite_width();
				};
			});
			//编辑状态显示发送按钮
			$(s.msg_area).on("input",function(){
				if(!$.circle.util.empty($(s.msg_area).val())){
					$('.btn-area').hide();
					$(s.send_msg_btn).show();
					edite_width();
				}else{
					$('.btn-area').show();
					$(s.send_msg_btn).hide();
					edite_width();
				};
			});
			//切换语音和键盘
			$('.editor').click(function(){
				$(this).hide();
				$(this).siblings('span.editor').show();
				var par = $(this).parent('.row2');
				if (par.find('.speech').is(':visible')) {
					par.find('input.text-area').show();
					par.find('div.voice-box').hide();
				}else{
					par.find('input.text-area').hide();
					par.find('div.voice-box').show();
				}
			});
		},
		//关注
		follow_teach:function(){
			$(document).on('click','#follow',function(){
				var self =$(this);
				if($.circle.util.empty(u_id)){
					$.circle.util.tips('您未登录，3秒后跳转登录！');
					setTimeout(function(){
						setTimeout(window.location.href="");
					},3E3);
				}
				$.post('url',{u_id:u_id,room_id:room_id},function(result){
					if (result.status == 1) {
						self.hide();
						self.siblings('.follow').find('span').text(parseInt(self.siblings('.follow').find('span').text())+1);
						self.siblings('.follow').show();
					}else{
						$.circle.util.tips(result.msg);
					}
				},'json')
			});
		},
		// 选择看老师或者看全部
		select_look:function(){
			$('.select_look').click(function(){
				$(this).hide();
				$(this).siblings('.select_look').show();
				if ($('.look-teacher').is(':visible')) {
					$('.userwz,.uservoice').hide();
				}else{
					$('.userwz,.uservoice').show();
				}
			})
		},
		//赠送礼物
		send_gift:function(){
			$('.gift').click(function(e){
				e.stopPropagation();
				if($('#qqfacebox').is(':visible')){
					$('#qqfacebox').hide('fast',function(){$('#qqfacebox').remove();});
				}
				if($('#photo-box').is(':visible')){
					$('#photo-box').hide();
				}
				if($('#gift-box').is(':visible')){
					return !1;
				}
				$('#gift-box').fadeIn(300);
			});
			$(document).click(function(){
				$('#gift-box').fadeOut(300);
			});
			$('#gift-box img').click(function(){
				var url = $(this).attr('src');
				console.log(url);
				var gift_url ='images/gift/'+url.substring(17,19)+'.gif';
				console.log(gift_url);
				var date={
					hour:{},
					minut:{},
					sec:{}
				};
				var bao
				$.post('url',{u_id:u_id,},function(result){},'json')
				$.circle.talk.now_time(date);
				var str = '<li class="userwz"><div class="tit"> <span class="time">'+date.hour+':'+date.minut+':'+date.sec+'</span>少李胜<i class="reply">回复信息</i><img src="images/share_pic1.png" alt=""> </div>';
			    str += '<div class="info"><i></i><span><img src="';
				str += url+'"></span></div></li>';
				$('ul#talk_list').append(str);
				$.circle.talk.scrollBottom('.talk-main');
				var gif = '<img src="'+gift_url+'">';
				$('.talk-main').append(gif);
				$('.talk-main > img').css({'width':'80%','position':'fixed','left':'10%','top':'30%','z-index':'99'});
				setTimeout(function(){
					$('.talk-main > img').remove();
				},3E3);
			});

		},
		//发送图片
		camera_show:function(){
			$('.plus').click(function(e){
				e.stopPropagation();
				if($('#qqfacebox').is(':visible')){
					$('#qqfacebox').hide('fast',function(){$('#qqfacebox').remove();});
				}
				if($('#gift-box').is(':visible')){
					$('#gift-box').hide();
				}
				if($('#photo-box').is(':visible')){
					return !1;
				}
				$('#photo-box').fadeIn(300);
			});
			$(document).click(function(){
				$('#photo-box').fadeOut(300);
			});
			$('#load_pic').on('change',function(event){
                var files = event.target.files,file;
                if (files && files.length > 0) {
                    file = files[0];
                    var URL = window.URL || window.webkitURL;
                    var imgUrl = URL.createObjectURL(file);
                    //console.log(imgUrl);
                    var date={
						hour:{},
						minut:{},
						sec:{}
					};
                    var fr = new FileReader();
                    fr.readAsDataURL(file);
	                fr.onload = function(ele){
	                    var pic_data = ele.target.result;
	                    console.log(pic_data);
	                };
					$.circle.talk.now_time(date);
					$.post('url',{room_ic:room_ic,uid:uid,pic_data:pic_data},function(result){
						if (result.status==1) {
							var str = '<li class="userwz"><div class="tit"> <span class="time">'+date.hour+':'+date.minut+':'+date.sec+'</span>少李胜<i class="reply">回复信息</i><img src="images/share_pic1.png" alt=""> </div>';
						    str += '<div class="info"><i></i><span><img src="';
							str += imgUrl+'"></span></div></li>';
		                    $('ul#talk_list').append(str);
							$.circle.talk.scrollBottom('.talk-main');
						}else{
							$.circle.util.tips(result.msg);
						}
					},'json');
                }
            });
            $('#load_video').on('change',function(event){
                var files = event.target.files,file;
                if (files && files.length > 0) {
                    file = files[0];
                    var fr = new FileReader();
                    fr.readAsDataURL(file);
	                fr.onload = function(ele){
	                    var pic_data = ele.target.result;
	                    console.log(pic_data);
	                };
                }
            });
		},
		send_msg:function(){
			var s = $.circle.talk.settings;
			$(s.send_msg_btn).click(function(){
				if($.circle.util.empty(uid)){
					$.circle.util.tips('您未登录，3秒后跳转登录！');
					var t=setTimeout(function(){window.location.href=WINNER.login_url;},3E3);
					return !1;
				}
				var self=$(this),str='',content;
				content = $.trim($(s.msg_area).val());
				if($.circle.util.empty(uid)) {}

			})
		},
		// 选择发送内容是否免费
		msg_isfree:function(){
			$('#msg_rights>li').click(function(){
				$(this).addClass('selected').siblings('li').removeClass('selected');
			})
		},
		scrollBottom: function (id,speed){
			speed = speed ? speed : 1200;
			if($(id)[0].scrollHeight > 0){
				$(id).animate({scrollTop: $(id)[0].scrollHeight}, speed);
			}
		},
		now_time:function(date){
			var time = new Date();
			date.hour = time.getHours();
			date.minut = time.getMinutes();
			date.sec = time.getSeconds();
			if (date.minut<10) {
				date.minut='0'+date.minut;
			}
			if (date.sec<10) {
				date.sec='0'+date.sec;
			}
			return date;
		}
	};
	$.circle.talk.init();
})(jQuery);



// function onSuccess(stream) {
//     //创建一个音频环境对像
//     audioContext = window.AudioContext || window.webkitAudioContext;
//     context = new audioContext();

//     //将声音输入这个对像
//     audioInput = context.createMediaStreamSources(stream);
    
//     //设置音量节点
//     volume = context.createGain();
//     audioInput.connect(volume);

//     //创建缓存，用来缓存声音
//     var bufferSize = 2048;

//     // 创建声音的缓存节点，createJavaScriptNode方法的
//     // 第二个和第三个参数指的是输入和输出都是双声道。
//     recorder = context.createJavaScriptNode(bufferSize, 2, 2);

//     // 录音过程的回调函数，基本上是将左右两声道的声音
//     // 分别放入缓存。
//     recorder.onaudioprocess = function(e){
//         console.log('recording');
//         var left = e.inputBuffer.getChannelData(0);
//         var right = e.inputBuffer.getChannelData(1);
//         // we clone the samples
//         leftchannel.push(new Float32Array(left));
//         rightchannel.push(new Float32Array(right));
//         recordingLength += bufferSize;
//     }

//     // 将音量节点连上缓存节点，换言之，音量节点是输入
//     // 和输出的中间环节。
//     volume.connect(recorder);

//     // 将缓存节点连上输出的目的地，可以是扩音器，也可以
//     // 是音频文件。
//     recorder.connect(context.destination); 
// }

