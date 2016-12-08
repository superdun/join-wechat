<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "address";
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
                    <label><em>*</em>标签:</label>
                    <input type="text" class="input w1" name="data[tag]" required>
                </li>
                <li class="clearfix">
                    <label><em>*</em>姓名:</label>
                    <input type="text" class="input w1" name="data[name]" required>
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
                        'action' : 'createAddress',
                        'redirectURL' : "<?=$redirectURL?>",
                        'data[tag]' : _this.find("[name='data[tag]']").val(),
                        'data[name]' : _this.find("[name='data[name]']").val(),
                        'data[phone]' : _this.find("[name='data[phone]']").val(),
                        'data[address]' : _this.find("[name='data[address]']").val(),
                        'data[default]' : _this.find("[name='data[default]']:checked").val()
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
        <div class="address-list">
            <?
            $list = $db->getList("member_address", "userId=$userId", "order by sortnum desc");
            foreach($list as $key=>$val){
            ?>
                <ul class="item">
                    <li class="clearfix">
                        <span class="tag">
                            标签：<?=$val['tag']?>
                        </span>
                        <span class="edit">
                            操作：
                            <?
                            if(empty($val['default'])){
                                ?>
                                <a data-id="<?=$val['id']?>" class="setDefaultAddress">设为默认</a>
                                <?
                            } else {
                                ?>
                                默认地址
                                <?
                            }
                            ?>
                            <a data-id="<?=$val['id']?>" class="deleteAddress">删除</a>
                        </span>
                    </li>
                    <li class="clearfix">姓名：<?=$val['name']?> (<?=$val['phone']?>)</li>
                    <li class="clearfix">详细地址：<?=$val['address']?></li>
                </ul>
            <?
            }
            ?>
        </div>
        <script>
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

            $('.deleteAddress').on('click',function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });

                $.ajax({
                    url:'<?=PATH?>controller/member.php',
                    data:{
                        'action' : 'deleteAddress',
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