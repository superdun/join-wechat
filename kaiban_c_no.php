<?
require_once("init.php");

if( !isset($_SESSION['kaiban']) || empty($_SESSION['kaiban']) ){
    header("location: ".PATH."kaiban_b.php"); exit;
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

<section class="kaib-c">
    <div class="hd">
        <h2>我已搞定场地？</h2>
    </div>
    <div class="bd">
        <div class="changdi">
            <div class="tit">
                <a href="<?=PATH?>kaiban_c_yes.php" class="cd-ys">是</a>
                <a href="javascript:;" class="cd-no current">否</a>
            </div>
            <div class="info">
                <p>没关系，请联系我们的工作人员</p>
                <p>电话：<em>400-1234-567</em></p>
            </div>
        </div>
    </div>
</section>

</body>
</html>