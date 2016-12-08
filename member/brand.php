<?
require_once("../init.php");
$base['menu'] = "index";

if ( !$_SESSION['userId'] ){
    header("location: login.php"); exit();
}
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

    <div class="vip-con">
        <form>
            <ul class="vip-list_02">
                <li class="clearfix"><div class="tit">姓名</div><div class="info"><input type="text" name="data[username]" value="<?=$user["username"]?>"></div></li>
                <li class="clearfix"><div class="tit">手机号</div><div class="info"><?=$user["name"]?></div></li>
                <li class="clearfix"><div class="tit">会员级别</div><div class="info"><?=$db->getField('member_grade', 'name', "id=".$user['grade']);?></div></li>
                <li class="clearfix"><div class="tit">注册时间</div><div class="info"><?=date('Y-m-d H:s:i', $user['createdTime'])?></div></li>
                <li class="clearfix"><div class="tit">最后登录时间</div><div class="info"><?=date('Y-m-d H:s:i', $user['createdTime'])?></div></li>
                <li class="clearfix"><div class="tit">修改密码</div><div class="info"><a href="<?=PATH?>member/change.php">修改</a></div></li>
                <li class="clearfix"><div class="tit">退出登录</div><div class="info"><a href="<?=PATH?>member/logout.php">确定</a></div></li>
                <li class="clearfix"><div class="tit">&nbsp;</div><div class="info"><button style="padding: 6px 15px;" type="submit">保存</button></div></li>
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
                        'data[brand]' : _this.find("[name='data[brand]']").val(),
                        'data[type]' : _this.find("[name='data[type]']").val()
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
