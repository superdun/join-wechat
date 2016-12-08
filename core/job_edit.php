<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, JOB_ADVANCEDID) == false)
{
	info("没有权限！");
}


$id		= (int)$_GET["id"];
$page	= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;

$listUrl = "job_list.php?page=$page";


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

//提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$sortnum		= (int)$_POST["sortnum"];
	$state			= (int)$_POST["state"];
	$showForm		= (int)$_POST["showForm"];
	$name			= htmlspecialchars(trim($_POST["name"]));
	$department		= htmlspecialchars(trim($_POST["department"]));
	$num			= htmlspecialchars(trim($_POST["num"]));
	$tel			= htmlspecialchars(trim($_POST["tel"]));
	$place			= htmlspecialchars(trim($_POST["place"]));
	$email			= htmlspecialchars(trim($_POST["email"]));
	$content		= $_POST["content"];

	if (empty($id))
	{
		$id = $db->getMax("job", "id") + 1;
		$sql = "insert into job(id, sortnum, state, showForm, name, department, num, tel, place, email, content) values('$id', $sortnum, $state, $showForm, '$name', '$department', '$num', '$tel', '$place', '$email', '$content')";
	}
	else
	{
		$sql = "update job set sortnum=$sortnum, state=$state, showForm=$showForm, name='$name', email='$email', content='$content' where id='$id'";
	}
	$rst = $db->query($sql);
	$db->close();
	header("Location: $listUrl");
}
else
{
	if ($id == "")
	{
		$sortnum 	= $db->getMax("job", "sortnum") + 10;
		$state		= 1;
		$showForm	= 1;
	}
	else
	{
		$sql = "select * from job where id='$id'";
		$rst = $db->query($sql);
		if ($row = $db->fetch_array($rst))
		{
			$id				= $row["id"];
			$sortnum		= $row["sortnum"];
			$state			= $row["state"];
			$showForm		= $row["showForm"];
			$name			= $row["name"];
			$department		= $row["department"];
			$num			= $row["num"];
			$tel			= $row["tel"];
			$place			= $row["place"];
			$email			= $row["email"];
			$content		= $row["content"];
		}
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
		<script charset="utf-8" src="kindeditor/kindeditor-min.js"></script>
		<script>
			KindEditor.ready(function(K) {
				var editor = K.create('textarea[name="content"]', {
					uploadJson : 'kindeditor/php/upload_json.php',
					fileManagerJson : 'kindeditor/php/file_manager_json.php',
					pasteType : 1,
					allowFileManager : true,
					afterCreate : function() {
						var self = this;
						K.ctrl(document, 13, function() {
							self.sync();
							K('form[name=form1]')[0].submit();
						});
						K.ctrl(self.edit.doc, 13, function() {
							self.sync();
							K('form[name=form1]')[0].submit();
						});
					}
				});
			});
		</script>
		<script type="text/javascript">
			function check(form)
			{
				if (form.sortnum.value.match(/\D/))
				{
					alert("请输入合法的序号！");
					form.sortnum.focus();
					return false;
				}

				return true;
			}
		</script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 招聘职位</td>
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
					<td class="editHeaderTd" colSpan="2">招聘职位</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">序号</td>
					<td class="editRightTd"><input type="text" name="sortnum" value="<?=$sortnum?>" size="5" maxlength="5"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">状态</td>
					<td class="editRightTd">
						<input type="radio" name="state" value="1"<? if ($state == 1) echo " checked"?>>显示
						<input type="radio" name="state" value="0"<? if ($state == 0) echo " checked"?>>不显示
					</td>
				</tr>
				<tr class="editTr" style="display: none">
					<td class="editLeftTd">有无表单</td>
					<td class="editRightTd">
						<input type="radio" name="showForm" value="1"<? if ($showForm == 1) echo " checked"?>>有
						<input type="radio" name="showForm" value="0"<? if ($showForm == 0) echo " checked"?>>无
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">职位名称</td>
					<td class="editRightTd"><input type="text" name="name" value="<?=$name?>" maxlength="100" size="30"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">招聘部门</td>
					<td class="editRightTd"><input type="text" name="department" value="<?=$department?>" maxlength="100" size="30"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">招聘人数</td>
					<td class="editRightTd"><input type="text" name="num" value="<?=$num?>" maxlength="100" size="30"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">联系电话</td>
					<td class="editRightTd"><input type="text" name="tel" value="<?=$tel?>" maxlength="100" size="50"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">工作地点</td>
					<td class="editRightTd"><input type="text" name="place" value="<?=$place?>" maxlength="100" size="50"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">电子信箱</td>
					<td class="editRightTd"><input type="text" name="email" value="<?=$email?>" maxlength="100" size="30"></td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">岗位要求</td>
					<td class="editRightTd"><textarea name="content" style="width:700px; height:300px;"><?=$content?></textarea>
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
		<?
		$db->close();
		?>
	</body>
</html>
