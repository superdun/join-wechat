<?
require_once("init.php");

$category_id    = 102;
$base['id']     = 102;
$base['menu']   = "abc";
$base['name']	= "Search";
$search		    = htmlspecialchars(trim($_GET["keyword"]));

if (empty($search)) {
    $db->close();
    header("location: ". PATH);
    exit;
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body class="inside">

<? require_once("header.php"); ?>

<div class="container products">

    <? require_once("location.php"); ?>

    <?
    //分页
    $page           = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
    $recordCount    = (int)$db->getCount('info', "class_id=$category_id and state>0 and title like '%$search%'");
    $pageSize       = 9;
    $pic_line       = 3;
    $pageCount	    = ceil($recordCount / $pageSize);
    //if ($page > $pageCount) $page = $pageCount;
    $list = $db->getList("info", "class_id like '$category_id%' and state>0 and title like '%$search%'", "order by state desc, sortnum desc", "limit ". ($page - 1) * $pageSize . ", " . $pageSize);
    ?>
        <div class="wrap clearfix">
            <div class="main">
                <div class="pic-list clearfix">
                    <?
                    foreach($list as $key=>$val) {
                        $pic = empty($val['pic']) ? "holder.js/221x221" :  PATH.UPLOAD_PATH.$val['pic'];
                        ?>
                        <div class="item">
                            <div class="pic"><a href="<?=getDisplay($val['id'])?>" target="_blank"><img src="<?=$pic?>"/></a></div>
                            <dl class="txt">
                                <dt><?=$val['title']?></dt>
                                <dd><a class="add"></a></dd>
                            </dl>
                        </div>
                        <?
                        if (($key+1) % $pic_line == 0) echo "<div class='clear'></div>";
                    }
                    ?>
                </div>

                <?if( $pageCount > 1){?><div class="page"><?=page($page, $pageCount)?></div><?}?>

            </div>

            <? require_once("aside.php"); ?>

        </div>
    </div>
</div>

<? require_once("footer.php"); ?>

</body>
</html>
