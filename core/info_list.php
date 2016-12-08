<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


$class_id		= trim($_GET["class_id"]);
$select_class	= empty($_GET["select_class"]) ? $class_id : trim($_GET["select_class"]);
$select_state	= (int)$_GET["select_state"];
$keyword		= urlencode(trim($_GET["keyword"]));
$page			= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
if (empty($class_id) || !checkClassID($class_id, 2))
{
	info("参数有误！");
}

//if ( substr($class_id, 0, CLASS_LENGTH)=='102' )
//{
//    header("Location: product.php?class_id=$class_id");
//    exit();
//}

if (strlen($select_class) % CLASS_LENGTH != 0 && !checkClassID($select_class, strlen($select_class) / CLASS_LENGTH))
{
	info("参数有误！");
}

//权限检查
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && $session_admin_grade != ADMIN_ADVANCED && hasInclude($session_admin_popedom, substr($class_id, 0, CLASS_LENGTH)) != true && hasInclude($session_admin_popedom, $class_id) != true)
{
	info("没有权限！");
}

$listUrl	= "info_list.php?class_id=$class_id&select_class=$select_class&select_state=$select_state&keyword=$keyword&page=$page";
$editUrl	= "info_edit.php?class_id=$class_id&select_class=$select_class&select_state=$select_state&keyword=$keyword&page=$page";
$createUrl	= "info_edit.php?class_id=$class_id&select_class=$select_class&select_state=$select_state&keyword=$keyword&page=$page&create=1";
$baseUrl	= "info_list.php?class_id=$class_id";
$csvUrl		= "product_csv.php?select_class=$select_class&select_state=$select_state&keyword=$keyword";


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);


