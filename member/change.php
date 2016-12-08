<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "change";
?>

<!doctype html>
<html style="font-size: 62.5%;">
<head>
    <? require_once("../head.php"); ?>
</head>
<body class="member">

<? require_once("../header.php"); ?>

<section>

    <? require_once("aside.php"); ?>

    <div class="vip-con member">
        <form class="form1">
            <ul>
                <li class="clearfix">
                    <label><em>*</em>原密码</label>
                    <input class="input" name="data[oldPassword]" type="password" required>
                </li>
                <li class="clearfix">
                    <label><em>*</em>新密码</label>
                    <input class="input" name="data[password]" type="password" required>
                </li>
                <li class="clearfix">
                    <label><em>*</em>确认密码</label>
                    <input class="input" name="data[password2]" type="password" required>
                </li>
                <li class="clearfix">
                    <label>&nbsp;</label>
                    <button type="submit">修改</button>
                </li>
            </ul>
        </form>
        <script>
            $('.change-form').on('submit',function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'change',
                        'redirectURL' : "<?=PATH?>member/login.php",
                        'data[oldPassword]' : _this.find("[name='data[oldPassword]']").val(),
                        'data[password]' : _this.find("[name='data[password]']").val(),
                        'data[password2]' : _this.find("[name='data[password2]']").val()
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
                            layer.closeAll();
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
</section>

<? require_once("../footer.php"); ?>

</body>
</html>