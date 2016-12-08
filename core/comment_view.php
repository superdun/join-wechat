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

$listUrl = "comment_list.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if (!$data = $db->getByWhere('comment', "id=$id")) {
	info("指定的记录不存在！");
}

//提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$status		= (int)$_POST["status"];
	$db->updateBySql('comment', "status=$status", "id=$id");
	header("Location: $listUrl");
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
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 评论管理</td>
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
					<td class="editHeaderTd" colSpan="2">评论管理</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">序号</td>
					<td class="editRightTd"><?=$data['sortnum']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">审核</td>
					<td class="editRightTd">
						<input type="radio" name="status" value="0">未审核
						<input type="radio" name="status" value="1" checked>已审核
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">信息标题</td>
					<td class="editRightTd"><?=$db->getField('info','title', 'id='.$data["infoId"])?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">用户名</td>
					<td class="editRightTd"><?=$db->getField('member','name', 'id='.$data["userId"])?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">内容</td>
					<td class="editRightTd"><?=nl2br($data['content'])?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">评论时间</td>
					<td class="editRightTd"><?=date('Y-m-d', $data['createdTime'])?></td>
				</tr>
				<tr class="editFooterTr">
					<td class="editFooterTd" colSpan="2">
						<input type="submit" value=" 确 定 ">
						<input type="reset" value=" 重 填 ">
					</td>
				</tr>
			</table>
		</form>
		<?
		$db->close();
		?>
	</body>
</html>
