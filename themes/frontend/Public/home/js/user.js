;(function($){
	$.user = {
		side_nav:function(){
			$(".fl_list h4").on("click",function(){
				var self = $(this);
				self.siblings("ul").stop().slideToggle();
				self.find('em').toggleClass("rotate");
			})
		},
		change_stand:function(){
			$("div.xg-tip").on("click",function(){
				var self = $(this);
				var input = self.siblings("input");
				var val = input.val();
				input.focus();
				input.val("");
				input.val(val);
			})
		}
	}
})(jQuery);



