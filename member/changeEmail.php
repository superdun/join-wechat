<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "changeEmail";
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

        <? require_once("aside.php"); ?>

        <div class="pes-info">
            <form class="change-form">
                <ul class="reg-ul">
                    <li class="clearfix"><label class="label">原邮箱</label><div class="input-box"><input name="data[oldEmail]" value="<?=$member['email']?>" readonly style="cursor:not-allowed;"></div></li>
                    <li class="clearfix"><label class="label">新邮箱<em>*</em></label><div class="input-box"><input name="data[email]" required></div></li>
                    <div class="zhanh" style=" padding-left: 65px;">
                        <span style="float: none;"><button type="submit">提交</button></span>
                    </div>
                </ul>
            </form>
            <script>
                $('.change-form').on('submit',function(){
                    var _this = $(this);
                    _shade = layer.load(1, {
                        shade: [0.5,'#000']
                    });

                    $.ajax({
                        url:'<?=PATH?>controller/member.php',
                        data:{
                            'action' : 'changeEmail',
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
                            _this.find('input').val('');
                            layer.closeAll();
                        }
                    });
                    return false;
                });
            </script>
        </div>
    </div>
</section>

<? require_once("../footer.php"); ?>

</body>
</html>
