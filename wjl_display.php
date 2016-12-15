<?
require_once("init.php");
require_once("member/isLogin.php");
require_once ("member/isCheckin.php");

//if ($member['checkin']==0){
//    info('我们的管理员会于12小时内审核您的账户,之后您就可以正常查看微记录了');
//}
//elseif($member['checkin']==2){
//    info('您没有通过审核，本模块只对家长开放，如有疑问请咨询卓因客服');
//}
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
</head>
<body>

<section class="wjl">
    <div class="wjl-banji"><?=$third['name']?>  <em><?=date("m", $data['createdTime'])?>月<?=date("d", $data['createdTime'])?>日</em></div>
    <div class="info"><?=replaceUploadBack($data['content']);?></div>
    <div class="teah-pl">
        <h2>评论</h2>
        <?
        if($userId){
        ?>
            <form>
                <input type="hidden" name="data[infoId]" value="<?=$data['id']?>">
                <input type="hidden" name="data[userId]" value="<?=$user['id']?>">

                <input type="text" name="data[name]" value="<?=$user['name']?>" required placeholder="请填写您的姓名">
                <div class="textarea-box">
                    <textarea name="data[content]" required placeholder="请填写备注"></textarea>
                </div>
                <div class="text-bottom">
                    <button type="submit">提交评论</button>
                </div>
            </form>
            <script>
                $('script:last').prev().submit(function(){
                    var _this = $(this);
                    layer.load(1, {
                        shade: [0.5,'#000']
                    });

                    $.ajax({
                        url:'<?=PATH?>controller/ajaxForm.php',
                        data:{
                            'action' : 'comment',
                            'redirectURL' : "<?=$redirectURL?>",
                            'data[infoId]' : _this.find("[name='data[infoId]']").val(),
                            'data[userId]' : _this.find("[name='data[userId]']").val(),
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
                                layer.closeAll();
                            }
                        },
                        error : function() {
                            alert("操作异常！");
                            layer.closeAll();
                        }
                    });
                    return false;
                });
            </script>
        <?
        } else {
        ?>
            <form>
                <input type="text" name="data[name]" value="<?=$user['name']?>" disabled>
                <div class="textarea-box">
                    <p><a href="<?=PATH?>member/login.php?redirectURL=<?=$redirectURL?>" class="login">点击登录</a></p>
                </div>
                <div class="text-bottom">
                    温馨提示：需要登录后才可以进行评论哦！
                </div>
            </form>
        <?
        }
        ?>
        <?
        $list = $db->getList("comment", "infoId=$id and status>0");
        foreach($list as $val){
        ?>
            <dl>
                <dt class="name"><?=$db->getField('member', "name", "id=".$val['userId'])?> 评论：</dt>
                <dd class="txt"><?=$val["content"]?></dd>
            </dl>
        <?
        }
        ?>
    </div>
</section>
</body>
</html>
