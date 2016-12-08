<script language="javascript" type="text/javascript" src="<?=PATH?>js/adver.js"></script>
<?
$list = $db->getList("adver", "state=1");
foreach($list as $val){
    echo "<script>AdPrepare(".$val['id'].",'".$val['title']."','".$val['url']."','".$val['mode']."','".PATH.UPLOAD_PATH.$val['pic']."',".$val['width'].",".$val['height'].",210,".rand(10,12).",'true');</script>";
}
?>