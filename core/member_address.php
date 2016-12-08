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
$page = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
if ($id < 1)
{
    info("参数有误！");
}

$listUrl = "member_address.php?page=" . $page . "&id=" .$id;
$returnUrl = "member_list.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);
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
		<script type="text/javascript" src="images/jquery-1.8.2.min.js"></script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
                <td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 会员管理</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>
					<a href="<?=$returnUrl?>">[返回]</a>
				</td>
				<td align="right">

				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<form name="form1" action="" method="post">
				<tr class="listHeaderTr">
					<td width="60">ID</td>
					<td>收货人</td>
					<td>所在地区</td>
					<td>详细地址</td>
					<td>手机</td>
					<td>邮编</td>
				</tr>
				<?
				//列表
				$sql = "select * from member_address where uid=" .$id . " order by id asc";
				$rst = $db->query($sql);
				while ($row = $db->fetch_array($rst))
				{
					$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
				?>
					<tr class="<?=$css?>">
						<td><?=$row["id"]?></td>
						<td><?=$row["consignee"]?></td>
                        <td><?=$row["devision"]?></td>
                        <td><?=$row["addressDetail"]?></td>
                        <td><?=$row["phone"]?></td>
                        <td><?=$row["zipcode"]?></td>
					</tr>
				<?
				}
				?>
                <tr class="listFooterTr">
                </tr>
			</form>
		</table>
		<?
		$db->close();
		?>
	</body>
</html>
