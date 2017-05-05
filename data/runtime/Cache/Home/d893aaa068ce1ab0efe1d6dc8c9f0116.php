<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>炒股培训课程_期货培训课程_股票期货高手-十年金融网</title>
    <meta name="keywords" content="炒股培训课程、期货培训课程、期货培训高手、股票培训高手"/>
    <meta name="description" content="深圳十年网络科技有限公司（十年金融网）是一家专注于金融领域教育与培训的线上平台型公司。以传播正确的金融投资理念为己任，关注于投资者风险教育与知识传播。"/>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <!-- Bootstrap -->
    <link href="/themes/frontend/Public/home/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/frontend/Public/home/bower_components/bootstrap/dist/css/bootstrap-switch.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/themes/frontend/Public/home/css/global.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/themes/frontend/Public/home/js/jquery-1.9.1.min.js"></script>
    <link rel="stylesheet" href="/themes/frontend/Public/home/bower_components/swiper/dist/css/swiper.min.css">
    <script src="/themes/frontend/Public/home/bower_components/swiper/dist/js/swiper.jquery.min.js"></script>
    <script src="/themes/frontend/Public/home/lib/date/WdatePicker.js"></script>
    <script src="/themes/frontend/Public/home/lib/layer/layer.js"></script>
    <script src="/themes/frontend/Public/home/js/common.js"></script>
</head>
<body>
<!--导航部分-->
<div class="header" style="position: relative;">
    <ul class="clearfix">
        <li><a href="http://www.10jrw.com">首页</a></li>
        <li><a href="http://www.10jrw.com/cream.html">精华专区</a></li>
        <li><a href="http://www.10jrw.com/live.html">在线分享</a></li>
        <li><a href="http://www.10jrw.com/basic/">基础知识</a></li>
        <li><a href="http://www.10jrw.com/welfare/signup.html">线下培训</a></li>
        <li><a href="http://www.10jrw.com/news_18/">主题活动</a></li>
        <li><a href="http://www.10jrw.com/rank.html">实战排行</a></li>
        <!--<li><a href="#">国诚投顾专区</a></li>--><!--<li><a href="#">金融服务专区</a></li>-->
        <li>
            <a href="/moni.html">模拟炒股</a></li>
        <li><a href="http://www.10jrw.com/playback_94.html">张宏建</a></li>
        <li><a href="http://www.10jrw.com/playback_1.html">邵立胜</a></li>
    </ul>
