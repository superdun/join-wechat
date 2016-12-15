<?
require_once("init.php");
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

<section class="kaib-e-bg">
    <div class="kaib-e-tp"><img src="<?=PATH?>images/kaib-e-top.jpg"></div>
    <div class="kaib-e">
        <div class="tit hide">
            以下是您的好友某某某<Br>
            发来的探索邀请
        </div>
        <?
        $data = $db->getByWhere("info", "class_id=102101 and state>0");
        if($data){
        ?>
            <div class="hd">
                <h2><?=$data["title"]?></h2>
            </div>
            <div class="info"><?=$data["content"]?></div>
            <div class="weix">
                <div class="txt">请关注“卓因青少年创客工场”<br>长按下面二维码即可关注我们</div>
                <div class="pic"><img src="<?=PATH . UPLOAD_PATH . $site['wechat'] ?>"/></div>
            </div>
        <?
        }
        ?>
        <div class="hd hide">
            <h2><em>真南路</em>班级课程介绍</h2>
        </div>
        <div class="bd hide">
            <div class="pic"><img src="images/bj-b.jpg"></div>
            <div class="info">
                卓因青少年创客工场是专注于将科技与青少年教育融合的专业机构。课程以科学制作、创意设计、生活实践等多元课程为主要载体，针对3-18岁儿童及青少年开展机器人&STEAM专项教育培训。致力于“体验式学习”理念，以Project-based learning的学习方式，让学员在过程中学习科学、物理、化学、逻辑－数学、计算机图形化编程等学科知识，培养孩子热爱科学、乐于动手实践的精神，发扬创客精神，开拓勇于创新的思维，建立积极的学习习惯并享受学习的乐趣。课程形式包括机器人成长课程、创客课程、机器人竞赛课程以及亲子科技课程。
            </div>
        </div>
        <div class="btn"><a class="yaoqing"><img src="images/sk-2.jpg"> </a> </div>
    </div>
    <div class="kaib-e-bt"><img src="images/kaib-e-bt.jpg"> </div>
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