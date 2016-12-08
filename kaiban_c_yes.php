<?
require_once("init.php");

if( !isset($_SESSION['kaiban']) || empty($_SESSION['kaiban']) ){
    header("location: ".PATH."kaiban_b.php"); exit;
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>

<section class="kaib-c">
    <div class="hd">
        <h2>我已搞定场地？</h2>
    </div>
    <div class="bd">
        <form>
            <div class="changdi">
                <div class="tit">
                    <a href="javascript:;" class="cd-ys current">是</a>
                    <a href="<?=PATH?>kaiban_c_no.php" class="cd-no">否</a>
                </div>
                <div class="info">
                    <div class="add-tit">上课地点：
                        <select name="data[area]" required>
                            <option value="0">请选择地区</option>
                            <option value="黄浦区">黄浦区</option>
                            <option value="徐汇区">徐汇区</option>
                            <option value="长宁区">长宁区</option>
                            <option value="静安区">静安区</option>
                            <option value="普陀区">普陀区</option>
                            <option value="闸北区">闸北区</option>
                            <option value="虹口区">虹口区</option>
                            <option value="杨浦区">杨浦区</option>
                            <option value="闵行区">闵行区</option>
                            <option value="宝山区">宝山区</option>
                            <option value="嘉定区">嘉定区</option>
                            <option value="浦东新区">浦东新区</option>
                            <option value="金山区">金山区</option>
                            <option value="松江区">松江区</option>
                            <option value="青浦区">青浦区</option>
                            <option value="奉贤区">奉贤区</option>
                            <option value="崇明县">崇明县</option>
                        </select>
                    </div>
                    <div class="textarea"><textarea name="data[address]" placeholder="请输入上课地点" required></textarea></div>
                    <div class="add-tit">请选择开班年龄段：</div>
                    <input type="hidden" name="data[ageBracket]" value="3-5岁">
                    <ul class="listinfo_age">
                        <li><a class="current">3-5岁</a> </li>
                        <li><a>6-8岁</a> </li>
                        <li><a>9-11岁</a> </li>
                        <li><a>12-14岁</a> </li>
                        <li><a>15岁+</a> </li>
                    </ul>
                </div>
            </div>
            <div class="btn"><button type="submit"><img src="images/kaib-b-btn.jpg"> </button></div>
        </form>
        <script>
            $(".listinfo_age li a").on("click", function(){
                $(this).parent().siblings().find('a').removeClass("current");
                $(this).addClass("current");
                $("[name='data[ageBracket]']").val($(this).html());
            });

            $('script:last').prev().submit(function(){
                var _this = $(this);
                layer.load(1, {
                    shade: [0.5,'#000']
                });
                $.ajax({
                    url:'<?=PATH?>controller/ajaxForm.php',
                    data:{
                        'action' : 'kaibanAddress',
                        'redirectURL' : "<?=PATH?>kaiban_d.php",
                        'data[area]' : _this.find("[name='data[area]'] option:selected").val(),
                        'data[address]' : _this.find("[name='data[address]']").val(),
                        'data[ageBracket]' : _this.find("[name='data[ageBracket]']").val()
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