</div>
<!--导航部分结束-->
<div id="header_nav">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-9 col-md-3">
                <div class="row">
                    <?php
 if(!empty($user)){ ?>
                    <div class="col-md-8 text-right">
                        <?php if(!empty($user['avatar'])) { ?>
                        <img width="50" height="50" src="<?php echo sp_get_asset_upload_path($user['avatar']);?>" alt="">
                        <?php } else {?>
                        <img width="50" height="50" src="/themes/frontend/Public/home/images/public_header/header_img.png"
                             alt="">
                        <?php } ?>

                        &nbsp;&nbsp;<?php echo $user['user_nicename'];?>
                    </div>
                    <div class="col-md-4 text-center" style="height:50px; line-height: 50px;">
                        <a href="<?php echo U('user/index/logout');?>" id="user_tunchu">退出</a>
                    </div>
                    <?php }else{ ?>
                    <div class="col-md-4 text-center" style="height:50px; line-height: 50px; width: 200px">
                        <a target="_blank" href="<?php echo ($jrw_url); ?>/userlogin.html">登录</a> |
                        <a target="_blank" href="<?php echo ($jrw_url); ?>/userregister.html">注册</a> |
                        <a target="_blank" href="<?php echo U('tlive/index/index');?>">高手登录</a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-6">
                    <img src="/themes/frontend/Public/home/images/public_header/header_logo.png" alt="">
                </div>
                <div class="col-md-6 text-center">
                    <img src="/themes/frontend/Public/home/images/public_header/header_logo_1.png" alt="">
                </div>
            </div>
        </div>
        <div class="col-md-offset-1  col-md-6" style="margin-top:26px; font-size: 16px;">
            <div class="row">
                <div class="col-md-offset-2 col-md-2 text-right">
                    <a id="nav_one_1" href="/">社圈首页</a>
                </div>
                <div class="col-md-2 text-right">
                    <a id="nav_one_2" href="<?php echo U('course/index');?>">培训课程</a>
                </div>
                <div class="col-md-2 text-right">
                    <a id="nav_one_3" href="<?php echo U('test/user');?>">个人中心</a>
                </div>
                <div class="col-md-2 text-right">
                    <a id="nav_one_4" href="<?php echo U('test/index');?>">民间高手</a>
                </div>
                <div class="col-md-2 text-right">
                    <a id="nav_one_5" href="<?php echo U('test/help_page');?>">帮助中心</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $portal_index_lastnews="1,2"; $portal_hot_articles="1,2"; $portal_last_post="1,2"; $tmpl=sp_get_theme_path(); $default_home_slides=array( array( "slide_name"=>"", "slide_pic"=>$tmpl."Public/home/images/home/banner_1.png", "slide_url"=>"", ), array( "slide_name"=>"", "slide_pic"=>$tmpl."Public/home/images/home/banner_1.png", "slide_url"=>"", ), array( "slide_name"=>"", "slide_pic"=>$tmpl."Public/home/images/farmerkiller/apply_banner.png", "slide_url"=>"", ), ); ?>
<!--以上是公共部分-->
<link rel="stylesheet" href="/themes/frontend/Public/home/css/home.css">
<div id="home_banner" class="container-fluid" >
    <div class="swiper-container2 swiper-container-horizontal" style="overflow: hidden;">
        <div class="swiper-wrapper" style="" >
            <?php $home_slides=sp_getslide("portal_index"); $home_slides=empty($home_slides)?$default_home_slides:$home_slides; ?>
            <?php if(is_array($home_slides)): foreach($home_slides as $key=>$vo): ?><div class="swiper-slide swiper-slide-active" >
                    <a href="<?php echo ($vo["slide_url"]); ?>"><img style="width:100%;" src="<?php echo sp_get_asset_upload_path($vo['slide_pic']);?>" alt=""></a>
                </div><?php endforeach; endif; ?>
        </div>
    </div>
    <!--<img src="images/home/banner_1.png" alt="">-->
</div>
<div class="container">
    <div id="today_recommend" style="position: relative;">
        <h3>今日推荐</h3>
        <div  class="swiper-container">
            <div class="swiper-wrapper">

                <?php
 if(!empty($tadayTopList)){ foreach($tadayTopList as $l){ ?>
                <div class="swiper-slide" data-url="<?php echo U('live/index',array('t_id'=>$l['id']));?>">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="img_box">
                                <img width="100%" src="<?php echo sp_get_asset_upload_path($l['avatar']) ;?>" alt="">
                                <!--<div class="l_icon">-->
                                <!--直播中-->
                                <!--</div>-->
                            </div>
                            <p class="text-center l_tearch_name"><?php echo $l['real_name'];?></p>
                        </div>
                        <div class="col-md-7">
                            <p class="l_rennum"><span><?php echo $l['msgs'];?></span>人参与</p>
                            <p class="l_text"><?php echo mb_substr($l['intro'],0,30);?>...</p>
                            <p class="l_reply"><i><img src="/themes/frontend/Public/home/images/home/home_icon_1.png" alt=""></i><?php echo date('H:i',strtotime($l['last_reply_time']));?> 回复文本</p>
                            <button type="button" class="btn btn-danger">+关注</button>
                        </div>
                    </div>
                    <div class="l_live_icon">
                        <?php if($l['type'] == 1){ ?>
                        <img src="/themes/frontend/Public/home/images/home/home_jigou.png" alt="">
                        <?php }else{ ?>
                        <img src="/themes/frontend/Public/home/images/home/home_gaoshou.png" alt="">
                        <?php }?>
                    </div>
                </div>
                <?php }} ?>

            </div>
            <!-- 如果需要导航按钮 -->
            <!--<div style="background:url(/themes/frontend/Public/home/images/home/home_left.png);"  class="swiper-button-prev"></div>-->
            <!--<div style="background:url(/themes/frontend/Public/home/images/home/home_right.png);" class="swiper-button-next"></div>-->
        </div>
        <div class="swiper-button-prev"  style="position: absolute;left:-50px; top:60%; background:none;">
            <div style="background:url(/themes/frontend/Public/home/images/home/home_left.png); height:44px; width:27px;">
            </div>
        </div>
        <div class="swiper-button-next" style="position: absolute;right:-50px; top:60%; background:none;">
            <div style="background:url(/themes/frontend/Public/home/images/home/home_right.png); height:44px; width:27px;">
            </div>
        </div>
    </div>
    <div id="home_main_context">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="tabbable" id="tabs-698440">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#panel-726860" onclick="getKillerList(0, 1)">综合排序</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#panel-174183" onclick="getKillerList(1, 1)">直播时间</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#panel-174184" onclick="getKillerList(2, 1)">粉丝数</a>
                            </li>
                        </ul>
                        <div id="home_tab_content"  class="tab-content">
                            <div class="tab-pane active" id="killerList">
                                <div class="row">
    <?php
 if(!empty($killerList)){ foreach($killerList as $l){ ?>
    <div class="col-md-4">
        <div class="home_item"  data-url="<?php echo U('live/index',array('t_id'=>$l['id'])); ?>">
            <div class="row">
                <div class="col-md-4">
                    <div style="position: relative;">
                        <img width="69" height="71" src="<?php echo sp_get_asset_upload_path($l['avatar']) ;?>" alt="">
                        <!--<div class="absoult_box">-->
                        <!--直播中-->
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-8">
                    <p class="tab_title"><?php echo $l['real_name'];?><img src="/themes/frontend/Public/home/images/home/home_icon_3.png" alt=""></p>
                    <p class="sing_2" style="margin-top:20px;"><img src="/themes/frontend/Public/home/images/home/home_icon_6.png" alt="">&nbsp;<?php echo $l['adept_str']; ?></p>
                </div>
            </div>
            <div class="row home_tab_bottom">
                <div class="col-md-4">
                    <button type="button" class="btn btn-danger">
                        +关注
                    </button>
                </div>
                <div class="col-md-8">
                    <p class="tab_bottom_info">粉丝：<span><?php echo $l['fans'];?></span> 直播：<?php echo $l['live_num']; ?><span></span></p>
                </div>
            </div>
            <div class="tab_biaoqian">
                <?php if($l['type'] == 1){ ?>
                <img src="/themes/frontend/Public/home/images/home/home_jigou.png" alt="">
                <?php }else{ ?>
                <img src="/themes/frontend/Public/home/images/home/home_gaoshou.png" alt="">
                <?php }?>
            </div>
        </div>
    </div>
    <?php  } }else{ ?>
    暂无数据
    <?php } ?>
</div>
<script>
    var pageInfo={};
    pageInfo['page']        =<?php echo $pageInfo['page']?>;
    pageInfo['pageLimit']   =<?php echo $pageInfo['pageLimit']?>;
    pageInfo['num']         =<?php echo $pageInfo['num']?>;
    pageInfo['pageNum']     =<?php echo $pageInfo['pageNum']?>;
    getTemplate(pageInfo, '<?php echo U('index/ajaxPage');?>', "#page");
</script>
                            </div>
                        </div>
                        <!--分页部分开始-->
                        <div id="page">
                            <div class="pages">
    <div id="Pagination"></div>
    <div class="searchPage">
        <span class="page-sum">共<strong class="allPage"> <?php echo $pageInfo['pageNum']?> </strong>页</span>
        <!--<span class="page-go">跳转<input type="text">页</span>-->
        <!--<a href="javascript:;" class="page-btn">GO</a>-->
    </div>
</div>
<script src="/themes/frontend/Public/home/js/jquery.pagination.js"></script>
<script>
    var pageIndex = <?php echo $pageInfo['page']-1?>;     //页面索引初始值
    var pageSize =  <?php echo $pageInfo['pageLimit']?>;     //每页显示条数初始化，修改显示条数，修改这里即可
    var pcount =    <?php echo $pageInfo['pageNum']?>;
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 30,
        // 如果需要前进后退按钮
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
    });

    var swiper2 = new Swiper('.swiper-container2', {
        pagination: '.swiper-pagination2',
        loop : true,
        autoplay : 4000,
        paginationClickable: true
    });

    $(document).ready(function() {
   
        $("#Pagination").pagination(pcount,{
            callback: PageCallback,  //PageCallback() 为翻页调用次函数。
            link_to:"javascript:void(0)",
            current_page: pageIndex,   //当前页索引
        });
    });

