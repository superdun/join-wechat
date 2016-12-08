<div class="extra fr">
    <div class="menu">
        <div class="colTxtTitle clearfix">
            <h2><?=$base['name']?></h2>
        </div>
        <dl>
            <?
            if ( $base['menu'] == 'search' ) {
                ?>
                <dt><a href="javascript:;" class="current"><?=$base['name']?></a></dt>
                <?
            } else {
                foreach ($categoryArray[$base['id']]['children'] as $key=>$val) {
                    ?>
                    <dt><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?><?if ($val['id'] == $second['id']) echo " class='current'"?>><?=$val['name']?></a></dt>
                    <?
                    if(count($val['children']) > 0){
                        foreach ($val['children'] as $val2) {
                            ?>
                            <dd><a href="<?=getCategoryUrl($val2['id'], $val2['url'])?>"<?=$val2['isBlank'] ?' target="_blank"' : '' ?><?if ($val2["id"] == $third['id']) echo " class='current'"?>><?=$val2["name"]?></a></dd>
                            <?
                        }
                    }
                    ?>
                    <?
                }
            }
            ?>
        </dl>
    </div>
    <!--<div class="waihui">
        <div class="waihui-tab">
            <ul>
                <li><a href="javascript:;" class="current">外汇</a></li>
                <li><a href="javascript:;">贵金属</a> </li>
                <li><a href="javascript:;">现货</a> </li>
            </ul>
        </div>
        <div class="waiihui-con">
            <div class="box" style="display: block">
                <iframe src="http://www.9forex.cn:8080/price1.html" width="100%" height="230" frameborder="0" scrolling="no"></iframe>
            </div>
            <div class="box">
                <iframe src="http://www.9forex.cn:8080/price2.html" width="100%" height="230" frameborder="0" scrolling="no"></iframe>
            </div>
            <div class="box">
                <iframe src="http://www.9forex.cn:8080/price3.html" width="100%" height="230" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </div>
    <script type="text/javascript">jQuery(".waihui").slide({ titCell:".waihui-tab li", mainCell:".waiihui-con",effect:"fade"});</script>-->

    <div class="zhongy">
        <div class="hd">
            <h2>中央银行利率</h2>
            <!--<p class="more"><a href="">查看跟多+</a></p>-->
        </div>
        <div class="bd">
            <table class="table">
                <thead>
                <tr>
                    <th class="tl">央行</th>
                    <th>利率</th>
                    <th>下次会议</th>
                </tr>
                <?
                foreach ($db->getList('info', "class_id=112101 and pic<>'' and state>0", "order by state desc, sortnum desc") as $val) {
                    ?>
                    <tr>
                        <td class="tl"><img src="<?=PATH.UPLOAD_PATH.$val['pic'] ?>" title="<?=$val['title']?>" width="28" height="18"><?=$val['title']?></td>
                        <td><?=$val['author']?></td>
                        <td><?=$val['tags']?></td>
                    </tr>
                    <?
                }
                ?>
                </thead>
            </table>
        </div>
    </div>
    <div class="team">
        <div class="hd">
            <h2>专家团队</h2>
            <p class="more"><a href="<?=getCategoryUrl(111104)?>">查看更多</a></p>
        </div>
        <div class="bd">
            <?
            foreach ($db->getList('info', "class_id=111104 and state>0 and pic<>''", "order by state desc, sortnum desc", 'limit 3') as $val) {
                ?>
                <div class="team-item">
                    <div class="pic fl"><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank" title="<?=$val['title']?>"><img src="<?=PATH.UPLOAD_PATH.$val['pic'] ?>" title="<?=$val['title']?>" width="100" height="100"> </a></div>
                    <div class="info fl">
                        <h2><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank"><?=$val['title']?></a> </h2>
                        <div class="txt"><?=$val['title2']?></div>
                        <p class="btn"><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank">独家观点</a></p>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>
    <div class="paihang">
        <div class="hd">
            <h2>热搜排行</h2>
            <!--<p class="more"><a href="">查看更多</a></p>-->
        </div>
        <div class="bd">
            <ul class="listinfo_03">
                <?
                foreach ($db->getList('info', "class_id=111106 and state>0", "order by state desc, sortnum desc", 'limit 6') as $key=>$val) {
                    ?>
                    <li><em><?=$key+1?></em><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank" title="<?=$val['title']?>"><?=leftStr($val['title'], 20)?></a> </li>
                    <?
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="rt-ad">
        <?
        if($banner = $db->getByWhere("banner", "class_id=8 and state>0 and pic<>''")){
            ?>
            <a href="<?=empty($banner['url']) ? "javascript:;" : $banner['url'] .'" target="_blank'?>"><img src="<?=PATH.UPLOAD_PATH.$banner['pic']?>" width="360" height="200"> </a>
            <?
        }
        ?>
    </div>
    <div class="video">
        <div class="hd">
            <h2>专家视频</h2>
            <p class="more"><a href="<?=getCategoryUrl(111105)?>">查看更多</a></p>
        </div>
        <div class="bd">
            <?
            $id = 0;
            if ($data = $db->getByWhere( 'info', "class_id=111105 and pic<>'' and state>0", "order by state desc, sortnum desc")) {
                $id = $data['id'];
                ?>
                <div class="videoPic">
                    <a href="<?=getDisplay($data["id"], $data['website']) ?>" target="_blank" title="<?=$data['title']?>"><img src="<?=PATH.UPLOAD_PATH.$data['pic'] ?>" title="<?=$data['title']?>" width="360" height="180"> </a>
                    <p class="name"><a href="<?=getDisplay($data["id"], $data['website']) ?>" target="_blank" title="<?=$data['title']?>"><?=leftStr($data['title'], 18)?></a></p>
                </div>
                <?
            }
            foreach ($db->getList('info', "class_id=111105 and pic<>'' and state>0 and id not in(".$id.")", "order by state desc, sortnum desc", 'limit 2') as $val) {
                ?>
                <div class="video-item clearfix">
                    <div class="pic fl"><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank" title="<?=$val['title']?>"><img src="<?=PATH.UPLOAD_PATH.$val['pic'] ?>" title="<?=$val['title']?>" width="120" height="80"> </div>
                    <div class="info fr">
                        <h2><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank" title="<?=$val['title']?>"><?=leftStr($val['title'], 20)?></a></h2>
                        <div class="txt"><?=leftStrRemoveHtml($val['content'], 40)?><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank"> [点击播放]</a>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>