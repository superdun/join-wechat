<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, MESSAGE_ADVANCEDID) == false)
{
	info("没有权限！");
}


$page = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;


$listUrl = "kaiban_list.php?page=$page";
$editUrl = "kaiban_edit.php?page=$page";


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);


//删除
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$id_array = $_POST["ids"];
	if (!is_array($id_array))
	{
		$id_array = array($id_array);
	}

	$db->query("begin");

	$sql = "delete from kaiban where id in (" . implode(",", $id_array) . ")";
	$rst = $db->query($sql);

	if ($rst)
	{
		$db->query("commit");
		$db->close();
		header("Location: $listUrl");
		exit();
	}
	else
	{
		$db->query("rollback");
		$db->close();
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
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 评论管理</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>&nbsp;
                    <a href="javascript:reverseCheck(document.form1.ids);">[反向选择]</a>&nbsp;
                    <a href="javascript:if(delCheck(document.form1.ids)) {document.form1.submit();}">[删除]</a>&nbsp;
				</td>
				<td align="right">
					<?
					//设置每页数
                    $page_size = DEFAULT_PAGE_SIZE;
    				//总记录数
                    $sql = "select count(*) as cnt from kaiban";
                    $rst = $db->query($sql);
                    $row = $db->fetch_array($rst);
                    $record_count = $row["cnt"];
                    $page_count = ceil($record_count / $page_size);

                    $page_str = page($page, $page_count, $pageUrl);
                    echo $page_str;
                    ?>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
            <form name="form1" action="" method="post">
                <tr class="listHeaderTr">
                    <td width="3%"></td>
                    <td width="8%">序号</td>
                    <td>班级名称</td>
                    <td>孩子姓名</td>
                    <td>年龄</td>
                    <td>性别</td>
                    <td>手机</td>
                    <td>开班年龄段</td>
                    <td>所在区/县</td>
                    <td>开班地址</td>
                    <td>班级状态</td>
                    <td>缺少人数</td>
                    <td>报名列表</td>
                    <td width="10%">提交时间</td>
					<td width="5%">操作</td>
				</tr>
                <?
                $sql = "select * from kaiban order by sortnum desc";
                $sql .= " limit " . ($page - 1) * $page_size . ", " . $page_size;
                $rst = $db->query($sql);
                while ($row = $db->fetch_array($rst))
                {
                    $css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
                ?>
                    <tr class="<?=$css?>">
                        <td><input type="checkbox" id="ids" name="ids[]" value="<?=$row["id"]?>"></td>
                        <td><?=$row["sortnum"]?></td>
                        <td><?=$row["title"]?></td>
                        <td><?=$row["name"]?></td>
                        <td><?=$row["age"]?></td>
                        <td><?=$row["sex"] == 1 ? "男" : "女"?></td>
						<td><?=$row["phone"]?></td>
						<td><?=$row["ageBracket"]?></td>
						<td><?=$row["area"]?></td>
						<td><?=$row["address"]?></td>
						<td>
							<?
							switch ($row["status"]){
								case 0:
									echo "<font color='#FF6600'>不显示</font>";
									break;
								case 1:
									echo "未满";
									break;
								case 2:
									 echo "<font color='#0066FF'>已满</font>";
									break;
								default:
									echo "<font color='#FF0000'>错误</font>";
									break;
							}
							?>
						</td>
						<td><?=$row["num"]?></td>
                        <td><a href="message_list.php?classId=<?=$row['id']?>">【<?=(int)$db->getCount("message", "classId=".$row['id'])?>】</a></td>
                        <td><?=date("Y-m-d", $row["createdTime"])?></td>
						<td><a href="<?=$editUrl?>&id=<?=$row["id"]?>">操作</a></td>
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
