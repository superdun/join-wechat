<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "car";
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
                    <label><em>*</em>品牌:</label>
                    <select type="text" class="input" name="data[brand]" required></select>
                </li>
                <li class="clearfix">
                    <label><em>*</em>联系电话:</label>
                    <input type="text" class="input" name="data[phone]" value="<?=$member['name']?>" required>
                </li>
                <li class="clearfix">
                    <label><em>*</em>详细地址:</label>
                    <textarea class="textarea" name="data[address]" required></textarea>
                </li>
                <li class="clearfix">
                    <label>是否默认:</label>
                    <input type="checkbox" class="checkbox" name="data[default]" value="1">
                </li>
                <li class="clearfix">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn">提交</button>
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
                        'action' : 'createCar',
                        'redirectURL' : "<?=$redirectURL?>",
                        'data[brand]' : _this.find("[name='data[brand]']']").val(),
                        'data[series]' : _this.find("[name='data[series]']").val(),
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
    <div class="vip-con member">
        <div class="car-list">
            <?
            $list = $db->getList("member_car", "userId=$userId", "order by sortnum desc");
            foreach($list as $key=>$val){
            ?>
                <ul class="item">
                    <li class="edit"><a data-id="<?=$val['id']?>" class="deleteCar">删除</a></li>
                    <li class="icon"><img src="<?=$db->getField("info_class", "icon", "id=".$val['brand']." and isTop=1")?>" width="80"/></li>
                    <li class="txt">
                        <span>宝马</span>
                        <p>系列：SUV7系</p>
                        <p>型号：1.6T-2012-2014</p>
                    </li>
                </ul>
            <?
            }
            ?>
        </div>
        <script>
            $('.deleteCar').on('click',function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'deleteCar',
                        'redirectURL' : "<?=$redirectURL?>",
                        'id' : _this.attr("data-id")
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

            $('.setDefaultAddress').on('click',function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'setDefaultAddress',
                        'redirectURL' : "<?=$redirectURL?>",
                        'id' : _this.attr("data-id")
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