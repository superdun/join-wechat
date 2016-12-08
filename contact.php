<?
require("init.php");

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
    if(!empty($base['url'])){
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
<body class="inside">

<? require_once("header.php"); ?>

<div class="container products">

    <? require_once("location.php"); ?>

    <div class="wrap">

        <div class="hide">
            <div class="thirdMenu">
                <?
                foreach ($categoryArray[$base_id]['children'] as $key=>$val) {
                    foreach ($val['children'] as $val2) {
                        ?>
                        <a href="<?=getCategoryUrl($val2['id'], $val2['url'])?>" <?if ($val2['id'] == $category_id) echo " class='current'"?>><?=$val2['name']?></a>
                        <?
                    }
                }
                ?>
            </div>
        </div>

        <div class="article">
            <div class="bd clearfix">
                <?=replaceUploadBack($db->getField('info', 'content', "class_id=$category_id and state>0", 'order by state desc, sortnum desc'))?>
            </div>
        </div>
        <div class="contact">
            <form>
                <h3>CONTACT GRAVIM</h3>
                <h4>Please use the contact form below to send us a message. If you'd like to request a sample, please<a href="<?=PATH.'request.php'?>" class="more">CLICK HERE</a></h4>
                <ul>
                    <li>
                        <label for="firstName">First Name<em>*</em></label>
                        <input type="text" id="firstName" name="data[firstName]" required>
                    </li>
                    <li>
                        <label for="lastName">Last Name<em>*</em></label>
                        <input type="text" id="lastName" name="data[lastName]" required>
                    </li>
                    <li>
                        <label for="email">Email<em>*</em></label>
                        <input type="text" id="email" name="data[email]" required>
                    </li>
                    <li>
                        <label for="phone">Phone<em>*</em></label>
                        <input type="text" id="phone" name="data[phone]" required>
                    </li>
                    <li>
                        <label for="content">Message<em>*</em></label>
                        <p>Tips on getting accurate quotes from suppliers.Please include the following in your inquiry:Order quantity,Special requests if any</p>
                        <textarea id="content" name="data[content]" required></textarea>
                    </li>
                    <li>
                        <button type="submit">SEND</button>
                    </li>
                </ul>
            </form>

            <script>
                $('script:last').prev().submit(function(){
                    var _this = $(this);
                    layer.load(1, {
                        shade: [0.5,'#000']
                    });
                    $.ajax({
                        url:'<?=PATH?>ajaxForm.php',
                        data:{
                            'action' : 'message',
                            'data[name]' : _this.find("[name='data[firstName]']").val()+' '+_this.find("[name='data[lastName]']").val(),
                            'data[email]' : _this.find("[name='data[email]']").val(),
                            'data[phone]' : _this.find("[name='data[phone]']").val(),
                            'data[content]' : _this.find("[name='data[content]']").val()
                        },
                        type:'post',
                        cache:false,
                        dataType:'text',
                        success:function(data) {
                            alert(data);
                            layer.closeAll();
                            _this.find('input, textarea').val('').html('');
                        },
                        error : function() {
                            alert("操作异常！");
                            layer.closeAll();
                        }
                    });

                    return false;
                });
            </script>
        </div>
    </div>
</div>

<? require_once("footer.php");?>

</body>
</html>
