<?
require_once("../init.php");

if ( $userId ){
    header("location: index.php"); exit();
}
?>

<!doctype html>
<html>
<head>
    <? require_once("../head.php"); ?>
</head>
<body>

<section class="reg">
    <div class="hd">
        <h2>注册会员</h2>
    </div>
    <div class="bd">
        <form>
            <ul class="reg-ul">
                <li class="clearfix">
                    <label class="label">
                        <span><img src="<?=PATH?>images/reg-ico1.png" height="30"></span>
                    </label>
                    <div class="input-box"><input name="data[phone]" placeholder="请输入您的手机号码"></div>
                </li>
                <li class="clearfix">
                    <label class="label">
                        <span><img src="<?=PATH?>images/reg-ico2.png" height="30"></span>
                    </label>
                    <div class="input-box"><input name="data[name]" placeholder="请输入您的用户名"></div>
                </li>
                <li class="clearfix hide">
                    <label class="label">
                        <span><img src="<?=PATH?>images/reg-ico3.png" height="30"></span>
                    </label>
                    <div class="input-box"><input class="txt1" placeholder="请输入验证码"><em class="yzm">|<a href="">获取验证码</a> </em></div>
                </li>
                <li class="clearfix">
                    <label class="label">
                        <span><img src="<?=PATH?>images/reg-ico4.png" height="30"></span>
                    </label>
                    <div class="input-box"><input name="data[password]" type="password" placeholder="请输入您的密码"></div>
                </li>
                <li class="clearfix">
                    <label class="label">
                        <span><img src="<?=PATH?>images/reg-ico4.png" height="30"></span>
                    </label>
                    <div class="input-box"><input name="data[password2]" type="password" placeholder="确认您的密码"></div>
                </li>
            </ul>
            <div class="btn">
                <button type="submit">提交</button>
                <p class="link" style="padding: 20px; font-size:1.5rem; text-align: right;">已有账号？点击<a href="<?=PATH?>member/login.php">登录</a>！</p>
            </div>
        </form>
        <script>
            $('script:last').prev().on('submit',function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'register',
                        'redirectURL' : "<?=PATH?>member/register_success.php",
                        'data[phone]' : _this.find("[name='data[phone]']").val(),
                        'data[name]' : _this.find("[name='data[name]']").val(),
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

</body>
</html>
