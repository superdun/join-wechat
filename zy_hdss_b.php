<?php 
require 'init.php';
$id = $_GET['id'];
$row = $db->getByWhere("info", "id = $id");

require_once "pay/WxPayPubHelper/WxPayPubHelper.php";
$jsApi = new JsApi_pub();

if(!isset($_SESSION['userInfo']['openid'])){
    if (!isset($_GET['code'])) {
        //触发微信返回code码
        $url = $jsApi->createOauthUrlForCode(urlencode(curPageURL()));
        Header("Location: $url");
    } else {
        //获取code码，以获取openid
        $code = $_GET['code'];
        $jsApi->setCode($code);
        $userInfo = $jsApi->getUserInfo();
        $_SESSION['userInfo'] = $userInfo;
    }
}
?>
<html>
<head>
    <?php require 'head.php';?>
</head>
<body>
<section class="hdss-b hdss-c">
    <div class="art-box"><?php echo $row['title']?></div>
    <div class="article" style="border-bottom: 1px solid #bbb;">
        <?=replaceUploadBack($row['content']);?>
    </div>
    <?
    if($row['price'] > 0){
    ?>
        <div class="hdfy">活动费用：¥<?=$row['price']?></div>
    <?
    }
    ?>

    <div class="teah-pl" style="border-bottom: none; border-top:1px #ccc solid; padding-top: 20px;">
        <?
        if($row['review']){
        ?>
            <form style="border-bottom: none;">
                <input type="hidden" name="data[infoId]" value="<?=$row['id']?>">
                <input type="text" name="data[name]" value="<?=$user['name']?>" required placeholder="请填写您的姓名">
                <input type="text" name="data[phone]" required placeholder="请填写您的联系方式">
                <div class="textarea-box">
                    <textarea name="data[content]" placeholder="请填写备注"></textarea>
                </div>
                <div class="text-bottom">
                    <button type="submit">提交</button>
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
                            'action' : 'active',
                            'redirectURL' : "<?=$redirectURL?>",
                            'data[infoId]' : _this.find("[name='data[infoId]']").val(),
                            'data[name]' : _this.find("[name='data[name]']").val(),
                            'data[phone]' : _this.find("[name='data[phone]']").val(),
                            'data[content]' : _this.find("[name='data[content]']").val()
                        },
                        type:'post',
                        cache:false,
                        dataType:'json',
                        success:function(result) {
                            <?
                            if(empty($row['price'])){
                            ?>
                                alert(result.msg);
                            <?
                            }
                            ?>

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
        }
        ?>
    </div>
</section>
</body>
</html>