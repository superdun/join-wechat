<?
require_once("init.php");
require_once("member/isLogin.php");

$id	= trim($_GET["id"]);

if ( empty($id) || (int)$db->getCount('info_class', "id=$id") < 1 ) {
    header("location: error.php"); exit;
}

//一级栏目处理
$base_id    = substr($id, 0, 3);
$second_id  = strlen($id) >= 6 ? substr($id, 0, 6) : 0;
$third_id   = strlen($id) >= 9 ? substr($id, 0, 9) : 0;
$base = $db->getByWhere("info_class", "id=$base_id");
if($base){
    if(!empty($base['url']) && !$second_id){
        header("location: ".$base['url']);
    }

    $category_id            = $base['id'];
    $category_name          = $base['name'];
    $category_state         = $base['info_state'];
    $category_seoTitle      = $base['seoTitle'];
    $category_keywords      = $base['keywords'];
    $category_description   = $base['description'];
}

//二级栏目处理
if($second_id){
    $second = $db->getByWhere("info_class", "id=$second_id");
} else {
    $second = $db->getByWhere("info_class", "id like '".$base['id']."___'", 'order by sortnum asc');
}
if($second){
    if(!empty($second['url']) && !$third_id){
        header("location: ".$second['url']);
    }

    $category_id            = $second['id'];
    $category_name          = $second['name'];
    $category_state         = $second['info_state'];
    $category_seoTitle      = $second['seoTitle'];
    $category_keywords      = $second['keywords'];
    $category_description   = $second['description'];
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
    <section class="wjl2">
        <div class="tit">班级列表</div>
        <div class="pic-list clearfix">
            <?
            $list = $db->getList("info_class", "id like '".$second_id."___%' and pic<>'' order by id asc");
            foreach($list as $key=>$val) {
                $pic = empty($val['pic']) ? "holder.js/200x200" : PATH . UPLOAD_PATH . $val['pic'];
            ?>
                <div class="item">
                    <div class="pic"><a href="<?=getCategoryUrl($val['id']) ?>"><img src="<?= $pic ?>" title="<?= $val['name'] ?>"></div>
                    <dl class="txt">
                        <dt class="title"><a href="<?= getCategoryUrl($val['id']) ?>" title="<?= $val['name'] ?>"><?= $val['name'] ?></a></dt>
                    </dl>
                </div>
            <?
            }
            ?>
        </div>
    </section>
</body>
</html>
