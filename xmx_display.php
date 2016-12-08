<?
require_once("init.php");
require_once("member/isLogin.php");

$id	= (int)$_GET["id"];
if ($id < 1) {
    header("location: error.php"); exit;
}

if ($data = $db->getByWhere('info', "id=$id and state>0")) {
    $db->updateBySql('info', 'views=views+1', 'id='.$data['id']);
} else {
    header("location: error.php"); exit;
}

//一级栏目处理
$base_id    = substr($data['class_id'], 0, 3);
$second_id  = strlen($data['class_id']) >= 6 ? substr($data['class_id'], 0, 6) : 0;
$third_id   = strlen($data['class_id']) >= 9 ? substr($data['class_id'], 0, 9) : 0;
$base = $db->getByWhere("info_class", "id=$base_id");
if($base){
    if(!empty($base['url']) && !$second_id){
        header("location: ".$base['url']);
    }

    $category_id            = $base['id'];
    $category_name          = $base['name'];
    $category_state         = $base['info_state'];
    $category_seoTitle      = $base['seoTitle'];
    $category_keywords      = $base['keywords'];
    $category_description   = $base['description'];
}

//二级栏目处理
if($second_id){
    $second = $db->getByWhere("info_class", "id=$second_id");
} else {
    $second = $db->getByWhere("info_class", "id like '".$base['id']."___'", 'order by sortnum asc');
}
if($second){
    if(!empty($second['url']) && !$third_id){
        header("location: ".$second['url']);
    }

    $category_id            = $second['id'];
    $category_name          = $second['name'];
    $category_state         = $second['info_state'];
    $category_seoTitle      = $second['seoTitle'];
    $category_keywords      = $second['keywords'];
    $category_description   = $second['description'];
} else{
    header("location: ".PATH); exit;
}

//三级栏目处理
if($third_id){
    $third = $db->getByWhere("info_class", "id=$third_id");
} else {
    $third  = $db->getByWhere("info_class", "id like '".$second['id']."___'", 'order by sortnum asc');
}
if($third){
    if(!empty($third['url'])){
        header("location: ".$third['url']);
    }

    $category_id            = $third['id'];
    $category_name          = $third['name'];
    $category_state         = $third['info_state'];
    $category_seoTitle      = $third['seoTitle'];
    $category_keywords      = $third['keywords'];
    $category_description   = $third['description'];
}

//页面SEO标题、描述、关键字
$site['title']          = empty($data['seoTitle']) ? $site['title'] . "-" . $data['title'] : $data['seoTitle'];
$site['keywords']       = empty($data['keywords']) ? $site['keywords'] : $data['keywords'];
$site['description']    = empty($data['description']) ? $site['description'] : $data['description'];

//获取上下文信息
$related = $db->getList("info", "class_id=$category_id and state>0", "order by state desc, sortnum desc");
foreach($related as $key=>$val){
    if ($related[$key]['id'] == $id){
        if ($key < count($related)) {
            $next_id	= $related[$key + 1]['id'];
            $next_title	= $related[$key + 1]['title'];
        } else {
            $next_id	= 0;
        }
        if ($key > 0) {
            $prev_id    = $related[$key - 1]['id'];
            $prev_title	= $related[$key - 1]['title'];
        } else {
            $prev_id    = 0;
        }
    }
}
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
</head>
<body>
  <section class="zy-xmx">
      <div class="zy-bg"><img src="images/z-bg.jpg"> </div>
      <div class="zy-item">
          <div class="zy-pic">
              <img src="images/zy-mx-pic.png">
              <div class="rt-ico"><img src="images/zy-mx-ico.png"> </div>
          </div>
          <div class="zy-txt">恭喜 <em><?=$data['title']?></em> 小朋友<Br>在创客课程中荣获 <em>创客小达人</em> 称号！<Br>已点赞次数：<?=$data['vote']?></div>
          <div class="zy-zan">
              <a data-id="<?=$id?>" class="vote">点赞</a>
          </div>
          <div class="zy-zan-txt">
              <em>集赞获奖品</em>
              说明：集满20个赞即获得卓因惊喜礼品哦！
          </div>
          <div class="weix" style="padding-top: 20px;">
              <div class="txt">请关注“卓因青少年创意工场”<br>长按下面二维码即可关注我们</div>
              <div class="pic"><img src="images/zy-weix.png"> </div>
          </div>
      </div>
  </section>
  <script>
      $('.vote').on('click', function(){
          var _this = $(this);
          layer.load(1, {
              shade: [0.5,'#000']
          });

          $.ajax({
              url:'<?=PATH?>controller/ajaxForm.php',
              data:{
                  'action' : 'vote',
                  'redirectURL' : "<?=$redirectURL?>",
                  'data[infoId]' : _this.attr("data-id")
              },
              type:'post',
              cache:false,
              dataType:'json',
              success:function(result) {
                  alert(result.msg);
                  if(result.state){
                      _this.find('input, textarea').val('');
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
                  layer.closeAll();
              }
          });
          return false;
      });
  </script>
</body>
</html>
