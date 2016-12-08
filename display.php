<?
require_once("init.php");

$id	= (int)$_GET["id"];
if ($id < 1) {
    header("location: error.php"); exit;
}
if ($data = $db->getByWhere('info', "id=$id and state>0")) {
    $db->updateBySql('info', 'views=views+1', 'id='.$data['id']);
} else {
    header("location: error.php"); exit;
}

//一级栏目处理
$base_id    = substr($data['class_id'], 0, 3);
$second_id  = strlen($data['class_id']) >= 6 ? substr($data['class_id'], 0, 6) : 0;
$third_id   = strlen($data['class_id']) >= 9 ? substr($data['class_id'], 0, 9) : 0;

if ( $second_id == 103102 || $second_id == 103103 ) {
    require_once("member/isLogin.php");
}

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
$site['title']          = empty($data['seoTitle']) ? $site['title'] . "-" . $data['title'] : $data['seoTitle'];
$site['keywords']       = empty($data['keywords']) ? $site['keywords'] : $data['keywords'];
$site['description']    = empty($data['description']) ? $site['description'] : $data['description'];

//获取上下文信息
$related = $db->getList("info", "class_id=$category_id and state>0", "order by state desc, sortnum desc");
foreach($related as $key=>$val){
    if ($related[$key]['id'] == $id){
        if ($key < count($related)) {
            $next_id	= $related[$key + 1]['id'];
            $next_title	= $related[$key + 1]['title'];
        } else {
            $next_id	= 0;
        }
        if ($key > 0) {
            $prev_id    = $related[$key - 1]['id'];
            $prev_title	= $related[$key - 1]['title'];
        } else {
            $prev_id    = 0;
        }
    }
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
    <link rel="stylesheet" type="text/css" href="<?=PATH?>css/inside.css">
</head>
<body class="inside">

<? require_once("header.php"); ?>

<div class="wrap">
    <div class="fArea clearfix">
        <div class="main fl">

            <? require_once("location.php"); ?>

            <div class="art-box hide">
                <h2><?=$data['title']?></h2>
                <div class="info"><i><img src="images/ico_1.jpg"> </i><em><?=formatDate('Y-m-d', $data['createdTime'])?></em> <i><img src="images/ico_2.jpg"> </i><em><?=$data['views']+1?></em></div>
            </div>
            <div class="article clearfix"><?=replaceUploadBack($data['content']);?></div>

            <ul class="up-down clearfix"><?
                if ($next_id > 0) {
                ?>
                    <li>
                        <em>下一条</em><a href="<?=PATH?>display.php?id=<?=$next_id?>" rel="next"><?=$next_title?></a>
                        <p class="aNext"></p>
                    </li>
                <?
                }
                if ($prev_id > 0) {
                ?>
                    <li>
                        <em>上一条</em><a href="<?=PATH?>display.php?id=<?=$prev_id?>" rel="prev"><?=LeftStr($prev_title, 20)?></a>
                        <p class="aPrev"></p>
                    </li>
                <?
                }
                ?>
            </ul>
            <div class="ipublic">
                <div class="hd">
                    <h2>相关新闻</h2>
                </div>
                <div class="bd">
                    <?
                    foreach($db->getList('info', "class_id=$category_id and state>0 and pic<>'' and id<>".$data['id'], "order by state desc, sortnum desc", "limit 3") as $val){
                    ?>
                        <div class="inews-item clearfix">
                            <div class="pic fl"><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank"><img src="images/p200x120.jpg"></a></div>
                            <div class="info fr">
                                <h2><a href="<?=getDisplay($val['id'], $val['website'])?>" target="_blank"><?=$val['title']?></a> </h2>
                                <div class="txt"><?=leftStrRemoveHtml($val["content"], 80)?></div>
                                <div class="time"><?=formatDate('Y-m-d', $val['createdTime'])?></div>
                            </div>
                        </div>
                    <?
                    }
                    ?>
                </div>
            </div>
            <div class="ipublic">
                <div class="hd">
                    <h2>网友评论</h2>
                </div>
                <div class="bd">
                    <?
                    if($userId){
                    ?>
                        <form>
                            <input type="hidden" name="data[infoId]" value="<?=$data['id']?>">
                            <div class="textarea-box">
                                <textarea name="data[content]" required></textarea>
                            </div>
                            <div class="text-bottom">
                                <button type="submit">提交评论</button>
                            </div>
                        </form>
                        <script>
                            $('script:last').prev().submit(function(){
                                var _this = $(this);
                                _shade = layer.load(1, {
                                    shade: [0.5,'#000']
                                });

                                $.ajax({
                                    url:'<?=PATH?>controller/ajaxForm.php',
                                    data:{
                                        'action' : 'comment',
                                        'redirectURL' : "<?=$redirectURL?>",
                                        'data[infoId]' : _this.find("[name='data[infoId]']").val(),
                                        'data[content]' : _this.find("[name='data[content]']").val()
                                    },
                                    type:'post',
                                    cache:false,
                                    dataType:'json',
                                    success:function(result) {
                                        alert(result.msg);
                                        if(result.state){
                                            _this.find('input, textarea').val('');
                                            layer.closeAll();
                                            if(result.url){
                                                window.location = decodeURIComponent(result.url);
                                            }
                                        } else {
                                            layer.close(_shade);
                                        }
                                    },
                                    error : function() {
                                        alert("操作异常！");
                                        _this.find('input, textarea').val('');
                                        layer.closeAll();
                                    }
                                });
                                return false;
                            });
                        </script>
                    <?
                    } else {
                    ?>
                        <div class="textarea-box">
                            <p style="line-height: 198px; font-size: 18px; text-align: center;"><a style="color:red; cursor: pointer;" class="login">点击登录</a></p>
                        </div>
                        <div class="text-bottom">
                            <button type="submit" class="disabled" disabled>提交评论</button>温馨提示：需要登录后才可以进行评论哦！
                        </div>
                    <?
                    }
                    ?>
                </div>
            </div>
        </div>

        <? require_once("aside.php"); ?>

    </div>
</div>

<? require_once("footer.php"); ?>

</body>
</html>
