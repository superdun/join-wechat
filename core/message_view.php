<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, MESSAGE_ADVANCEDID) == false)
{
	info("没有权限！");
}

$id		= (int)$_GET["id"];
$page	= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
if ($id < 1)
{
	info("参数有误！");
}

$listUrl = "message_list.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$data				= $_POST["data"];
	$data["sortnum"]	= (int)$data["sortnum"];
	$data["reply"]		= htmlspecialchars(trim($data["reply"]));
	$data["status"]		= (int)$data["status"];

	if ($db->add($table, $data)) {
		header("location: $listUrl");
	} else {
		info("回复留言失败！");
	}
}

if ($data = $db->getByWhere('message', "id=$id")) {
	$db->updateBySql('message', 'status=1', "id=$id");
} else {
	info("指定的记录不存在！");
}
?>


<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="-1000">
		<link href="images/admin.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="images/common.js"></script>
		<script charset="utf-8" src="kindeditor/kindeditor.js"></script>
		<script>
			KindEditor.ready(function(K) {
				K.create('textarea[name="reply"]', {
					width : '700px',
					height : '200px',
					pasteType : 1,
					items : _items
				});
			});
		</script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 留言簿</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[返回列表]</a>&nbsp;
				</td>
			</tr>
		</table>
		<form name="form1" action="" method="post">
			<table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
				<tr class="editHeaderTr">
					<td class="editHeaderTd" colSpan="2">留言簿</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">序号</td>
					<td class="editRightTd"><?=$data['sortnum']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">孩子姓名</td>
					<td class="editRightTd"><?=$data['name']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">年龄</td>
					<td class="editRightTd"><?=$data['age']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">性别</td>
					<td class="editRightTd"><?=$data['sex']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">联系电话</td>
					<td class="editRightTd"><?=$data['phone']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">留言时间</td>
					<td class="editRightTd"><?=date('Y-m-d',$data['createdTime'])?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">留言IP</td>
					<td class="editRightTd"><?=$data['ip']?></td>
				</tr>
			</table>
		</form>
		<?
		$db->close();
		?>
	</body>
</html>
