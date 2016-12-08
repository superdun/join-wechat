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

<section class="kaib-g">
    <div class="kaib-g-pic">
        <img src="images/kaib-g.jpg">
        <div class="txt">
            欢迎您的加入！<Br>
            <?
            if($data['num']>0){
            ?>
                距离开班还差<Br>
                [<em><?=$data['num']?></em>]人
            <?
            } else {
            ?>
                即将开班，请等待通知
            <?
            }
            ?>
        </div>
    </div>
    <div class="hp-btn" style="padding-top: 0"><a class="yaoqing"><img src="images/hp-1.jpg"></a></div>
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
</section>

</body>
</html>