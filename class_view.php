<?
require_once("init.php");

$id	= (int)$_GET["id"];
if ($id < 1) {
    header("location: error.php"); exit;
}
if (!$data = $db->getByWhere('kaiban', "id=$id and status>0")) {
    header("location: error.php"); exit;
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>
<section class="banji-b">
    <div class="hd">
        <h2><?=$data['title']?>课程介绍</h2>
    </div>
    <div class="bd">
        <div class="info">
            <?=replaceUploadBack($data['content']);?>
        </div>
    </div>
    <div class="hd">
        <h2>卓因简介</h2>
    </div>
    <div class="info">
        <?
        if ($intro = $db->getByWhere('info', "class_id=102101 and state>0", 'order by state desc, sortnum desc')) {
        ?>
            <?= replaceUploadBack($intro['content']); ?>
        <?
        }
        ?>
    </div>
    <div class="weix">
        <div class="txt">请关注“卓因青少年创意工场”<br>长按下面二维码即可关注我们</div>
        <div class="pic"><img src="<?=PATH.UPLOAD_PATH.$site['wechat']?>"> </div>
    </div>
    <div class="btn"><a href="<?=PATH?>class_join.php?id=<?=$id?>"><img src="images/sk-2.jpg"> </a> </div>
    <div class="hp-btn hide"><a class="yaoqing"><img src="images/hp-1.jpg"></a></div>
</section>

<div class="yaoqingimg"><img src="<?=PATH?>images/yaoqing.png"></div>
<script>
    //邀请
    $('.yaoqing').on('click', function(){
        $('.yaoqingimg').fadeIn('slow');
    });
    $('.yaoqingimg').on('click', function(){
        $(this).fadeOut('slow');
    });
</script>

</body>
</html>
