/*
 * 依赖包：
 * __PUBLIC__/js/jquery.js
 * __PUBLIC__/js/cookie.js
 * __PUBLIC__/js/expand/LG.js
 * 
 */

var Weixin = function (config) {
    var weixin = this; //全局对象
    var loacalUrl = new LG.URL(window.location.href);

    var url_config = {
        authHost: 'http://activity.10jrw.com', //微信认证域名
        authService: "https://open.weixin.qq.com/connect/oauth2/authorize", //微信认证服务地址
        authRedirect: '/index.php/api/wechat/authRedirect', //微信认证
        authUser: '/index.php/api/wechat/authUser', //微信认证地址
    };

    weixin.user = {};  //用户全局变量
    weixin.host = loacalUrl.host;
    weixin.config = {
        appId: "",
        timestamp: "",
        nonceStr: "",
        signature: "",
    };

    // 初始化(构造函数)
    (function () {
        if(config){
            weixin.config=config;
        }

        if ($.cookie('user')) {
            weixin.user = JSON.parse($.cookie('user'));
        } else if (loacalUrl.get("code")) {
            //window.location.href = url_config['authUser']+"?code="+loacalUrl.get("code");
            //alert(loacalUrl.get("code"));
            $.ajax({  
                type : "get",  
                url : url_config['authUser'],  
                data : {code: loacalUrl.get("code")},  
                async : false,  
                success : function(result){  
                    if (result.status == 200) {
                        $.cookie('user', JSON.stringify(result.data));
                        weixin.user = result.data;
                    }
                }  
            });   
            
        } else {
            var redirectUrl = url_config.authHost + url_config.authRedirect + "?url=" + encodeURIComponent(window.location.href);
            var authUrl = new LG.URL(url_config.authService);
            var urlParams = {
                appid: weixin.config.appId,
                redirect_uri: encodeURIComponent(redirectUrl),
                response_type: "code",
                scope:"snsapi_userinfo",
                //scope: "snsapi_base",
                state: "state",
            }
            for (var index in urlParams) {
                authUrl.set(index, urlParams[index]);
            }
            window.location.href = authUrl.url() + "#wechat_redirect";
        }
    })();
    
    weixin.test = function () {
        alert("test");
    };

}


var configInfo = getRemoteData({url: window.location.href}, "/index.php/api/wechat/sign");//获取微信JS配置信息
var Weixin=new Weixin(configInfo);
