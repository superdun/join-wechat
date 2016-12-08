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

$listUrl = "kaiban_list.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$data				= $_POST["data"];
	$data['content']          = replaceUpload(filterHtml($data['content']));
	$data['intro']          = replaceUpload(filterHtml($data['intro']));

	if ($db->update('kaiban', $data, "id=$id")) {
		header("location: $listUrl");
	} else {
		info("修改失败！");
	}
}

if (!$data = $db->getByWhere('kaiban', "id=$id")) {
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
				K.create('#content', {
					width : '700px',
					height : '200px',
					pasteType : 1,
					items : _items
				});
				K.create('#intro', {
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
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 班级编辑</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[返回列表]</a>&nbsp;
				</td>
			</tr>
		</table>
		<form name="form1" action="" method="post" enctype="multipart/form-data">
			<table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
				<tr class="editHeaderTr">
					<td class="editHeaderTd" colSpan="2">班级编辑</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">序号</td>
					<td class="editRightTd"><?=$data['sortnum']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">班级名称</td>
					<td class="editRightTd"><input type="text" name="data[title]" value="<?=$data['title']?>" required></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">经度</td>
					<td class="editRightTd"><input type="text" name="data[long]" value="<?=$data['long']?>" required></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">纬度</td>
					<td class="editRightTd"><input type="text" name="data[lat]" value="<?=$data['lat']?>" required></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">坐标拾取器</td>
					<td class="editRightTd"><a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">坐标拾取器</a>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">班级状态</td>
					<td class="editRightTd">
						<input type="radio" name="data[status]" value="0"<? if ($data['status'] == 0) echo " checked"?>> 不显示
						<input type="radio" name="data[status]" value="1"<? if ($data['status'] == 1) echo " checked"?>> 未满
						<input type="radio" name="data[status]" value="2"<? if ($data['status'] == 2) echo " checked"?>> 已满
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">缺少人数</td>
					<td class="editRightTd"><input type="text" name="data[num]" value="<?=$data['num']?>" required></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">孩子姓名</td>
					<td class="editRightTd"><?=$data['name']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">年龄</</td>
					<td class="editRightTd"><?=$data['age']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">性别</</td>
					<td class="editRightTd"><?=$data["sex"] == 1 ? "男" : "女"?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">手机</td>
					<td class="editRightTd"><?=$data['phone']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">开班年龄段</td>
					<td class="editRightTd"><?=$data['ageBracket']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">所在区/县</td>
					<td class="editRightTd"><?=$data['area']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">开班地址</td>
					<td class="editRightTd"><?=$data['address']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">提交时间</td>
					<td class="editRightTd"><?=date("Y-m-d", $data["createdTime"])?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">提交IP</td>
					<td class="editRightTd"><?=$data['ip']?></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">班级简介</td>
					<td class="editRightTd"><textarea id="intro" name="data[intro]" cols="100" rows="10"><?=$data['intro']?></textarea>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">班级介绍</td>
					<td class="editRightTd"><textarea id="content" name="data[content]" cols="100" rows="10"><?=$data['content']?></textarea>
					</td>
				</tr>
				<tr class="editFooterTr">
					<td class="editFooterTd" colSpan="2">
						<input type="submit" value=" 确 定 ">
					</td>
				</tr>
			</table>
		</form>
		<?
		$db->close();
		?>
	</body>
</html>
