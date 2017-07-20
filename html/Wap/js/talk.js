;
(function ($) {
    $.circle.talk = {
        settings: {
            send_msg_btn: 'input#send_msg',
            msg_autoScroll: true,
            is_send_msg: false,
            msg_area: 'input#msg_area'
        },
        init: function () {
            $.circle.talk.commen();
            $.circle.talk.follow_teach();
            $.circle.talk.select_look();
            $.circle.talk.send_gift();
            $.circle.talk.send_txtmsg();
            $.circle.talk.camera_show();
            $.circle.talk.msg_isfree();
            $.circle.talk.send_txtmsg();
            $.circle.talk.send_voice();
            $.circle.talk.play_video();
            $.circle.talk.play_voice();
            //$.circle.talk.unlock_msg();
        },
        commen: function () {
            //介绍详情
            var s = $.circle.talk.settings;
            $(document).on('click', '.introduc > span', function () {
                $(this).parent('.introduc').find('.info').toggle();
            });

            function count_height() {
                // 中间聊天部分高度
                var header_h = $('.yj_header').outerHeight(true),
                        top_h = $('.share-top').outerHeight(true),
                        tit_h = $('.share-tit').outerHeight(true),
                        send_h = $('.send-msg').outerHeight(true),
                        win_h = $(window).outerHeight(true);
                var talk_h = (win_h - header_h - top_h - tit_h - send_h) + 'px';
                $('.talk-main').height(talk_h);
            }
            ;
            count_height();
            function edite_width() {
                //输入框的宽度，发送礼物按钮隐藏时间，需再次调用
                var p_right = $('.btn-right').width() + 2;
                $('.send-msg .row2').css('padding-right', p_right + 'px');
            }
            ;
            edite_width();
            //发送消息之后隐藏发送按钮
            $(s.msg_area).on("focus", function () {
                if (!$.circle.util.empty($(s.msg_area).val())) {
                    $('.btn-area').hide();
                    $(s.send_msg_btn).show();
                    edite_width();
                } else {
                    $('.btn-area').show();
                    $(s.send_msg_btn).hide();
                    edite_width();
                }
                ;
            });
            //编辑状态显示发送按钮
            $(s.msg_area).on("input", function () {
                if (!$.circle.util.empty($(s.msg_area).val())) {
                    $('.btn-area').hide();
                    $(s.send_msg_btn).show();
                    edite_width();
                } else {
                    $('.btn-area').show();
                    $(s.send_msg_btn).hide();
                    edite_width();
                }
                ;
            });
            //切换语音和键盘
            $('.editor').click(function () {
                $(this).hide();
                $(this).siblings('span.editor').show();
                var par = $(this).parent('.row2');
                if (par.find('.speech').is(':visible')) {
                    par.find('input.text-area').show();
                    par.find('div.voice-box').hide();
                } else {
                    par.find('input.text-area').hide();
                    par.find('div.voice-box').show();
                }
            });
        },
        //点击关注
        follow_teach: function () {
            $(document).on('click', '#follow', function () {
                var self = $(this);
                if ($.circle.util.empty(u_id)) {
                    $.circle.util.tips('您未登录，3秒后跳转登录！');
                    setTimeout(function () {
                        setTimeout(window.location.href = "");
                    }, 3E3);
                }
                $.post('url', {u_id: u_id, room_id: room_id}, function (result) {
                    if (result.status == 1) {
                        self.hide();
                        self.siblings('.follow').find('span').text(parseInt(self.siblings('.follow').find('span').text()) + 1);
                        self.siblings('.follow').show();
                    } else {
                        $.circle.util.tips(result.msg);
                    }
                }, 'json')
            });
        },
        // 选择看老师或者看全部
        select_look: function () {
            $('.select_look').click(function () {
                $(this).hide();
                $(this).siblings('.select_look').show();
                if ($('.look-teacher').is(':visible')) {
                    $('.userwz,.uservoice').show();
                } else {
                    $('.userwz,.uservoice').hide();
                }
            })
        },
        //赠送礼物
        send_gift: function () {
            $('.gift').click(function (e) {
                e.stopPropagation();
                if ($('#qqfacebox').is(':visible')) {
                    $('#qqfacebox').hide('fast', function () {
                        $('#qqfacebox').remove();
                    });
                }
                if ($('#photo-box').is(':visible')) {
                    $('#photo-box').hide();
                }
                if ($('#gift-box').is(':visible')) {
                    return !1;
                }
                $('#gift-box').fadeIn(300);
            });
            $(document).click(function () {
                $('#gift-box').fadeOut(300);
            });
            /*
            $('#gift-box img').click(function () {
                var url = $(this).attr('src');
                console.log(url);
                var gift_url = 'images/gift/' + url.substring(17, 19) + '.gif';
                var saytime = $.circle.talk.now_time();
                $.post('url', {user_id: user_id, message: url, room_id: room_id, msg_type: 4, role: 2, saytime: saytime}, function (result) {
                    if (result.status == 1) {
                        var str = '';
                        str += '<li class="userwz"><div class="tit"> <span class="time">' + result.data.saytime + '</span>' + result.data.client_name + '<img src="' + result.data.avatar + '"></div>';
                        str += '<div class="info"><i></i><span><img src="';
                        str += gift_url + '"></span></div></li>';
                        $('ul#talk_list').append(str);
                        $.circle.talk.scrollBottom('.talk-main');
                        var gif = '<img src="' + gift_url + '">';
                        $('.talk-main').append(gif);
                        $('.talk-main > img').css({'width': '80%', 'position': 'fixed', 'left': '10%', 'top': '30%', 'z-index': '99'});
                        setTimeout(function () {
                            $('.talk-main > img').remove();
                        }, 3E3);
                    } else {
                        $.circle.util.tips(result.msg);
                        return !1;
                    }
                }, 'json');
            });*/
            
            
        },
        //发送图片和视频
        camera_show: function () {
            $('.plus').click(function (e) {
                e.stopPropagation();
                if ($('#qqfacebox').is(':visible')) {
                    $('#qqfacebox').hide('fast', function () {
                        $('#qqfacebox').remove();
                    });
                }
                if ($('#gift-box').is(':visible')) {
                    $('#gift-box').hide();
                }
                if ($('#photo-box').is(':visible')) {
                    return !1;
                }
                $('#photo-box').fadeIn(300);
            });
            $(document).click(function () {
                $('#photo-box').fadeOut(300);
            });
            //发送图片
            $('#load_pic').on('change', function (event) {
                var self = $(this);
                var files = event.target.files, file;
                var pic_data, reader_status, saytime;
                if (files && files.length > 0) {
                    file = files[0];
                    var URL = window.URL || window.webkitURL;
                    var imgUrl = URL.createObjectURL(file);
                    var reader = new FileReader();
                    reader.readAsBinaryString(file);
                    reader.onload = function (ele) {
                        pic_data = ele.target.result;
                        reader_status = 1;
                    };
                    var timer = setInterval(function () { //保证reader加载完全
                        if (!!reader_status) {
                            clearInterval(timer);
                            send_pic();
                        }
                    }, 200);
                    saytime = $.circle.talk.now_time();
                    function send_pic() {
                        var str = '';
                        if (role == 2) {
                            $.post('url', {user_id: user_id, room_id: room_id, message: pic_data, msg_type: 3, role: 2, saytime: saytime}, function (result) {
                                if (result.status == 1) {
                                    str += '<li class="userwz"><div class="tit"> <span class="time">' + result.data.saytime + '</span>' + result.data.client_name + '<img src="' + result.data.avatar + '"></div>';
                                    str += '<div class="info"><i></i><span><img class="pic" src="';
                                    str += imgUrl + '"></span></div></li>';
                                    $('ul#talk_list').append(str);
                                    $('#load_pic').val('');
                                    $.circle.talk.scrollBottom('.talk-main');
                                } else {
                                    $.circle.util.tips(result.msg);
                                }
                            }, 'json');
                        }
                        if (role == 1) {
                            $.post('url', {user_id: user_id, room_id: room_id, message: pic_data, msg_type: 3, role: 1, saytime: saytime}, function (result) {
                                if (result.status == 1) {
                                    str += '<li class="teawz"><div class="tit"><img src="' + result.data.avatar + '">' + result.data.client_name + '<span class="time">' + result.data.saytime + '</span></div>';
                                    str += '<div class="info"><i></i><span><img class="pic" src="';
                                    str += imgUrl + '"></span></div></li>';
                                    $('ul#talk_list').append(str);
                                    $('#load_pic').val('');
                                    $.circle.talk.scrollBottom('.talk-main');
                                } else {
                                    $.circle.util.tips(result.msg);
                                }
                            }, 'json');
                        }
                    }
                    ;
                }
            });
            //发送视频
            $('#load_video').on('change', function (event) {
                var self = $(this);
                var files = event.target.files, file;
                var reader_status, canplay_status;
                var date = {
                    hour: {},
                    minut: {},
                    sec: {}
                };
                $.circle.talk.now_time(date);
                if (files && files.length > 0) {
                    file = files[0];
                    var URL = window.URL || window.webkitURL;
                    var videoUrl = URL.createObjectURL(file);
                    var video_player = document.getElementById('video_player');
                    video_player.src = videoUrl;
                    var reader = new FileReader();
                    reader.readAsBinaryString(file);
                    reader.onload = function (ele) {
                        var video_src = ele.target.result;
                        reader_status = 1;
                    };
                    video_player.addEventListener('canplay', function () {
                        video_time = Math.ceil(video_player.duration);
                        canplay_status = 1;
                    });
                    var timer = setInterval(function () {
                        if (!!canplay_status && !!reader_status) {
                            clearInterval(timer);
                            video();
                        }
                    }, 200);
                    function video() {
                        if (video_time > 11) {
                            $.circle.util.tips('视频不能超过10秒');
                            self.val('');
                            return !1;
                        }
                        var str = '';
                        str += '<li class="userwz"><div class="tit"><span class="time">' + date.hour + ':' + date.minut + ':' + date.sec + '</span>';
                        str += '梁少杰' + '<img src="images/share_pic1.png"></div>';
                        str += '<div class="info"><i></i><span><video src="' + videoUrl + '"></video></span>';
                        str += '</div></li>';
                        $('ul#talk_list').append(str);
                        $.circle.talk.scrollBottom('.talk-main');
                        self.val('');
                        //高手发送视频
                        if (role == 1) {
                            $.post('url', {u_id: u_id, message: video_src, room_id: room_id, msg_type: 6, role: 1, saytime: saytime}, function (result) {
                                if (result.status == 1) {
                                    var str = '';
                                    str += '<li class="teawz"><div class="tit"><img src="' + result.data.avatar + '">';
                                    str += result.data.client_name + '<span class="time">' + result.data.saytime + '</span></div>';
                                    str += '<div class="info"><i></i><span><video src="' + videoUrl + '"></video></span>';
                                    str += '</div></li>';
                                    $('ul#talk_list').append(str);
                                    $.circle.talk.scrollBottom('.talk-main');
                                    self.val('');
                                } else {
                                    $.circle.util.tips(result.msg);
                                }
                            }, 'json');
                        }
                        if (role == 2) {
                            $.post('url', {u_id: u_id, video_src: video_src, room_id: room_id, msg_type: 6, role: 2}, function (result) {
                                if (result.status == 1) {
                                    var str = '';
                                    str += '<li class="userwz"><div class="tit"><span class="time">' + result.data.saytime + '</span>';
                                    str += result.data.client_name + '<img src="' + result.data.avatar + '"></div>';
                                    str += '<div class="info"><i></i><span><video src="' + videoUrl + '"></video></span>';
                                    str += '</div></li>';
                                    $('ul#talk_list').append(str);
                                    $.circle.talk.scrollBottom('.talk-main');
                                    self.val('');
                                } else {
                                    $.circle.util.tips(result.msg);
                                    return !1;
                                }
                            }, 'json')
                        }
                    }
                }
            });
        },
        //发送文字消息
        send_txtmsg: function () {
            /*$('#send_msg').click(function(){
             var self=$(this),str='',content,saytime;
             var is_charge=$('#msg_rights').find('li.selected').attr('data-val');
             saytime = $.circle.talk.now_time();
             content = $.trim($('#msg_area').val());
             if($.circle.util.empty(content)){
             $('#msg_area').focus();
             $.circle.util.tips('请输入要发送内容');
             return !1;
             }
             if($.circle.util.getStrLength(content) > 140 ){
             $.circle.util.tips('发送内容不得超过140字');
             return !1;
             }
             var message = $.circle.util.replace_emotion(content);
             var str='';
             str += '<li class="teawz"><div class="tit"><img src="images/share_pic1.png">'+'是个急'+'<span class="time">'+saytime+'</span></div>';
             str += '<div class="info"><i></i><span>'+ message;
             str += '</span></div></li>';
             $('ul#talk_list').append(str);
             $.circle.talk.scrollBottom('.talk-main');
             $('#msg_area').val('');
             if (role == 1) {
             $.post('url',{user_id:user_id,message:content,room_id:room_id,is_charge:is_charge,msg_type:1,role:1,saytime:saytime},function(result){
             if (result == 1) {
             content = $.circle.util.replace_emotion(content);
             var str='';
             str += '<li class="teawz"><div class="tit"><img src="'+result.data.avatar+'">'+result.data.client_name+'<span class="time">'+result.data.saytime+'</span></div>';
             str += '<div class="info"><i></i><span>'+ content;
             str += '</span></div></li>';
             $('ul#talk_list').append(str);
             $.circle.talk.scrollBottom('.talk-main');
             $('#msg_area').val('');
             }
             },'json');
             }
             if (role == 2) {
             $.post('url',{user_id:user_id,message:content,room_id:room_id,msg_type:1,role:2,saytime:saytime},function(result){
             if (result == 1) {
             content = $.circle.util.replace_emotion(content);
             var str='';
             str += '<li class="userwz"><div class="tit"> <span class="time">'+result.data.saytime+'</span>'+result.data.client_name+'<img src="'+result.data.avatar+'"></div>';
             str += '<div class="info"><i></i><span>'+ content;
             str += '</span></div></li>';
             $('ul#talk_list').append(str);
             $.circle.talk.scrollBottom('.talk-main');
             $('#msg_area').val('');
             }
             },'json');
             }
             })*/
        },
        // 选择发送内容是否免费
        msg_isfree: function () {
            $('#msg_rights>li').click(function () {
                $(this).addClass('selected').siblings('li').removeClass('selected');
            })
        },
        // 语音发送
        send_voice: function () {
            $('.voice-area').on('click', function () {
                $('.voi_play').find('i').removeClass('playing');
                var player = document.getElementById('voi_player');
                player.pause();
            })
            
            
            /*
            $('.voice-area').change(function (event) {
                
                var self = $(this), is_charge = $('#msg_rights').find('li.selected').attr('data-val');
                var files = event.target.files, file;
                var voiceData, v_time, saytime;
                saytime = $.circle.talk.now_time();
                var reader_status, canplay_status;
                if (files && files.length > 0) {
                    file = files[0];
                    var URL = window.URL || window.webkitURL;
                    var v_palyer = document.getElementById('voi_player');
                    var voiceUrl = URL.createObjectURL(file);
                    v_palyer.src = voiceUrl;
                    var reader = new FileReader();
                    reader.readAsBinaryString(file);
                    reader.onload = function (ele) {
                        voiceData = ele.target.result;//音频文件的二进制数据
                        reader_status = 1;
                    }
                    v_palyer.addEventListener("canplay", function () {
                        v_time = Math.ceil(v_palyer.duration);//音频时长
                        canplay_status = 1;
                    });
                    var timer = setInterval(function () { //保证reader和canplay加载完全
                        if (!!canplay_status && !!reader_status) {
                            clearInterval(timer);
                            voice();
                        }
                    }, 200);
                    function voice() {
                        if (v_time > 60) {
                            $.circle.util.tips('录音不能超过60秒');
                            self.val('');
                            return !1;
                        }

                      
                      
                        //高手发送语音
                        if (role == 1) {
                            //发送用户id，语音文件，房间id，是否免费，语音时长
                            $.post('url', {user_id: user_id, message: voiceData, room_id: room_id, is_charge: is_charge, msg_type: 5, role: 1, voice_time: v_time, saytime: saytime}, function (result) {
                                if (result.status == 1) {
                                    var str = '';//用于拼接发送的信息
                                    str += '<li class="teavoice"><div class="tit"><img src="' + result.data.avatar + '">';
                                    str += result.data.client_name + '<span class="time">' + result.data.saytime + '</span></div>';
                                    str += '<div class="info"><span class="pointer"></span><p class="play voi_play" data-val="' + voiceUrl + '"><i></i></p><span>' + v_time + '”</span>';
                                    str += '</div></li>';
                                    $('ul#talk_list').append(str);//发送内容加入到聊天区域
                                    $.circle.talk.voice_length('.play', v_time);//设置语音气泡长度
                                    $.circle.talk.scrollBottom('.talk-main');//语音发送成功滚动条下滑到底部
                                    self.val('');//清空input
                                } else {
                                    $.circle.util.tips(result.msg);
                                    return !1;
                                }
                            }, 'json')

                        }
                        //学员发送语音
                        if (role == 2) {
                            //发送用户id，语音文件，房间id，语音时长
                            $.post('url', {u_id: u_id, voiceData: voiceData, room_id: room_id, msg_type: 5, role: 2, v_time: v_time, }, function (result) {
                                if (result.status == 1) {
                                    var str = '';
                                    str += '<li class="uservoice"><div class="tit"><span class="time">' + result.data.saytime + '</span>';
                                    str += result.data.client_name + '<img src="' + result.data.avatar + '"></div>';
                                    str += '<div class="info"><span class="pointer"></span><p class="play voi_play" data-val="' + voiceUrl + '"><i></i></p><span>' + v_time + '</span>';
                                    str += '</div></li>';
                                    $('ul#talk_list').append(str);
                                    $.circle.talk.voice_length('.play', v_time);
                                    $.circle.talk.scrollBottom('.talk-main');
                                    self.val('');//清空input
                                } else {
                                    $.circle.util.tips(result.msg);
                                    return !1;
                                }
                            }, 'json')
                        }
                    }
                    ;
                }
                ;
                
             
   
            })*/
        },
        //有消息时页面滑到底部
        scrollBottom: function (id, speed) {
            speed = speed ? speed : 1200;
            if ($(id)[0].scrollHeight > 0) {
                $(id).animate({scrollTop: $(id)[0].scrollHeight}, speed);
            }
        },
        //解锁付费信息
        unlock_msg: function () {
            $('.info-lock').on('click', function () {
                $('.masking').show();
                $('.tip-1').show();
            })
            $('.tip .cancel').on('click', function () {
                $('.masking').hide();
                $(this).parent().parent().hide();
            })
        },
        //获取现在时间
        now_time: function () {
            var date = {
                hour: {},
                minut: {},
                sec: {}
            };
            var saytime = '';
            var time = new Date();
            date.hour = time.getHours();
            date.minut = time.getMinutes();
            date.sec = time.getSeconds();
            if (date.minut < 10) {
                date.minut = '0' + date.minut;
            }
            if (date.sec < 10) {
                date.sec = '0' + date.sec;
            }
            saytime += date.hour + '-' + date.minut + '-' + date.sec;
            return saytime;
        },
        play_video: function () {
            $(document).on('click', '#talk_list video', function (e) {
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
            $(document).on('touchstart', '.video_bg', function () {
                $(this).find('.top').fadeToggle();
            });
            $(document).on('touchend', '.video_bg .top', function (event) {
                event.preventDefault();
                var video_player = $('.video_bg').find('video')[0];
                if (video_player.play) {
                    video_player.pause();
                }
                $(this).parent('.video_bg').fadeOut('fast');
            })
        },
        //播放语音
        play_voice: function () {
            $(document).on('click', '.voi_play', function () {
                var self = $(this);
                var voi_src = self.attr('data-val');
                var player = document.getElementById('voi_player');
                if (player.paused) {
                    player.src = voi_src;
                    player.play();
                    self.find('i').addClass('playing');
                    player.addEventListener('ended', function () {
                        self.find('i').removeClass('playing');
                    });
                } else {
                    if (player.currentSrc == self.attr('data-val')) {
                        player.pause();
                        self.find('i').removeClass('playing');
                    } else {
                        player.src = voi_src;
                        player.play();
                        $('.voi_play').find('i').removeClass('playing');
                        self.find('i').addClass('playing');
                        player.addEventListener('ended', function () {
                            self.find('i').removeClass('playing');
                        });
                    }
                }
            });
        },
        //根据时间设置语音气泡长度
        voice_length: function (id, time) {
            var _time = parseInt(time);
            var v_width = 1.3 * _time + '%';
            var len = $('.talk-main').find(id).length;
            alert(len);
            var obj = $('.talk-main').find(id).eq(len - 1);
            obj.css({'width': v_width});
        }
    };
    $.circle.talk.init();
})(jQuery);



