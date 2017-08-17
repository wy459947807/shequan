;(function($){
	$.pay={
		setting:{},
		standerd:function(){
			$(".course-time tr").on('click',function(){
				var self = $(this);
				if (self.find('input').length!=0){
					self.find('input[type="text"]').focus();
					self.find('input[type="radio"]').prop("checked","checked");
					self.addClass('on').siblings('tr').removeClass('on');
				}
			})
		}
	};
})(jQuery);
