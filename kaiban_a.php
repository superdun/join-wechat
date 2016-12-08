<?
require_once("init.php");

$id	= trim($_GET["id"]);

if ( empty($id) || (int)$db->getCount('info_class', "id=$id") < 1 ) {
    header("location: error.php"); exit;
}

//一级栏目处理
$category = $db->getByWhere("info_class", "id=$id");
if($category){
    if(!empty($category['url']) && !$second_id){
        header("location: ".$category['url']);
    }

    $category_id            = $category['id'];
    $category_name          = $category['name'];
    $category_state         = $category['info_state'];
    $category_seoTitle      = $category['seoTitle'];
    $category_keywords      = $category['keywords'];
    $category_description   = $category['description'];
} else{
    header("location: ".PATH); exit;
}

//页面SEO标题、描述、关键字
$site['title']          = empty($category_seoTitle) ? $site['title'] . "-" . $category_name : $category_seoTitle;
$site['keywords']       = empty($category_keywords) ? $site['keywords'] : $category_keywords;
$site['description']    = empty($category_description) ? $site['description'] : $category_description;
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

<section class="kaib-a">
    <div class="hd">
        <h2><?=$category_name?></h2>
    </div>
    <div class="bd">
        <?
        $list = $db->getList("info", "class_id=$category_id and state>0", "order by state desc, sortnum desc");
        foreach ($list as $key => $val) {
        ?>
            <div class="tit"><em>Q<?=$key+1?></em><?=$val["title"]?></div>
            <div class="info"><?=leftStrRemoveHtml($val["content"], 0)?></div>
        <?
        }
        ?>
    </div>
    <div class="btn"><a href="<?=PATH?>kaiban_b.php"><img src="images/kaib-a-btn.jpg"> </a> </div>
</section>

</body>
</html>