<?
require_once("../init.php");
require_once("isLogin.php");
?>

<!doctype html>
<html>
<head>
    <? require_once("../head.php"); ?>
    <link rel="stylesheet" type="text/css" href="<?=PATH?>css/inside.css">
</head>
<body>

<? require_once("../header.php"); ?>

<section class="wrap">
    <div class="sArea clearfix">
        <form class="forgot-form" style="padding: 50px 100px;">
            <ul class="reg-ul">
                <li class="clearfix"><label class="label">邮箱<em>*</em></label><div class="input-box"><input name="data[email]" required></div></li>
                <div class="zhanh clearfix" style=" padding-left: 65px;">
                    <span style="float: none;"><button type="submit">提交</button></span>
                </div>
                <li class="clearfix"style=" padding-left: 65px;"><em>*</em>点击提交后，平台会发送重置密码到您的邮箱，请注意查收邮件。</li>
            </ul>
        </form>
        <script>
            $('.forgot-form').on('submit',function(){
                var _this = $(this);
                _shade = layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'forgot',
                        'redirectURL' : "<?=$redirectURL?>",
                        'data[email]' : _this.find("[name='data[email]']").val()
                    },
                    type:'post',
                    cache:false,
                    dataType:'json',
                    success:function(result) {
                        alert(result.msg);
                        if(result.state){
                            _this.find('input').val('');
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
                        layer.closeAll();
                    }
                });
                return false;
            });
        </script>
    </div>
</section>

<? require_once("../footer.php"); ?>

</body>
</html>
