<header>
    <div class="topArea">
        <div class="wrap clearfix">
            <div class="lt-top fl">
                <img src="images/top_tel.png"><em><?=$site['tel']?></em><img src="images/top_email.png"><em><a href="mailto://<?=$site['email']?>"><?=$site['tel']?></a> </em>
            </div>
            <div class="rt-top fr">
                <ul>
                    <li><a href="" class="qq">qq</a> </li>
                    <li><a href="" class="zone">zone</a> </li>
                    <li><a href="" class="weixin">weixin</a> </li>
                    <li><a href="" class="weibo">weibo</a> </li>
                    <li><a href="" class="renren">renren</a> </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="logoArea wrap clearfix">
        <h1 class="logo"><a href="<?=PATH?>"><img src="<?=PATH . UPLOAD_PATH . $site['logo'] ?>"/></a></h1>
        <div class="rtcon">
            <div class="nav">
                <ul>
                    <?
                    foreach ($categoryArray as $key=>$val) {
                    ?>
                        <li>
                            <a<?=$val['menu'] == $base['menu'] ? ' class="current"' : '' ?> href="<?=getCategoryUrl($val['id'], $val['url'])?>"<?=$val['isBlank'] ?' target="_blank"' : '' ?>><em class="ch"><?= $val['name'] ?></em><em class="en"><?=$val['menu']?></em></a>
                            <?
                            if (SUBNAV && $val['children']) {
                            ?>
                                <div class="subNav">
                                    <?
                                    foreach ($val['children'] as $val2) {
                                        ?>
                                        <a href="<?=getCategoryUrl($val2['id'], $val2['url'])?>"<?=$val2['isBlank'] ?' target="_blank"' : '' ?>><?=$val2["name"] ?></a>
                                        <?
                                    }
                                    ?>
                                </div>
                            <?
                            }
                            ?>
                        </li>
                        <?
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <?
    if ($base['menu'] == 'index') {
    ?>
        <div class="banner mob-banner swiper-container">
            <div class="bd mob-bd swiper-wrapper">
                <?
                $banner = $db->getList('banner', "class_id=1 and state>0 and pic<>''", "order by sortnum asc");
                foreach ($banner as $val) {
                    if(empty($val["website"])){
                    ?>
                        <div class="swiper-slide" style="background: url(<?=PATH . UPLOAD_PATH . $val['pic'] ?>) no-repeat 50% center;"></div>
                    <?
                    } else {
                    ?>
                        <div class="swiper-slide" style="background: url(<?=PATH . UPLOAD_PATH . $val['pic'] ?>) no-repeat 50% center;">
                            <a target="_blank" href="<?= $val["website"] ?>"></a>
                        </div>
                    <?
                    }
                }
                ?>
            </div>

            <?
            if(count($banner) > 1){
                ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
                <?
            }
            ?>
        </div>
        <script type="text/javascript">
            new Swiper('.banner',{
                pagination : '.swiper-pagination',
                prevButton:'.swiper-button-prev',
                nextButton:'.swiper-button-next',
                paginationClickable :true,
                autoplay : 5000
            });
        </script>
    <?
    } else {
    ?>
        <div class="ibanner">
            <div class="swiper-wrapper">
                <?
                $pic = $second['pic'] ? $second['pic'] : $base['pic'];
                if ($pic) {
                    ?>
                    <div class="swiper-slide" style="background: url(<?=PATH . UPLOAD_PATH . $pic ?>) no-repeat 50% center;"></div>
                    <?
                } else {
                    ?>
                    <div class="swiper-slide" style="background: url(images/banner.jpg) no-repeat 50% center;"></div>
                    <?
                }
                ?>
            </div>
        </div>
        <?
    }
    ?>
</header>