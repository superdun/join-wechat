<?
require_once("init.php");

$id	= trim($_GET["id"]);

if ( empty($id) || (int)$db->getCount('info_class', "id=$id") < 1 ) {
    header("location: error.php"); exit;
}

if ( $id == 103102 || $id == 103103 ) {
    require_once("member/isLogin.php");
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

//三级栏目处理
if($third_id){
    $third = $db->getByWhere("info_class", "id=$third_id");
} else {
    $third  = $db->getByWhere("info_class", "id like '".$second['id']."___'", 'order by sortnum asc');
}
if($third){
    if(!empty($third['url'])){
        header("location: ".$third['url']);
    }

    $category_id            = $third['id'];
    $category_name          = $third['name'];
    $category_state         = $third['info_state'];
    $category_seoTitle      = $third['seoTitle'];
    $category_keywords      = $third['keywords'];
    $category_description   = $third['description'];
}

//页面SEO标题、描述、关键字
$site['title']          = empty($category_seoTitle) ? $site['title'] . "-" . $category_name : $category_seoTitle;
$site['keywords']       = empty($category_keywords) ? $site['keywords'] : $category_keywords;
$site['description']    = empty($category_description) ? $site['description'] : $category_description;

//分页
$page           = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
$recordCount    = (int)$db->getCount('info', "class_id=$category_id and state>0");
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

    <?
    if ($category_state == 'content') {
        if ($data = $db->getByWhere('info', "class_id=$category_id and state>0", 'order by state desc, sortnum desc')) {
            $db->updateBySql('info', 'views=views+1', 'id='.$data['id']);
    ?>
            <div class="article">
                <div class="bd clearfix"><?=replaceUploadBack($data['content']);?></div>
            </div>
    <?
        }
    } elseif ($category_state == 'list') {
        $hotId = 0;
        if ( HOTNEW ) {
            if ($data = $db->getByWhere('info', "class_id=$category_id and state>0 and pic<>''", 'order by state desc, sortnum desc')) {
                $hotId = $data['id'];
    ?>
                <div class="hotNews clearfix">
                    <div class="pic">
                        <a href="<?=getDisplay($data['id'], $data['website'])?>"><img src="<?=PATH.UPLOAD_PATH.$data['pic']?>" width="240" height="180" /></a>
                    </div>
                    <dl class="txt">
                        <dt><a href="<?=getDisplay($data['id'], $data['website'])?>"><?=leftStr($data['title'], 20)?></a> </dt>
                        <dd class="date"><?=formatDate('Y-m-d', $data['createdTime'])?></dd>
                        <dd class="intro">  <?=leftStrRemoveHtml($data["content"], 150)?>...</dd>
                        <dd class="more"><a href="<?=getDisplay($data['id'], $data['website'])?>">查看详细 >></a></dd>
                    </dl>
                </div>
        <?
            }
        }
    ?>
        <div class="list">
            <?
            $pageSize   = 10;
            $pageCount	= ceil($recordCount / $pageSize);
            if ($page > $pageCount) $page = $pageCount;
            $list = $db->getList("info", "class_id='$category_id' and state>0 and id not in ( $hotId )", "order by state desc, sortnum desc", "limit ". ($page - 1) * $pageSize . ", " . $pageSize);
            foreach($list as $val) {
            ?>
                <div class="list-item">
                    <h2><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank" title="<?=$val['title']?>"><?=$val['title']?></a></h2>
                    <p class="info"><i><img src="images/ico_1.jpg"> </i><em><?=formatDate('Y-m-d', $val['createdTime'])?></em><i><img src="images/ico_2.jpg"> </i><em><?=$val['views']?></em></p>
                    <div class="txt"><?=leftStrRemoveHtml($val['content'], 150)?></div>
                    <div class="detail"><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank">查看详情&gt;</a></div>
                </div>
            <?
            }
            ?>
        </div>
        <?if( $pageCount > 1){?><div class="page"><?=page3($page, $pageCount)?></div><?}?>
    <?
    } elseif ($category_state == 'pic'){
        $pageSize   = 9;
        $pic_line   = 3;
        $pageCount	= ceil($recordCount / $pageSize);
        if ($page > $pageCount) $page = $pageCount;
        $list = $db->getList("info", "class_id like '$category_id%' and state>0", "order by state desc, sortnum desc", "limit ". ($page - 1) * $pageSize . ", " . $pageSize);
    ?>
      <section class="zy-xmx">
          <div class="zy-bg"><img src="images/z-bg.jpg"> </div>
          <div class="zy-item">
              <div class="title2"><?=$category_name?></div>
              <ul class="listinfo_02">
                  <?
                  foreach($list as $key=>$val) {
                      $pic = empty($val['pic']) ? "holder.js/200x200" :  PATH.UPLOAD_PATH.$val['pic'];
                  ?>
                      <li>
                          <div class="list-pic"><a href="<?=getDisplay($val['id'], null, "xmx")?>"><img src="<?=$pic?>"/></a></div>
                          <dl>
                              <dt><a href="<?=getDisplay($val['id'], null, "xmx")?>"><?=$val['title']?></a><Br><?=$val['tags']?></dt>
                          </dl>
                      </li>
                  <?
                      if (($key+1) % $pic_line == 0) echo "<div class='clear'></div>";
                  }
                  ?>
                </ul>
            </div>
            <?if( $pageCount > 1){?><div class="page"><?=page3($page, $pageCount)?></div><?}?>
        </section>
    <?
    } elseif ($category_state == 'pictxt') {  //图文列表
    ?>
        <div class="item-list">
            <?
            $pageSize   = 10;
            $pageCount	= ceil($recordCount / $pageSize);
            if ($page > $pageCount) $page = $pageCount;
            $list = $db->getList("info", "class_id='$category_id' and state>0", "order by state desc, sortnum desc", "limit ". ($page - 1) * $pageSize . ", " . $pageSize);
            foreach($list as $val) {
                $pic = empty($val['pic']) ? "holder.js/230x230" :  PATH.UPLOAD_PATH.$val['pic'];
            ?>

                <div class="item clearfix">
                    <div class="pic"><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank"><img src="<?=$pic?>" title="<?=$val['title']?>"></a></div>
                    <dl class="txt">
                        <dt><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank" title="<?=$val['title']?>"><?=$val['title']?></a></dt>
                        <dd class="intro"><?=leftStrRemoveHtml($val['content'], 180)?></dd>
                        <dd class="date"><?=date("Y-m-d", $val['createdTime'])?></span></dd>
                        <dd><a class="btn" href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank">查看详情</a></dd>
                    </dl>
                </div>
            <?
            }
            ?>
            <?if( $pageCount > 1){?><div class="page"><?=page3($page, $pageCount)?></div><?}?>
        </div>
        <?
    } elseif ($category_state == 'custom') {
    ?>
        <section class="wjl">
            <div class="tit"><?=$third['name']?></div>
            <div class="wjl-item-wrap">
                <?
                $pageSize   = 9999;
                $pageCount	= ceil($recordCount / $pageSize);
                if ($page > $pageCount) $page = $pageCount;
                $list = $db->getList("info", "class_id='$category_id' and state>0", "order by state desc, sortnum desc", "limit ". ($page - 1) * $pageSize . ", " . $pageSize);
                foreach($list as $val) {
                    $pic = empty($val['pic']) ? "holder.js/230x230" :  PATH.UPLOAD_PATH.$val['pic'];
                ?>
                    <div class="wjl-item clearfix">
                        <div class="time fl"><?=date("m", $val['createdTime'])?>月<em><?=date("d", $val['createdTime'])?></em></div>
                        <div class="info fr">
                            <ul class="list clearfix">
                                <?
                                foreach($db->getList('info_list', "info_id=".$val['id']." and state>0 and pic<>''", "order by sortnum desc") as $val2){
                                ?>
                                    <li><img src="<?=PATH.UPLOAD_PATH.$val2['pic']?>"></li>
                                <?
                                }
                                ?>
                            </ul>
                            <div class="txt"><?=$val['intro']?></div>
                            <div class="detail"><a href="<?=getDisplay($val['id'], $val['website'], 'wjl')?>">查看详情 ></a> </div>
                        </div>
                    </div>
                <?
                }
                ?>
            </div>
        </section>
    <?
    }
    ?>

</body>
</html>
