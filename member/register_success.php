<?
require_once("../init.php");
?>

<!doctype html>
<html>
<head>
    <? require_once("../head.php"); ?>
</head>
<body>
  <section class="success-reg">
      <div class="success-bg"><img src="<?=PATH?>images/success-reg.jpg"> </div>
      <div class="success-txt">
          <p> Hi，<?=$user['name']?></p>
          <p>恭喜您成为卓因会员</p>
      </div>
      <div class="btn">
          <button><a href="<?=PATH?>index.php" style="color:#fff;">提交</a></button>
      </div>
  </section>
</body>
</html>
