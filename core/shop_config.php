<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, CONFIG_ADVANCEDID) == false)
{
	info("没有权限！");
}


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);


//提交
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$data		= $_POST["data"];

	if ( empty($data['free']) || empty($data['postage']) )
	{
		$db->close();
		info("填写的参数不完整！");
	}

	$sql = "update shop_config set free='". $data['free'] ."', postage='". $data['postage'] ."' where id=1";
	$rst = $db->query($sql);
	$db->close();
	if ($rst)
	{
		info("保存成功！");
	}
	else
	{
		info("保存失败！");
	}
}

$sql = "select * from shop_config where id=1";
$rst = $db->query($sql);
if ($row = $db->fetch_array($rst))
{
	$data			= $row;
	$db->close();
}
else
{
	$db->close();
	info("还没有记录！");
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
				<td class="position">当前位置: 管理中心  -&gt; 高级管理 -&gt; 商店设置</td>
			</tr>
		</table>
		<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="20">
				<td></td>
			</tr>
		</table>
		<form name="form1" action="" method="post" onSubmit="return check(this);">
			<table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
				<tr class="editHeaderTr">
					<td class="editHeaderTd" colSpan="2">商店设置</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">包邮金额</td>
					<td class="editRightTd">
						<input type="text" name="data[free]" value="<?=$data['free']?>" size="50" maxlength="100" required>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">快递费用</td>
					<td class="editRightTd">
						<input type="text" name="data[postage]" value="<?=$data['postage']?>" size="50" maxlength="100" required>
					</td>
				</tr>
				<tr class="editFooterTr">
					<td class="editFooterTd" colSpan="2">
						<input type="submit" value=" 确 定 ">
						<input type="reset" value=" 重 填 ">
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
