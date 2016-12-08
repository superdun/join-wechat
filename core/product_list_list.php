<?
require_once "init.php";
require_once "isadmin.php";
require_once "config.php";

$class_id		= trim($_GET["class_id"]);
$info_id		= (int)($_GET["info_id"]);
$page			= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;

if (empty($info_id) )
{
	info("指定的信息ID号无效！");
}

//权限检查
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && $session_admin_grade != ADMIN_ADVANCED && hasInclude($session_admin_popedom, substr($class_id, 0, CLASS_LENGTH)) != true && hasInclude($session_admin_popedom, $class_id) != true)
{
	info("没有权限！");
}

$listUrl	= "product_list_list.php?class_id=$class_id&info_id=$info_id&page=$page";
$editUrl	= "product_list_edit.php?class_id=$class_id&info_id=$info_id&page=$page";
$baseUrl	= "product_list.php?class_id=$class_id";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);


//查询顶级分类的记录设置
$sql = "select * from product where id=". $info_id;
$rst = $db->query($sql);
if (!($row = $db->fetch_array($rst)))
{
	$db->close();
	info("指定的信息ID号无效！");
}

//批量操作
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$id_array	= $_POST["ids"];
	$action		= trim($_POST["action"]);
	if (empty($action))
	{
		$db->close();
		info("参数有误！");
	}

    $cnt = 1;
	if (!is_array($id_array))
	{
        $id_array = array($id_array);
	} else {
        $cnt = count($id_array);
    }

	//事务开始
	$db->query("begin");

	//删除记录
	if ($action == "delete")
	{
		//权限检查
		if ($session_admin_grade == ADMIN_COMMON)
		{
			$sql = "select pic, pic2, annex, files from product_list where id in (" . implode(",", $id_array) . ") and state=0 and admin_id=$session_admin_id";
		}
		else
		{
			$sql = "select pic, pic2, annex, files from product_list where id in (" . implode(",", $id_array) . ")";
		}

		$rst = $db->query($sql);
		while ($row = $db->fetch_array($rst))
		{
			$pic	.= $row["pic"] . ",";
			$pic2	.= $row["pic2"] . ",";
			$annex	.= $row["annex"] . ",";
			$files	.= $row["files"] . ",";
		}

		//权限检查
		if ($session_admin_grade == ADMIN_COMMON)
		{
			$sql = "delete from product_list where id in (" . implode(",", $id_array) . ") and state=0 and admin_id=$session_admin_id";
		}
		else
		{
			$sql = "delete from product_list where id in (" . implode(",", $id_array) . ")";
		}

		if (!$db->query($sql))
		{
			$db->query("rollback");
			$db->close();
			info("删除信息失败！");
		} else {
            //删除图片
            deleteFiles($pic, 1);
            deleteFiles($annex, 1);
            deleteFiles($files, 2);

            //修改段落数量
            $sql = "update product set lists=lists-$cnt where id=$info_id";
            if (!$db->query($sql)){
                $db->query("rollback");
                $db->close();
                info("设置状态失败！");
            }
        }
	}
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
				<td class="position">当前位置: 管理中心 -&gt; <?=$db->getTableFieldValue("product_class", "name", "where id='$class_id'")?> -&gt; 列表</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$baseUrl?>">[返回]</a>
					<a href="<?=$listUrl?>">[刷新列表]</a>
					<a href="<?=$editUrl?>">[增加]</a>
					<a href="javascript:reverseCheck(document.form1.ids);">[反向选择]</a>&nbsp;
					<a href="javascript:if(delCheck(document.form1.ids)) {document.form1.action.value = 'delete';document.form1.submit();}">[删除]</a>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<form name="form1" action="" method="post">
				<input type="hidden" name="action" value="">
				<input type="hidden" name="state" value="">
				<tr class="listHeaderTr">
					<td width="30"></td>
					<td width="60">序号</td>
					<td>标题</td>
                    <td width="80">缩略图</td>
                    <!--<td width="80">大图</td>
					<td width="80">附件</td>-->
					<td width="80">状态</td>
					<td width="150">发表时间</td>
				</tr>
				<?
				//设置每页数
				$page_size		= DEFAULT_PAGE_SIZE;
				//总记录数
				$sql			= "select count(*) as cnt from product_list a where a.info_id=".$info_id;
				$rst			= $db->query($sql);
				$row			= $db->fetch_array($rst);
				$record_count	= $row["cnt"];
				$page_count		= ceil($record_count / $page_size);

				//分页
				$page_str		= page($page, $page_count);
				//列表
				$sql = "select * from product_list where info_id=".$info_id." order by create_time desc, sortnum desc";
				$sql .= " limit " . ($page - 1) * $page_size . ", " . $page_size;
				$rst = $db->query($sql);
				while ($row = $db->fetch_array($rst))
				{
					$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
				?>
					<tr class="<?=$css?>">
						<td><input type="checkbox" id="ids" name="ids[]" value="<?=$row["id"]?>"></td>
						<td><?=$row["sortnum"]?></td>
						<td><a href="<?=$editUrl?>&id=<?=$row["id"]?>"><?=$row["title"]?></a></td>
						<td style="padding: 5px;"><?=(empty($row["pic"])) ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["pic"] . "' target='_blank'><img src='" . UPLOAD_PATH_FOR_ADMIN . $row["pic"] . "' width='50' height='50'></a>"?></td>
<!--						<td>--><?//=(empty($row["pic"])) ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["pic"] . "' target='_blank'>图片</a>"?><!--</td>-->
						<!--<td><?/*=(empty($row["pic2"])) ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["pic2"] . "' target='_blank'>图片</a>"*/?></td>
                        <td><?/*=$row["annex"] == "" ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["annex"] . "' target='_blank'>附件</a>"*/?></td>-->
                        <td>
                            <?
                            switch ($row["state"])
                            {
                                case 0:
                                    echo "<font color=#FF9900>隐藏</font>";
                                    break;
                                case 1:
                                    echo "显示";
                                    break;
                            }
                            ?>
                        </td>
						<td><?=formatDate("Y-m-d", $row["create_time"])?></td>
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