//分类记录设置
$base_id = substr($class_id, 0, CLASS_LENGTH);
if(!$data = $db->getByWhere("info_class", "id='$class_id'")){
	info("分类不存在！");
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

	if (!is_array($id_array))
	{
		$id_array = array($id_array);
	}

	//事务开始
	$db->query("begin");

	//删除记录
	if ($action == "delete")
	{
		//权限检查
		if ($session_admin_grade == ADMIN_COMMON)
		{
			$sql = "select pic, annex, files from info where id in (" . implode(",", $id_array) . ") and state=0 and admin_id=$session_admin_id";
		}
		else
		{
			$sql = "select pic, annex, files from info where id in (" . implode(",", $id_array) . ")";
		}

		$rst = $db->query($sql);
		while ($row = $db->fetch_array($rst))
		{
			$pic	.= $row["pic"] . ",";
			$annex	.= $row["annex"] . ",";
			$files	.= $row["files"] . ",";
		}

		//权限检查
		if ($session_admin_grade == ADMIN_COMMON)
		{
			$sql = "delete from info where id in (" . implode(",", $id_array) . ") and state=0 and admin_id=$session_admin_id";
		}
		else
		{
			$sql = "delete from info where id in (" . implode(",", $id_array) . ")";
		}

		if (!$db->query($sql))
		{
			$db->query("rollback");
			$db->close();
			info("删除信息失败！");
		} else {
            $sql = "delete from info_list where info_id in (" . implode(",", $id_array) . ")";
            if (!$db->query($sql))
            {
                $db->query("rollback");
                $db->close();
                info("删除子信息失败！");
            }
        }
	}
	//设置状态
	elseif ($action == "state")
	{
		$state = (int)$_POST["state"];
		$sql = "update info set state=$state where id in (" . implode(",", $id_array) . ")";
		if (!$db->query($sql))
		{
			$db->query("rollback");
			$db->close();
			info("设置状态失败！");
		}
	}
	//转移
	elseif ($action == "shift")
	{
		$state = (int)$_POST["state"];
		$sql = "update info set class_id=$state where id in (" . implode(",", $id_array) . ")";
		if (!$db->query($sql))
		{
			$db->query("rollback");
			$db->close();
			info("批量设置失败！");
		}
	}
	//复制
	elseif ($action == "copy")
	{
		// $state = (int)$_POST["state"];
		// $sql = "insert into info (id,class,title,content) select id,'2',title,content from article where class='1'"
		// $sql = "update info set class_id=$state where id in (" . implode(",", $id_array) . ")";
		// if (!$db->query($sql))
		// {
		// 	$db->query("rollback");
		// 	$db->close();
		// 	info("批量设置失败！");
		// }
	}

	$db->query("commit");
	$db->close();
	if ($action == "delete")
	{
		//删除图片
		deleteFiles($pic, 1);
		deleteFiles($annex, 1);
		deleteFiles($files, 2);
	}
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
				<td class="position">当前位置: 管理中心 -&gt; <?=$db->getTableFieldValue("info_class", "name", "where id='$class_id'")?> -&gt; 列表</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>
					<a href="<?=$createUrl?>">[增加]</a>
					<a href="javascript:reverseCheck(document.form1.ids);">[反向选择]</a>&nbsp;
					<a href="javascript:if(delCheck(document.form1.ids)) {document.form1.action.value = 'delete';document.form1.submit();}">[删除]</a>&nbsp;
					<?
					if ($db->getField("info_class", "has_sub", "id=$class_id") == 1){
						$list = $db->getList("info_class", "id like '" . $class_id . "%'", "order by sortnum asc");
						$list = getNodeData($list, substr($class_id, 0, strlen($class_id) - CLASS_LENGTH), CLASS_LENGTH);
					?>
						<select name="select_class" style="width:250px;" onChange="window.location='<?=$baseUrl?>&select_class=' + this.options[this.selectedIndex].value;">
							<?=optionsTree($list, $select_class)?>
						</select>
					<?
					}
					?>
					<select name="select_state" style="width:90px;" onChange="window.location='<?=$baseUrl?>&select_class=<?=$select_class?>&select_state=' + this.options[this.selectedIndex].value;">
						<option value="">请选择</option>
						<option value="1"<? if ($select_state == 1) echo " selected"?>>未审核</option>
						<option value="2"<? if ($select_state == 2) echo " selected"?>>正常</option>
						<option value="3"<? if ($select_state == 3) echo " selected"?>>置顶</option>
						<option value="4"<? if ($select_state == 4) echo " selected"?>>总置顶</option>
					</select>
					<select name="state" id="state" onChange="if(stateCheck(document.form1.ids)) {document.form1.action.value = 'state';document.form1.state.value='' + this.options[this.selectedIndex].value + '';document.form1.submit();}">
						<option value="-1">设置状态为</option>
						<option value="0">未审核</option>
						<option value="1">正常</option>
						<option value="2">置顶</option>
						<option value="3">总置顶</option>
					</select>
					<span style="margin-left:6px;">批量：</span>
					<select name="shiftOrCopy" id="shiftOrCopy">
						<!-- <option value="0">请选择</option> -->
						<option value="1" selected>转移</option>
						<!-- <option value="2">复制</option> -->
					</select>
					<select name="baseClass" id="baseClass">
						<option value="0">请选择</option>
						<?
						$list = $db->getList("info_class", "id like '___'", "order by sortnum asc");
						foreach($list as $val){
						?>
							<option value="<?=$val["id"]?>"><?=$val["name"]?></option>
						<?
						}
						?>
					</select>
					<select name="secondClass" id="secondClass">
						<option value="0">请选择</option>
					</select>
					<button id="shiftOrCopyBtn">确定</button>
					<script type="text/javascript">
					$(function(){
						$('#baseClass').change(function(){
							var html = '<option value="0">请选择</option>';
							if($(this).val() > 0){
								$.getJSON("getClass.php",
									{ class_id: $(this).val() },
									function(data){
										$.each(data,function(index,array){
											html += "<option value='"+array['id']+"'>"+array['name']+"</option>";
										});
										$('#secondClass').html(html);
									}
								);
							}
						});

						$('#shiftOrCopyBtn').click(function(event) {
							if(shfitOrCopyCheck(document.form1.ids)) {
								var val = $('#shiftOrCopy').val();
								if(val > 0){
									if($('#secondClass').val() > 0){
										switch (Number(val)){
											case 1:
											document.form1.action.value = 'shift';
											document.form1.state.value = $('#secondClass').val();
											document.form1.submit();
											break;
											case 2:
											document.form1.action.value = 'copy';
											document.form1.state.value = $('#secondClass').val();
											document.form1.submit();
											break;
										}
									} else {
										alert('请选择批量动作的目标菜单');
									}
								} else {
									alert('请选择批量动作');
								}
							}
						});
					});
					</script>
				</td>
				<td align="right">
					<form name="searchForm" method="get" action="" style="margin:0px;">
						查询：<input name="keyword" type="text" value="<?=urldecode($keyword)?>" placeholder="<?if ($info_state == 'pic'){ echo '请输入搜索的模板标题或模板编号';}else{ echo '请输入搜索的标题';}?>" size="30" maxlength="50" />
						<input type="submit" value="查询" style="width:60px;">
						<input type="hidden" name="class_id" value="<?=$class_id?>" />
						<input type="hidden" name="select_class" value="<?=$select_class?>" />
						<input type="hidden" name="select_state" value="<?=$select_state?>" />
					</form>
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

					<?
					if ($data['hasPic']){
					?>
						<td width="80">缩略图</td>
					<?
					}

					if ($data['hasPic2']){
					?>
						<td width="80">大图</td>
					<?
					}

					if ($data['hasLists']){
					?>
						<td width="80">多图</td>
					<?
					}

					if ($data['hasAnnex']){
					?>
						<td width="80">附件</td>
					<?
					}

					if ($data['hasViews']){
					?>
						<td width="80">浏览量</td>
					<?
					}

					if ($class_id == 103101){
					?>
						<td width="80">报名列表</td>
					<?
					}

					if ($class_id == 103102){
					?>
						<td width="80">评论列表</td>
					<?
					}

					if ($class_id == 103103){
						?>
						<td width="80">点赞次数</td>
						<?
					}

					if ($data['hasState']){
					?>
						<td width="80">状态</td>
					<?
					}
					?>
					<td width="150">发表时间</td>
				</tr>
				<?
				//筛选条件、权限
				if ($session_admin_grade == ADMIN_COMMON) {
					$SQL_ = "and a.state=0 and a.admin_id=$session_admin_id and title like '%" . urldecode($keyword) . "%' ";
				} else {
					switch ($select_state)
					{
						case 1:
							$SQL_ = "and a.state=0";
							break;
						case 2:
							$SQL_ = "and a.state=1";
							break;
						case 3:
							$SQL_ = "and a.state=2";
							break;
						case 4:
							$SQL_ = "and a.state=3";
							break;
						default:
							$SQL_ = "";
							break;
					}
				}

				//设置每页数
				$page_size		= DEFAULT_PAGE_SIZE;
				//总记录数
				$sql			= "select count(*) as cnt from info a where a.class_id like '" . $select_class . "%' and a.title like '%" . urldecode($keyword) . "%' $SQL_";
				$rst			= $db->query($sql);
				$row			= $db->fetch_array($rst);
				$record_count	= $row["cnt"];
				$page_count		= ceil($record_count / $page_size);
				//分页
				$page_str		= page($page, $page_count);
				//列表
				$sql = "select a.id, a.sortnum, a.title, a.spc, a.author, a.lists, a.source, a.website, a.pic, a.pic2, a.annex, a.views, a.files, a.createdTime, a.state, b.name from info a left join info_class b on a.class_id=b.id where a.class_id like '" . $select_class . "%' and (a.title like '%" . urldecode($keyword) . "%' or a.spc like '%" . urldecode($keyword) . "%') $SQL_ order by a.state desc, a.createdTime desc, a.sortnum desc";
				$sql .= " limit " . ($page - 1) * $page_size . ", " . $page_size;
				//echo $sql;
				$rst = $db->query($sql);
				while ($row = $db->fetch_array($rst))
				{
					$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
				?>
					<tr class="<?=$css?>">
						<td><input type="checkbox" id="ids" name="ids[]" value="<?=$row["id"]?>"></td>
						<td><?=$row["sortnum"]?></td>
						<td><a href="<?=$editUrl?>&id=<?=$row["id"]?>"><?=$row["title"]?></a></td>
						<?
						if ($data['hasPic']){
						?>
							<td style="padding: 5px;"><?=(empty($row["pic"])) ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["pic"] . "' target='_blank'><img src='" . UPLOAD_PATH_FOR_ADMIN . $row["pic"] . "' width='50' height='50'></a>"?></td>
						<?
						}

						if ($data['hasPic2']){
						?>
                            <td style="padding: 5px;"><?=(empty($row["pic2"])) ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["pic2"] . "' target='_blank'><img src='" . UPLOAD_PATH_FOR_ADMIN . $row["pic2"] . "' width='50' height='50'></a>"?></td>
						<?
						}

                        if ($data['hasLists']){
                        ?>
                            <td>
                                <a href="info_list_list.php?class_id=<?=$class_id?>&info_id=<?=$row["id"]?>">
                                    <?
                                    if ( !empty($row['lists']) ) {
                                    ?>
                                        <span style='color: #FF0000'>有[<?=$row["lists"]?>]</span>
                                    <?
                                    } else {
                                        echo '无';
                                    }
                                    ?>
                                </a>
                            </td>
					    <?
                        }

						if ($data['hasAnnex']){
						?>
							<td><?=$row["annex"] == "" ? "无" : "<a href='" . UPLOAD_PATH_FOR_ADMIN . $row["annex"] . "' target='_blank'>附件</a>"?></td>
						<?
						}

						if ($data['hasViews']){
						?>
							<td><?=$row["views"]?></td>
						<?
						}

						if ($class_id == 103101){
						?>
							<td width="80"><a href="active_list.php?infoId=<?=$row['id']?>">【<?=$db->getCount("active", "infoId=".$row['id'])?>】</a></td>
						<?
						}

						if ($class_id == 103102){
						?>
							<td width="80"><a href="comment_list.php?infoId=<?=$row['id']?>&class_id=<?=$class_id?>">【<?=$db->getCount("comment", "infoId=".$row['id'])?>】</a></td>
						<?
						}

						if ($class_id == 103103){
							?>
							<td width="80"><?=$db->getCount("vote", "infoId=".$row['id'])?></td>
							<?
						}

						if ($data['hasState']){
						?>
							<td>
								<?
								switch ($row["state"])
								{
									case 0:
										echo "<font color=#FF9900>未审核</font>";
										break;
									case 1:
										echo "正常";
										break;
									case 2:
										echo "<font color=#FF3300>置顶</font>";
										break;
									case 3:
										echo "<font color=#FF3300>总置顶</font>";
										break;
									default :
										echo "<font color=#FF0000>错误</font>";
										exit;
								}
								?>
							</td>
						<?
						}
						?>
						<td><?=date("Y-m-d", $row["createdTime"])?></td>
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