</script>
                        </div>

                    </div>
                </div>
                <div class="col-md-3 shares_top5">
                    <div class="text-center">
                        <a target="_blank" href="/index.php?g=&m=test&a=index"><h5>申请开通我的社圈</h5></a>
                        <img style="width:37px;display: inline-block;margin-top:-52px; margin-left:176px;"" src="/themes/frontend/Public/home/images/home/hand.gif" alt="">
                        <h4>股票高手 TOP5</h4>
                        <br>
                        
                        <?php
 if(!empty($gupiaoTopList)){ foreach($gupiaoTopList as $key=>$l){ ?>
                        <a href="/index.php?g=&m=live&a=index&t_id=$l['id']">
                            <p>
                                <i style="width:26px; display: inline-block"><img style="width:100%;" src="/themes/frontend/Public/home/images/home/home_top_<?php echo $key+1?>.png" alt=""></i>
                                <i style="width:50px;display: inline-block"> <img width="35" height="35" src="<?php echo sp_get_asset_upload_path($l['avatar']) ;?>" alt=""></i>
                                <em style="font-style: normal;width:80px; display: inline-block"><?php echo $l['real_name'];?></em>
                                <span style="width:60px; display: inline-block; color:#fc3737;" class="text-danger">↑18</span>
                            </p>
                        </a>
                        <?php }} ?>
    
                        
                        <h4>期货高手 TOP5</h4>
                        <br>

                        <?php
 if(!empty($qihuoTopList)){ foreach($qihuoTopList as $key=>$l){ ?>
                        <a href="/index.php?g=&m=live&a=index&t_id=$l['id']">
                            <p>
                                <i style="width:26px; display: inline-block"><img style="width:100%;" src="/themes/frontend/Public/home/images/home/home_top_<?php echo $key+1?>.png" alt=""></i>
                                <i style="width:50px;display: inline-block"> <img width="35" height="35" src="<?php echo sp_get_asset_upload_path($l['avatar']) ;?>" alt=""></i>
                                <em style="font-style: normal;width:80px; display: inline-block"><?php echo $l['real_name'];?></em>
                                <span style="width:60px; display: inline-block; color:#fc3737;" class="text-danger">↑18</span>
                            </p>
                        </a>
                        <?php }} ?>
                        
                        
                        <h4>外汇高手 TOP5</h4>
                        <br>
                        <?php
 if(!empty($waihuiTopList)){ foreach($waihuiTopList as $key=>$l){ ?>
                        <a href="/index.php?g=&m=live&a=index&t_id=$l['id']">
                            <p>
                                <i style="width:26px; display: inline-block"><img style="width:100%;" src="/themes/frontend/Public/home/images/home/home_top_<?php echo $key+1?>.png" alt=""></i>
                                <i style="width:50px;display: inline-block"> <img width="35" height="35" src="<?php echo sp_get_asset_upload_path($l['avatar']) ;?>" alt=""></i>
                                <em style="font-style: normal;width:80px; display: inline-block"><?php echo $l['real_name'];?></em>
                                <span style="width:60px; display: inline-block; color:#fc3737;" class="text-danger">↑18</span>
                            </p>
                        </a>
                        <?php }} ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="foot">
    <div class="public_foot clearfix">
        <div class="imgbox"><a href="http://www.10jrw.com">
            <img src="/themes/frontend/Public/home/images/public_foot/font_logo.png" alt=""></a>
        </div>
        <div class="item1 item">
            <h4>十年赢家</h4>
            <ul>
                <li><a href="http://www.10jrw.com/aboutus/11.html">新手入门</a></li>
                <li><a href="http://www.10jrw.com/aboutus/16.html">网站地图</a></li>
            </ul>
        </div>
        <div class="item2 item">
            <h4>关于我们</h4>
            <ul>
                <li><a href="http://www.10jrw.com/aboutus/2.html">公司介绍</a></li>
                <li><a href="http://www.10jrw.com/aboutus/18.html">深圳国诚介绍</a></li>
                <li><a href="http://www.10jrw.com/aboutus/3.html">联系我们</a></li>
                <li><a href="http://www.10jrw.com/aboutus/14.html">加入我们</a></li>
            </ul>
        </div>
        <div class="item3 item">
            <h4>赢家声明</h4>
            <ul>
                <li><a href="http://www.10jrw.com/aboutus/4.html">免责条款</a></li>
                <li><a href="http://www.10jrw.com/aboutus/10.html">版权声明</a></li>
               <!--<li><a href="#">意见反馈</a></li>-->
                <li><a href="http://www.10jrw.com/aboutus/15.html">商务合作</a></li>
            </ul>
        </div>
        <div class="item4 item">
            <h4>帮助信息</h4>
            <ul>
                <li><a href="#">积分说明</a></li>
                <li><a href="#">邀请码说明</a></li>
                <li><a href="http://www.10jrw.com/home/help/course_time.html">课程时间表</a></li>
                <li><a href="http://www.10jrw.com/home/help/course_fee.html">课程费用表</a></li>
            </ul>
        </div>
        <div class="item5 item">
            <h4>全国免费热线</h4>
            <h3>400-788-3603</h3>
        </div>
        <div class="item6 item">
            <p>在线咨询</p>
            <div class="img1">
                <img src="/themes/frontend/Public/home/images/public_foot/font_erweima.png" alt="">
                <span>扫一扫 微信公众号</span>
            </div>
        </div>
    </div>
    <div class="zhengJianHui clearfix">
        <div class="f_img_box">
            <img src="/themes/frontend/Public/home/images/public_foot/font_icon.png" alt="">
        </div>
        <div class="f_text">
            十年赢家网的在线课程为深圳市国诚投资咨询的在线培训项目<br/>
            深圳市国诚投资咨询有限公司<br>
            中华人民共和国经营证券期货业务许可证，流水号：000000000093<br>
            提醒广大投资者：选择正规合法的证券投资咨询机构提供服务<br>
        </div>
    </div>
    <div class="f_banquan">
        <p>2013-2016 深圳十年网络科技有限公司 粤ICP备16021529号-2  </p>
        <p>网络文化许可证 编号：粤网文（2016） 1351-304</p>
        <p>广播电视节目制作经营许可证 编号（粤）字第01834号</p>
        <p>增值电信业务经营许可证 编号：粤B2-20160597 </p>
        <p>2015-2016 保留所有权利<br/>投资有风险，理财需谨慎</p>
    </div>
    <!--<p class="firendlink">
        友情链接：
        <a href="http://www.zbmf.com" target="_blank">模拟炒股</a>&nbsp;|
        <a href="http://www.xingumin.net" target="_blank">股票基本知识</a>&nbsp;|
        <a href="http://www.lufaxy.com" target="_blank">陆长网</a>&nbsp;|
        <a href="http://www.kashen.cm" target="_blank">卡神网</a>&nbsp;|
        <a href="http://www.jczks.com" target="_blank">原油投资</a>&nbsp;|
        <a href="http://www.xapeizi.cn/" target="_blank">西安期货配资</a>&nbsp;|
        <a href="http://www.xinniumoney.com/" target="_blank">股票分析</a>&nbsp;|
        <a href="http://www.maitl.com.cn" target="_blank">p2p网贷软件系统</a>
    </p>-->
    <div class="container">
        <div class="hezuo_box">
            <span>合作单位：</span>
            <a rel="nofollow" href="http://www.hexun.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_a.png">
            </a>
            <a rel="nofollow" href="http://www.qhrb.com.cn/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_b.png">
            </a>
            <a rel="nofollow" href="http://www.stockstar.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_c.png">
            </a>
            <a rel="nofollow" href="http://www.cctvfinance.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_d.png">
            </a>
            <a rel="nofollow" href="http://www.jrj.com.cn/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_e.png">
            </a>
            <a rel="nofollow" href="http://www.7hcn.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_f.png">
            </a>
            <a rel="nofollow" href="http://www.zlw.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/zhongliang_e.jpg">
            </a>
            <a rel="nofollow" href="http://www.wenhua.com.cn/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/wenhua.jpg">
            </a>
            <a rel="nofollow" href="https://xueqiu.com/#/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/xueqiu.png">
            </a>
			<a rel="nofollow" href="http://www.zbmf.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_g.png">
            </a>
			<a rel="nofollow" href="http://www.xijinfa.com/" target="_blank">
                <img src="/themes/frontend/Public/home/images/public_foot/sponsor_h.png">
            </a>
        </div>
    </div>
</div>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/themes/frontend/Public/home/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/themes/frontend/Public/home/bower_components/bootstrap/dist/js/bootstrap-switch.min.js"></script>
<script src="/themes/frontend/Public/home/js/tougu.js"></script>
</body>
</html>
<script src="/themes/frontend/Public/home/js/home.js"></script>
<script src="/themes/frontend/Public/home/js/strongWind.js"></script>

<script>
    $(document).ready(function () {

    });

    var killerInfo = {
        page: 1,
        pageLimit: 30,
        status: 1,
        orderType: 0
    };

    //获取高手列表
    function getKillerList(orderType, page) {
        killerInfo['orderType'] = orderType ? orderType : 0;
        killerInfo['page'] = page ? page : 1;
        getTemplate(killerInfo, '/', "#killerList");
    }

    //翻页调用
    function PageCallback(index, jq) {
        getKillerList(killerInfo['orderType'], index + 1);
    }


</script>