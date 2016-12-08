<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

$class_id		= trim($_GET["class_id"]);

if(isset($class_id)){
	$sql = "select id, name from info_class where id like '".$class_id."___' order by sortnum asc";
	$rst = $db->query($sql);
	while ($row = $db->fetch_array($rst))
	{
		$html[] = array("id"=>$row['id'],"name"=>$row['name']);
	}
	echo json_encode($html);
}

?>
