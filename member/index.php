<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "index";
?>

<!doctype html>
<html>
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
                <!--<li class="clearfix">
                    <label>姓名</label>
                    <input type="text" class="input w1" name="data[username]" value="<?/*=$user["username"]*/?>">
                </li>-->
                <li class="clearfix">
                    <label>姓名</label>
                    <?=$user["name"]?>
                </li>
                <!--<li class="clearfix">
                    <label>会员级别</label>
                    <?/*=$db->getField('member_grade', 'name', "id=".$user['grade']);*/?>
                </li>-->
                <li class="clearfix">
                    <label>注册时间</label>
                    <?=date('Y-m-d H:s:i', $user['createdTime'])?>
                </li>
                <li class="clearfix">
                    <label>最后登录时间</label>
                    <?=date('Y-m-d H:s:i', $user['createdTime'])?>
                </li>
                <!--<li class="clearfix">
                    <label>修改密码</label>
                    <a class="change" href="<?/*=PATH*/?>member/change.php">修改</a>
                </li>-->
                <!--<li class="clearfix">
                    <button class="button" type="submit">保存姓名</button>
                </li>-->
                <li class="clearfix">
                    <a class="button" href="<?=PATH?>member/logout.php">退出登录</a>
                </li>
            </ul>
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
                        'action' : 'changePersonal',
                        'redirectURL' : "<?=$redirectURL?>",
                        'data[username]' : _this.find("[name='data[username]']").val()
                    },
                    type:'post',
                    cache:false,
                    dataType:'json',
                    success:function(result) {
                        alert(result.msg);
                        if(result.state){
                            if(result.url){
                                window.location = decodeURIComponent(result.url);
                            } else {
                                _this.find('input').val('');
                                layer.closeAll();
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
    </div>
</section>

<? require_once("../footer.php"); ?>

</body>
</html>
