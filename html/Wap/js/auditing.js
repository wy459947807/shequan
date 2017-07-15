;
(function ($) {
    $.circle.auditing = {
        settings: {

        },
        init: function () {
            $.circle.auditing.upload_idcard();
            $.circle.auditing.upload_certify();
            $.circle.auditing.submit_info();
        },
        //上传身份证
        upload_idcard: function () {
            $('#id_card').on("change", function (event) {
                var files = event.target.files, file;
                var self = $(this);
                if (files && files.length > 0) {
                    file = files[0];
                    if (file.size > 1024 * 1024 * 2) {
                        $.circle.util.tips('图片大小不能超过2M！');
                        return !1;
                    }
                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var str = reader.result;
                        var img = new Image();
                        img.src = str;
                        self.parents('.txt').siblings('.pic').append(img);
                        self.parents('.txt').siblings('.pic').find('.g-delet').show();
                        self.hide();
                    }
                }
            });
            $('.id-card .upload').on('click', function () {
                $.circle.util.tips('您已上传过图片');
            })
            //删除身份证
            $('.id-card .g-delet').click(function () {
                var self = $(this);
                self.hide();
                self.siblings('img').remove();
                self.parents('.id-card').find('input#id_card').val('').show();
            });
        },
        //上传证书
        upload_certify: function () {
            $('.certify-upload').on('change', function (event) {
                var files = event.target.files, file;
                var self = $(this);
                if (files && files.length > 0) {
                    file = files[0];
                    if (file.size > 1024 * 1024 * 2) {
                        $.circle.util.tips('图片大小不能超过2M！');
                        return !1;
                    }
                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var str = reader.result;
                        var img = new Image();
                        img.src = str;
                        self.siblings('.pic').hide();
                        self.siblings('.pic-certify').append(img);
                        self.siblings('.pic-certify').find('.g-delet').show();
                        self.hide();
                    }
                }
            });
            //删除证书
            $('.pic-certify .g-delet').click(function () {
                var self = $(this);
                self.hide();
                self.siblings('img').remove();
                self.parents('.up-load').find('input.certify-upload').val('').show();
                self.parents('.up-load').find('.pic').show();
            });
        },
        submit_info: function () {
            $(document).on('click', '#submit_info', function () {
                var real_name, mobile, wechat, email, adept_type, card_img, cert_imgs, intro;
                real_name = $.trim($('.person-info').find('input[name="real_name"]').val());
                mobile = $.trim($('.person-info').find('input[name="mobile"]').val());
                wechat = $.trim($('.person-info').find('input[name="wechat"]').val());
                email = $.trim($('.person-info').find('input[name="email"]').val());
                adept_type = $('.person-info').find('input[name="deal"]:checked').attr('data-value');
                intro = $.trim($('.introduc').find('textarea').val());
                card_img = $('.id-card .pic').find('img').attr('src');
                if ($.circle.util.empty(card_img)) {
                    $.circle.util.tips('请先上传身份证照片！');
                    return !1;
                }
                if ($.circle.util.empty(real_name)) {
                    $.circle.util.tips('请先输入真实姓名！');
                    $('.person-info').find('input[name="real_name"]').focus();
                    return !1;
                }
                if (!$.circle.util.isName(real_name)) {
                    $.circle.util.tips('姓名应由2-4个汉字组成！');
                    $('.person-info').find('input[name="real_name"]').focus();
                    return !1;
                }
                if ($.circle.util.empty(mobile)) {
                    $.circle.util.tips('请先输入手机号码！');
                    $('.person-info').find('input[name="mobile"]').focus();
                    return !1;
                }
                var _phone = /^0?1[3|4|5|8][0-9]\d{8}$/;
                if (!_phone.test(mobile)) {
                    $.circle.util.tips('请输入正确手机号！');
                    $('.person-info').find('input[name="mobile"]').focus();
                    return !1;
                }
                if ($.circle.util.empty(intro)) {
                    $.circle.util.tips('请输入自我介绍！');
                    $('.introduc').find('textarea').focus();
                    return !1;
                }
                //证书数据
                var len = $('.pic-certify').length;
                var cert_imgs = new Array;
                var index = $('.pic-certify').attr('data-id');
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var index = $('.pic-certify').eq(i).attr('data-id');
                        var item = $('.pic-certify').eq(i).find('img').attr('src');
                        cert_imgs[index] = item;
                    }
                }
                console.log(cert_imgs);
                $.post('url', {card_img: card_img, real_name: real_name, mobile: mobile, adept_type: adept_type, intro: intro, cert_imgs: cert_imgs}, function (result) {
                    if (result.status == 1) {
                        $.circle.util.tips('提交成功！');
                    } else {
                        $.circle.util.tips(result.msg);
                    }
                }, 'json');
            })
        }
    };
    $.circle.auditing.init();
})(jQuery);
