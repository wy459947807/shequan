// QQ表情插件
(function($){
	$.fn.qqFace = function(options){
		var defaults = {
			id : 'qqfacebox',
			assign : 'emotion_area',
			path:'/public/images/emotion/'	//表情存放的路径
			
		};
		var option = $.extend(defaults, options);
		var assign = $('.'+option.assign);
		var id = option.id;
		var path = option.path;
		var name_arr = $.circle.util.emotion_bag();
		if(assign.length<=0){
			alert('缺少表情赋值对象');
			return false;
		}
		
		$(this).click(function(e){
			if($('#'+id).length>0){$('#'+id).remove();}
			var strFace, labFace, pos;
			if($('#'+id).length<=0){
				pos=$(".emotion").index(this);
				strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="qq_Face">' +
							  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
				for(var i=1; i<=75; i++){
					labFace = "["+name_arr[i]+"]";
					strFace += '<td><img src="'+path+i+'.gif" title="'+name_arr[i]+'" onclick="$(\'.'+option.assign+'\').setCaret(' + pos + ');$(\'.'+option.assign+'\').insertAtCaret(' + pos + ',\'' + labFace + '\');" /></td>';
					if( i % 15 == 0 ) strFace += '</tr><tr>';
				}
				strFace += '</tr></table></div>';
			}
			$(this).parent().append(strFace);
			var offset = $(this).position();
			var top = offset.top-$('#'+id).height();
			$('#'+id).css('top',top);
			$('#'+id).css('left',offset.left);
			$('#'+id).fadeIn('5000');
			e.stopPropagation();
		});
		
		$(document).click(function(){
			$('#'+id).fadeOut('fast',function(){$('#'+id).remove();});
		});
	};

	$(document).on("keyup",".emotion_area",function () {
		var content = $(this).val(),
		    pos = $(".emotion_area").index(this),
		    lengths = $.circle.util.getStrLength(content);  //取得当前字数
		//最大允许输入300个字
		if (lengths >= 300) {
			$(this).val(content.substring(0, Math.ceil(300)));
		}
		var num = 300 - Math.ceil(lengths);
		var msg = num < 0 ? 0 : num;
		//当前字数同步到显示提示
		$('#input_fontnum').html(msg);
		$(this).setCaret(pos);
	});

	$(document).on("mouseenter",".emotion",function(){
		$(this).qqFace({
			id : 'qqfacebox',
			assign : 'emotion_area',
			path:'/public/images/emotion/'	//表情存放的路径
		});
	});

})(jQuery);

jQuery.extend({ 
unselectContents: function(){ 
	if(window.getSelection) 
		window.getSelection().removeAllRanges(); 
	else if(document.selection) 
		document.selection.empty(); 
	} 
}); 
jQuery.fn.extend({ 
	selectContents: function(){ 
		$(this).each(function(i){ 
			var node = this; 
			var selection, range, doc, win; 
			if ((doc = node.ownerDocument) && (win = doc.defaultView) && typeof win.getSelection != 'undefined' && typeof doc.createRange != 'undefined' && (selection = window.getSelection()) && typeof selection.removeAllRanges != 'undefined'){ 
				range = doc.createRange(); 
				range.selectNode(node); 
				if(i == 0){ 
					selection.removeAllRanges(); 
				} 
				selection.addRange(range); 
			} else if (document.body && typeof document.body.createTextRange != 'undefined' && (range = document.body.createTextRange())){ 
				range.moveToElementText(node); 
				range.select(); 
			} 
		}); 
	}, 

	setCaret: function(pos){ 
		if(!/msie/.test(navigator.userAgent.toLowerCase())) return; 
		var initSetCaret = function(){ 
			var textObj = $(this).get(pos); 
			textObj.caretPos = document.selection.createRange().duplicate(); 
		}; 
		$(this).click(initSetCaret).select(initSetCaret).keyup(initSetCaret); 
	}, 

	insertAtCaret: function(pos,textFeildValue){ 
		var textObj = $(this).get(pos),
			tval='';
		if(document.all && textObj.createTextRange && textObj.caretPos){ 
			var caretPos=textObj.caretPos; 
			caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ? 
			textFeildValue+'' : textFeildValue; 
			tval=caretPos.text;caretPos.text='';caretPos.focus();caretPos.text=tval;
		} else if(textObj.setSelectionRange){ 
			var rangeStart=textObj.selectionStart; 
			var rangeEnd=textObj.selectionEnd; 
			var tempStr1=textObj.value.substring(0,rangeStart); 
			var tempStr2=textObj.value.substring(rangeEnd); 
			textObj.value=tempStr1+textFeildValue+tempStr2; 
			textObj.focus(); 
			var len=textFeildValue.length; 
			textObj.setSelectionRange(rangeStart+len,rangeStart+len); 
			textObj.blur(); 
		}else{
			textObj.value+=textFeildValue;
			tval=textObj.value;textObj.value='';textObj.focus();textObj.value=tval;
		}
		$( '.emotion_area' ).keyup();
	} 
});