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

$listUrl = "member_list.php?page=$page";
$viewUrl = "member_view.php?page=$page";
$editUrl = "member_edit.php?page=$page";
$adsUrl = "member_address.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_array = $_POST["ids"];
//    $action = trim($_POST["action"]);
//
//    echo $action;exit;
//
//    if (empty($action)) {
//        $db->close();
//        info("参数有误！");
//    }

    if (!is_array($id_array)) {
        $id_array = array($id_array);
    }

    //事务开始
    $db->query("begin");

    //删除记录
//    if ($action == "delete") {
        $sql = "delete from member where id in (" . implode(",", $id_array) . ")";
        if (!$db->query($sql))
        {
            $db->query("rollback");
            $db->close();
            info("删除信息失败！");
        } else {
            $sql = "delete from member_address where uid in (" . implode(",", $id_array) . ")";
            if (!$db->query($sql))
            {
                $db->query("rollback");
                $db->close();
                info("删除子信息失败！");
            }
        }
//    }

    $db->query("commit");
    $db->close();
    header("Location: $listUrl");
    exit();
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
<!--					<a href="--><?//=$editUrl?><!--">[增加]</a>-->
					<a href="javascript:reverseCheck(document.form1.ids);">[反向选择]</a>&nbsp;
					<a href="javascript:if(delCheck(document.form1.ids)) {document.form1.action.value = 'delete';document.form1.submit();}">[删除]</a>&nbsp;
				</td>
				<td align="right">
                    <?
                    //设置每页数
                    $page_size = DEFAULT_PAGE_SIZE;
                    //总记录数
                    $sql = "select count(*) as cnt from member";
                    $rst = $db->query($sql);
                    $row = $db->fetch_array($rst);
                    $record_count = $row["cnt"];
                    $page_count = ceil($record_count / $page_size);
                    $page_str = page($page, $page_count, $pageUrl);
                    ?>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<form name="form1" action="" method="post">
				<tr class="listHeaderTr">
					<td width="30"></td>
					<td width="60">ID</td>
					<td>帐号</td>
					<td>手机</td>
					<td>邮箱</td>
					<td width="80">状态</td>
<!--					<td width="80">收货地址</td>-->
				</tr>
				<?
				//列表
				$sql = "select * from member order by id asc";
				$rst = $db->query($sql);
				while ($row = $db->fetch_array($rst))
				{
					$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
				?>
					<tr class="<?=$css?>">
						<td><input type="checkbox" id="ids" name="ids[]" value="<?=$row["id"]?>"></td>
						<td><?=$row["id"]?></td>
						<td><a href="<?=$editUrl?>&id=<?=$row["id"]?>"><?=$row["name"]?></a></td>
                        <td><?=$row["phone"]?></td>
                        <td><?=$row["email"]?></td>
                        <td>
                            <?
                            switch ($row["status"])
                            {
                                case 0:
                                    echo "<font color=#FF9900>禁用</font>";
                                    break;
                                case 1:
                                    echo "正常";
                                    break;
                                default :
                                    echo "<font color=#FF0000>错误</font>";
                                    exit;
                            }
                            ?>
                        </td>
<!--                        <td><a href="--><?//=$adsUrl?><!--&id=--><?//=$row['id']?><!--">查看</a></td>-->
					</tr>
				<?
				}
				?>
                <tr class="listFooterTr">
                    <td colspan="10"><?=$page_str?></td>
                </tr>
			</form>
		</table>
		<?
		$db->close();
		?>
	</body>
</html>
