;(function($){
	$.circle.pay = {
		settings:{

		},
		init:function(){
		},
		//选择课程数量并提交订单购买赢家宝
		// submit_order:function(){
		// 	var num=parseInt($('#quantity').text()),price=parseInt($('#univalent').text()),sum_money;
		// 	$(document).on('click','#reduce',function(){
		// 		if (num>1) {
		// 			num--;
		// 			sum_money=num*price;
		// 			$('#quantity').text(num);
		// 			$('#sum_money').text(sum_money);
		// 			return num;
		// 		}
		// 		if (num=1) {
		// 			$.circle.util.tips('至少选择一节课！');
		// 			return !1;
		// 		}
		// 	});
		// 	$(document).on('click','#plus',function(){
		// 		if(num<10){
		// 			num++;
		// 			sum_money=num*price;
		// 			$('#quantity').text(num);
		// 			$('#sum_money').text(sum_money);
		// 			return num;
		// 		}
		// 		if (num=10) {
		// 			$.circle.util.tips('最多选择十节课！');
		// 			return !1;
		// 		}
		// 	});
		// 	$(document).on('click','#go_buy',function(e){
		// 		event.preventDefault()
		// 		var uid='';//测试使用
		// 		if ($.circle.util.empty(uid)) {
		// 			$.circle.util.tips('请先登录！');
		// 			setTimeout(function(){
		// 				window.location.href='url';
		// 			},3E3);
		// 			return !1;
		// 		}
		// 		$.post('url',{uid:uid,teacher:teacher,sum_money:sum_money,number:num},function(result){
		// 			if (result.status==1) {
		// 				window.location.href="url";
		// 			}else{
		// 				$.circle.util.tips(result.msg);
		// 			}
		// 		},'json');
		// 	})
		// },
		//立即支付，支付人民币获得赢家宝
		go_pay:function(){
                    /*
			$(document).on('click','a#go_pay',function(e){
				event.preventDefault();
				var self=$(this);
				if ($('.agree input').is(':checked')) {
					window.location.href=self.attr('href');
				}else{
					$.circle.util.tips('请先同意网络服务条款！');
				}
			});*/
		},
		//用赢家宝购买文字直播权限
		pay_yjbao:function(){
			$(document).on('click','.course-time tr',function(){//选择购买课程时间
				if ($(this).find("input[name='time']").length != 0) {
					$(this).addClass('on').siblings('tr').removeClass('on');
					$(this).find("input[name='time']").prop("checked",true);
					$(this).find("input[name='number']").focus();
				}
			});
			$(document).on('click','#pay_yjbao',function(){//提交购买
				var container=$(".course-time").find("tr.on");
				var price=$.trim(container.find('span').text());
				var num=$.trim(container.find("input[name='number']").val());
				var Number=/^[0-9]*$/;
				if(!Number.test(num)){
					$.circle.util.tips('请输入阿拉伯数字！');
					return!1;
				}
				if (num == 0) {
					$.circle.util.tips('请输入大于0的数字！');
					return!1;
				}
				var sum_yjbao=price*num;
				$('#sum_yjbao').text(sum_yjbao+'个');
				if(sum_yjbao==0 || sum_yjbao==""){
					$.circle.util.tips('请输入订购数量！');
					return !1;
				}
				$.post('url',{uid:uid,rteachid:teachid,yjbaos:yjbaos,coursetype:coursetype},function(result){
					if (result.status==1) {
						if (data.type=0) {
							$('#baotip-box').show();
							$('#baotip-box .close').click(function(){
								$(this).parents('#baotip-box').hide();
							});
						}else{
							$.circle.util.tips('购买成功！');
						}
					}else{
						$.circle.util.tips(result.msg);
					}
				},'json');
			})
		}
	};
})(jQuery);


