<footer class="footer">
    <div class="foot wrap clearfix">
        <div class="foot-item clearfix">
            <dl>
                <dt>集团简介</dt>
                <?
                foreach ($categoryArray[102]['children'] as $key=>$val) {
                ?>
                    <dd><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?><?if ($val['id'] == $second['id']) echo " class='current'"?>><?=$val['name']?></a></dd>
                <?
                }
                ?>
            </dl>
            <dl>
                <dt>美尚产业</dt>
                <?
                foreach ($categoryArray[103]['children'] as $key=>$val) {
                    ?>
                    <dd><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?><?if ($val['id'] == $second['id']) echo " class='current'"?>><?=$val['name']?></a></dd>
                    <?
                }
                ?>
            </dl>
            <dl>
                <dt>新闻中心</dt>
                <?
                foreach ($categoryArray[106]['children'] as $key=>$val) {
                    ?>
                    <dd><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?><?if ($val['id'] == $second['id']) echo " class='current'"?>><?=$val['name']?></a></dd>
                    <?
                }
                ?>
            </dl>
        </div>
        <div class="foot-cx">
            <ul>
                <li>
                    <label class="label">授权查询</label>
                    <div class="input-box"><input></div>
                </li>
                <li>
                    <label class="label">防伪码查询</label>
                    <div class="input-box"><input></div>
                </li>
            </ul>
        </div>
        <div class="foot-phone">
            <ul>
                <li>
                    <p><img src="images/erweima_2.jpg"><Br>美商会官方微博 </p>
                </li>
                <li>
                    <p><img src="<?=PATH . UPLOAD_PATH . $site['wechat'] ?>"/><Br>美商会官方微信 </p>
                </li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <div class="wrap">
            <span>
                <?
                foreach ($categoryArray as $key=>$val) {
                ?>
                    <a<?=$val['menu'] == $base['menu'] ? ' class="current"' : '' ?> href="<?=getCategoryUrl($val['id'], $val['url'])?>"<?=$val['isBlank'] ?' target="_blank"' : '' ?>><?= $val['name'] ?></a>
                <?
                }
                ?>
            </span>
            <?=$site["copyright"]?>
        </div>
    </div>
</footer>

<script language="javascript" type="text/javascript" src="<?=PATH?>js/common.js"></script>
<!--[if lte IE 6]><script src="<?=PATH?>js/iepng.js"></script><![endif]-->
<?
require_once("adver.php");
$db->close();
echo "<div style='display: none'>".$site['javascript']."</div>";
?>
