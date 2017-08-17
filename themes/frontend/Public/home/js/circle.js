;(function($){
	$.circle = $.circle || {version: "v1.1.0"},
    $.extend($.circle, {
    	util:{
    		getStrLength:function(str){
    			str = $.trim(str);
    			var length = str.replace(/[^\x00-\xff]/g, "**").length;
                return parseInt(length / 2) == length / 2 ? length / 2: parseInt(length / 2) + .5;
    		},
    		empty:function(str){
    			return void 0 === str || null === str || '' === str
    		},
    		//通用提示框layer
    		tips:function(str){
    			layer.open({
				    content: str
				    ,skin: 'msg'
				    ,time: 2 //2秒后自动关闭
				});
    		},
    		//表情替换
    		replace_emotion:function(str){
    			var name_arr = $.circle.util.emotion_bag(),
    				face = new Array();
				for (var i = 0; i <= 75; i++) {
					face["["+name_arr[i]+"]"]='<img src="images/emotion/'+i+'.gif" border="0" />';
				}
				var reg = /\[.+?\]/g;
				str = str.replace(reg,function(a,b){
					if (face[a] == undefined) return a;
					return face[a];
				});
				return str;
    		},
    		//表情包
            emotion_bag: function(){
                return new Array("mm","微笑","撇嘴","色","发呆","流泪","害羞","闭嘴","睡","大哭","尴尬","发怒","调皮","龇牙","惊讶","难过",
                    "冷汗","抓狂","吐","偷笑","可爱","白眼","傲慢","饥饿","困","惊恐","流汗","憨笑","大兵","奋斗","咒骂",
                    "疑问","嘘","晕","抓狂","衰","敲打","再见","擦汗","抠鼻","糗大了","坏笑","左哼哼","右哼哼","哈欠","鄙视",
                    "委屈","快哭了","阴险","亲亲","吓","可怜","拥抱","月亮","太阳","炸弹","骷髅","菜刀","猪头","西瓜","咖啡",
                    "饭","爱心","强","弱","握手","胜利","抱拳","勾引","OK","NO","玫瑰","凋谢","示爱","爱情","飞吻");
            },
            //判断登陆
            islogin: function(){
               return "" == CIRCLE.uid ? (circle.location.href=CIRCLE.login_url) : !0;
            },
            empty: function(str) {
                return void 0 === str || null === str || "" === str
            },
            isName: function(str) {
                return /[\u4e00-\u9fa5]{2,4}/.test(str) ? !0 : !1
            }
            // isMobile: function(str) {
            //     return /^0?1[3|4|5|8][0-9]\d{8}$/g.test(str) ? !0 : !1
            // }
    	}
    })
})(jQuery);

_chinese = /[\u4e00-\u9fa5]{2,4}/,_phone = /^0?1[3|4|5|8][0-9]\d{8}$/;