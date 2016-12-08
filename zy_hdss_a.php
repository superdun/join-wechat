<?php
require("init.php");
$type = $_GET['type'];
?>
<html>
<?php require_once("head.php");?>
<body>
<section class="hdss-a">
    <div class="banner">
        <div class="bd">
        <ul>
        <?php
            $banner = $db->getList("banner","class_id=2");
            foreach ($banner as $list){
        ?>
            <li><img src="<?php echo PATH.UPLOAD_PATH.$list['pic']?>"> </li>
            <?php }?>
        </ul>
        </div>
        <div class="hd">
        <ul>

        </ul>
        </div>
        <script type="text/javascript">jQuery(".banner").slide({ titCell:".hd ul", mainCell:".bd ul",effect:"leftLoop", autoPage:"<li></li>", autoPlay:true });</script>
    </div>
    <div class="hdss-news-wrap">
        <div class="tit clearfix"><a href="?type=activity" class="on activity">活动</a> <a id="event" href="?type=event" class="event">赛事</a></div>
        <?php
        $type = $_GET['type'];
        $class_id = 103101101;
        if($type == "event"){
            $class_id = 103101102;
        }
        $page           = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
        $pageSize   = 3;
        $recordCount    = (int)$db->getCount('info', "class_id=$class_id and state>0");
        $pageCount	= ceil($recordCount / $pageSize);
        if ($page > $pageCount){ $page = $pageCount;}
            $arr = $db->getList("info","class_id=$class_id","order by state desc, sortnum desc","limit ". ($page - 1) * $pageSize . ", " . $pageSize);
            foreach ($arr as $list){
                $url = empty($list['website']) ? PATH."zy_hdss_b.php?id".$list['id'] : $list['website'];
        ?>
        <div class="hdss-item clearfix">
            <div class="pic fl"><a href="<?=$url?>"><img width=118 height=75 src="<?php echo PATH.UPLOAD_PATH.$list['pic']?>"> </a> </div>
            <div class="info fr">
                <h2><a href="<?=$url?>"><?php echo leftStr($list['title'],40)?></a> </h2>
                <?php echo $list['description']?>...
            </div>
        </div>
        <?php }?>
         <?if( $pageCount > 1){?>
        <div class="onload"><a>点击加载更多</a>
            <div class="page hide"><?=page3($page, $pageCount)?></div>
        <?}?>
        <input type="hidden" value="1" class="act" addval="1" /></div>
    </div>
</section>
</body>
<script>
$(function(){
	var type = "<?php echo $type?>";
	var class_id = <?php echo $class_id?>;
	if(type == "event"){
		$("#event").addClass("on");
		$(".activity").removeClass("on");
	}

	$(".onload").click(function(){
		var p = $(".act").attr("addval");
		$.post("<?php echo PATH?>controller/activity.php",{type:type,class_id:class_id,sum:p},function(data){
			var obj = jQuery.parseJSON(data);
			if(obj == ""){
				$(".onload a").html("没有更多了");
				$(".onload").fadeOut(2000);
			}
		    $.each(obj,function(i,item){
			    $(".hdss-item:last").after('<div class="hdss-item clearfix"><div class="pic fl"><a href="<?php echo PATH?>zy_hdss_b.php?id='+item.id+'"><img width=118 height=75 src="<?php echo PATH.UPLOAD_PATH?>'+item.pic+'"></a></div><div class="info fr"><h2><a href="<?echo PATH?>zy_hdss_b.php?id='+item.id+'">'+item.title+'</a> </h2>'+item.description+'...</div></div>');
			})
		})
		p = Number(p) + Number(1);
		$(".act").attr("addval",p);
	})
})
</script>
</html>
