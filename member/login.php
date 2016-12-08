<?
require_once("../init.php");

if ( $userId ){
    header("location: index.php"); exit();
}

$redirectURL	= trim($_GET["redirectURL"]);
if(empty($redirectURL)){
    $redirectURL = PATH."member/index.php";
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
          <h2>会员登录</h2>
      </div>
      <div class="bd">
          <form>
              <ul class="reg-ul">
                  <li class="clearfix">
                      <label class="label">
                          <span><img src="<?=PATH?>images/reg-ico2.png" height="30"></span>
                      </label>
                      <div class="input-box"><input name="data[name]" placeholder="请输入您的用户名"></div>
                  </li>
                  <li class="clearfix">
                      <label class="label">
                          <span><img src="<?=PATH?>images/reg-ico4.png" height="30"></span>
                      </label>
                      <div class="input-box"><input type="password" name="data[password]" placeholder="请输入您的密码"></div>
                  </li>
              </ul>
              <div class="btn">
                  <button type="submit">提交</button>
                  <p class="link" style="padding: 20px; font-size:1.5rem; text-align: right;">还没有账号？点击<a href="<?=PATH?>member/register.php">立即注册</a>！ </p>
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
                          'action' : 'login',
                          'redirectURL' : "<?=$redirectURL?>",
                          'data[name]' : _this.find("[name='data[name]']").val(),
                          'data[password]' : _this.find("[name='data[password]']").val()
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
