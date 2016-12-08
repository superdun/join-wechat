<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, LINK_ADVANCEDID) == false)
{
	info("没有权限！");
}

$id				= trim($_GET["id"]);
$class_id		= trim($_GET["class_id"]);
$page			= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if (empty($class_id))
{
	$sql = "select id, name from brand_class order by sortnum asc limit 1";
}
else
{
	$sql = "select id, name from brand_class where id=$class_id";
}
$rst = $db->query($sql);
if ($row = $db->fetch_array($rst))
{
	$class_id		= $row["id"];
	$class_name		= $row["name"];
}
else
{
	$db->close();
	info("请增加品牌分类！", "main.php");
}

$listUrl	= "brand_list.php?class_id=$class_id&page=$page";
$editUrl	= "brand_edit.php?class_id=$class_id";

//删除
if ($id != "")
{
	$sql = "delete from brand where id='$id'";
	$rst = $db->query($sql);
	$db->close();
	if ($rst)
	{
		header("Location: $listUrl");
		exit;
	}
	else
	{
		info("删除失败！");
	}
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
				<td class="position">当前位置: 管理中心 -&gt; 列表</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>
					<a href="<?=$editUrl?>">[增加]</a>
					<select name="select_class" onChange="window.location='?class_id=' + this.options[this.selectedIndex].value;">
						<?
						$sql = "select id, name from brand_class order by sortnum asc";
						$rst = $db->query($sql);
						while ($row = $db->fetch_array($rst))
						{
							if ($class_id == $row["id"])
							{
						?>
								<option value="<?echo $row["id"]?>" selected><?echo $row["name"]?></option>
						<?
							}
							else
							{
						?>
								<option value="<?echo $row["id"]?>"><?echo $row["name"]?></option>
						<?
							}
						}
						?>
					</select>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<form name="form1" action="" method="post">
				<input type="hidden" name="action" value="">
				<input type="hidden" name="state" value="">
                <tr class="listHeaderTr">
                    <td>序号</td>
                    <td>名称</td>
                    <td>状态</td>
                    <td>删除</td>
                </tr>
				<?
				//设置每页数
				$page_size		= DEFAULT_PAGE_SIZE;
				//总记录数
				$sql			= "select count(*) as cnt from brand a where class_id =$class_id";
				$rst			= $db->query($sql);
				$row			= $db->fetch_array($rst);
				$record_count	= $row["cnt"];
				$page_count		= ceil($record_count / $page_size);
				//分页
				$page_str		= page($page, $page_count);
				//列表
				$sql = "select id, class_id, sortnum, name, state from brand where class_id=$class_id order by sortnum asc";
                $sql .= " limit " . ($page - 1) * $page_size . ", " . $page_size;
                $rst = $db->query($sql);
                while ($row = $db->fetch_array($rst))
                {
                	$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
                ?>
                    <tr class="<?=$css?>">
                       	<td><?=$row["sortnum"]?></td>
						<td><a href="<?=$editUrl?>&id=<?=$row["id"]?>"><?=$row["name"]?></a></td>
						<td>
							<?
							switch ($row["state"])
							{
								case 0:
									echo "<font color=#FF9900>不显示</font>";
									break;
								case 1:
									echo "显示";
									break;
								default :
									echo "<font color=#FF0000>错误</font>";
									exit;
							}
							?>
						</td>
						<td><a href="<?=$listUrl?>&id=<?=$row["id"]?>" onClick="return del();">删除</a></td>
					</tr>
				<?
                }
                ?>
                <tr class="listFooterTr">
                    <td colspan="15"><?=$page_str?></td>
                </tr>
			</form>
		</table>
		<?
        $db->close();
		?>
	</body>
</html>
