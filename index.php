<?
require("init.php");
$base['menu']   = "index";
header("location: ".PATH."member"); exit();
?>

<!doctype html>
<html>

<? require_once("head.php"); ?>

<body class="index">

<? require_once("header.php"); ?>

<section class="wrap">
    <div class="cy-wrap clearfix">
        <?
        foreach ($categoryArray[103]['children'] as $key=>$val) {
        ?>
            <div class="cy-item">
                <div class="pic">
                    <a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?> class="cy-<?=$key+1?>"><span></span></a>
                </div>
                <div class="info current">
                    <h2><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?>><?=$val['name']?></a> </h2>
                    <div class="txt"><?=leftStrRemoveHtml($val['content'], 0)?></div>
                    <div class="detail">
                        <a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?>>查看详情</a>
                    </div>
                </div>
            </div>
        <?
        }
        ?>
    </div>
    <div class="about hTitle">
        <div class="hd">
            <h2><em>美尚集团</em></h2>
        </div>
        <div class="bd clearfix">
            <?
            if ($data = $db->getByWhere( 'info', "class_id=102101 and pic<>'' and state>0", "order by state desc, sortnum desc")) {
            ?>
                <div class="intro fl">
                    <img src="<?=PATH.UPLOAD_PATH.$data["pic"]?>" width="300" height="250">
                </div>
                <div class="info fl">
                    <h2><?=$data["title"]?></h2>
                    <div class="txt"><?=leftStrRemoveHtml($data["content"], 150)?></div>
                    <div class="detail"><a href="<?=getCategoryUrl(102101)?>">点击查看+</a> </div>
                </div>
            <?
            }
            ?>

            <div class="picshow fr">
                <div class="p-bd"><img src="images/p248x248.jpg"></div>
                <div class="p-hd">
                    <ul>
                        <li class="on"></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="xueyuan hTitle">
        <div class="hd">
            <h2><em>微商学院</em></h2>
            <div class="txt"><?=$db->getField("info_class", "content", "id=104")?></div>
        </div>
        <div class="bd">
            <ul class="listinfo_01 clearfix">
                <?
                foreach ($db->getList( 'info', "class_id=104102 and pic<>'' and state>0", "order by state desc, sortnum desc", 'limit 4') as $val) {
                ?>
                    <li>
                        <div class="list-pic">
                            <img src="<?=PATH.UPLOAD_PATH.$val['pic'] ?>" title="<?=$val['title']?>" width="278" height="198">
                            <div class="plus"><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank"><span><img src="images/plus.png"></span></a></div>
                        </div>
                        <p class="name"><a href="<?=getDisplay($val["id"], $val['website']) ?>" target="_blank" title="<?=$val['title']?>"><?=leftStr($val['title'], 20)?></a></p>
                    </li>
                <?
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="product hTitle">
        <div class="hd">
            <h2><em>产品系列</em></h2>
            <div class="txt"><?=$db->getField("info_class", "content", "id=105")?></div>
        </div>
        <div class="bd">
            <ul class="listinfo_02 clearfix">
                <?
                foreach ($categoryArray[105]['children'] as $key=>$val) {
                ?>
                    <li>
                        <diV class="list-pic"><a href=""><img src="images/p266x196.jpg"> </a></diV>
                        <dl>
                            <dt><a href="<?=getCategoryUrl($val['id'], $val['url'])?>" <?=$val['isBlank'] ?' target="_blank"' : '' ?>><?=$val['name']?></a></dt>
                            <dd><?=leftStrRemoveHtml($val['content'], 0)?></dd>
                        </dl>
                    </li>
                <?
                }
                ?>
            </ul>
        </div>
    </div>
</section>
<section class="sbanner">
    <div class="bd"><img src="images/sbanner.jpg"> </div>
</section>
<section class="wrap">
    <div class="media hTitle">
        <div class="hd">
            <h2><em>媒体报道</em></h2>
            <div class="txt">
                安徽微商学院为您提供权威、实用、全面的微商资讯和指导，全景展现健康可持续的微电商生态圈<Br>
                专业组织策划高品质微商活动，倡导优质健康的微商生活
            </div>
        </div>
        <div class="bd">
            <ul class="listinfo_03 clearfix">
                <li class="current">
                    <div class="list-pic"><a href=""><img src="images/p348x198.jpg"></a></div>
                    <dl>
                        <dt><a href=""><em>【行业动态】</em>2016“美商会微商学院”交流课</a> </dt>
                        <dd>2015年2月10日，中央电视台举办的“影响力对话——对话微商”
                            栏目，由著名主持人赵保乐与客座嘉宾美尚集团……<a href=""> [阅读更多+]</a></dd>
                    </dl>
                </li>
                <li>
                    <div class="list-pic"><a href=""><img src="images/p348x198.jpg"></a></div>
                    <dl>
                        <dt><a href=""><em>【行业动态】</em>2016“美商会微商学院”交流课</a> </dt>
                        <dd>2015年2月10日，中央电视台举办的“影响力对话——对话微商”
                            栏目，由著名主持人赵保乐与客座嘉宾美尚集团……<a href=""> [阅读更多+]</a></dd>
                    </dl>
                </li>
                <li>
                    <div class="list-pic"><a href=""><img src="images/p348x198.jpg"></a></div>
                    <dl>
                        <dt><a href=""><em>【行业动态】</em>2016“美商会微商学院”交流课</a> </dt>
                        <dd>2015年2月10日，中央电视台举办的“影响力对话——对话微商”
                            栏目，由著名主持人赵保乐与客座嘉宾美尚集团……<a href=""> [阅读更多+]</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
</section>

<? require_once("footer.php"); ?>

</body>
</html>
