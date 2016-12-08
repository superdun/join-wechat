<?
require_once "init.php";
?>

<!doctype html>
<html>

<? require_once "head.php"; ?>
<link rel="stylesheet" type="text/css" href="<?=PATH?>css/error.css" />

<body>
<div class="bg">
    <div class="cont">
        <div class="c1"><img src="<?=PATH?>images/404/01.png" class="img1" /></div>
        <h2>哎呀…您访问的页面不存在</h2>
        <div class="c2"><a href="javascript:history.go(-1);location.reload();" class="re">返回上一页</a><a href="<?=$siteUrl?>" class="home">网站首页</a></div>
        <div class="c3"><a href="http://www.paikehanbao.com" class="c3"><?=$config_title?></a> 提醒您 - 您可能输入了错误的网址，或者该网页已删除或移动</div>
    </div>
</div>
</body>
</html>