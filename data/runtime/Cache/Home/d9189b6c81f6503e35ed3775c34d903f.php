<?php if (!defined('THINK_PATH')) exit();?><div class="row">
    <?php
 $adept_type = C('ADEPT_TYPE'); if(!empty($listOne)){ foreach($listOne as $l){ $adept_arr =[]; $adept_str =''; if(strpos($l['adept_type'],'|')){ $adepts = explode('|',$l['adept_type']); foreach($adepts as $va){ $adept_arr[] = $adept_type[$va]; } $adept_str = implode('/',$adept_arr); }else{ $adept_str = $adept_type[$l['adept_type']]; } ?>
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
                    <p class="sing_2" style="margin-top:20px;"><img src="/themes/frontend/Public/home/images/home/home_icon_6.png" alt="">&nbsp;<?php echo $adept_str; ?></p>
                </div>
            </div>
            <div class="row home_tab_bottom">
                <div class="col-md-4">
                    <button type="button" class="btn btn-danger">
                        +关注
                    </button>
                </div>
                <div class="col-md-8">
                    <p class="tab_bottom_info">粉丝：<span><?php echo $l['fans'];?></span> 直播：<?php $d=ceil((strtotime($l['last_login_time'])-$l['ctime'])/(24*60*60));echo $d >0 ? $d:0 ?><span></span></p>
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