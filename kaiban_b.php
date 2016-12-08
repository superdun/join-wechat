<?
require_once("init.php");
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

<section class="kaib-a">
    <div class="hd">
        <h2>请填写如下信息</h2>
    </div>
    <div class="bd">
        <form>
            <ul class="kaib-ul-01">
                <li class="clearfix">
                    <label class="label">孩子姓名：</label>
                    <div class="input-box"><input name="data[name]" placeholder="请输入孩子的姓名" required></div>
                </li>
                <li class="clearfix">
                    <label class="label">年龄：</label>
                    <div class="input-box"><input name="data[age]" placeholder="请输入孩子的年龄" required></div>
                </li>
                <li class="clearfix">
                    <label class="label">性别：</label>
                    <div class="checkbox clearfix">
                        <div class="chk-nan">
                            <input id="radio_1" name="data[sex]" value="1" class="radio_1" type="radio" checked>
                            <label for="radio_1"></label>
                            男孩
                        </div>
                        <div class="chk-nv">
                            <input id="radio_2" name="data[sex]" value="2" class="radio_1" type="radio">
                            <label for="radio_2"></label> 女孩
                        </div>
                    </div>
                </li>
                <li class="clearfix">
                    <label class="label">手机号码：</label>
                    <div class="input-box"><input name="data[phone]" placeholder="请输入正确的手机号码" required></div>
                </li>
                <!--<li class="clearfix">
                    <label class="label">输入验证码：</label>
                    <div class="input-box"><input class="txt"> <div class="yzm">| 获取验证码</div></div>
                </li>-->
            </ul>
            <div class="btn"><button type="submit"><img src="images/kaib-b-btn.jpg"> </button></div>
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
                        'action' : 'kaiban',
                        'redirectURL' : "<?=PATH?>kaiban_c_yes.php",
                        'data[name]' : _this.find("[name='data[name]']").val(),
                        'data[age]' : _this.find("[name='data[age]']").val(),
                        'data[sex]' : _this.find("[name='data[sex]']:checked").val(),
                        'data[phone]' : _this.find("[name='data[phone]']").val()
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