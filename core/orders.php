<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, JOB_ADVANCEDID) == false)
{
	info("没有权限！");
}

$listUrl = "orders.php?page=$page";
$editUrl = "orders_view.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

$page	    = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
$status	    = (int)$_GET["status"];
$orderId	= trim($_GET["orderId"]);
$keyword	= urlencode(trim($_GET["keyword"]));
$content	= htmlspecialchars($_GET["content"]);

//删除
if ( $orderId != "" && $content != "" )
{
    $db->updateField( 'orders', 'status', 5 , 'orderId='.$orderId);
    $db->updateField( 'orders', 'content', $content, 'orderId='.$orderId);
    info("订单取消成功！");
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
		<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 订单管理</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>&nbsp;
					<div  style="display: none">
						<span style="margin-left: 5px;">订单状态:</span>
						<select name="select_state" style="width:90px;" onChange="window.location='<?=$listUrl?>&state=' + this.options[this.selectedIndex].value;">
							<option value="0">全部</option>
							<option value="1"<? if ($status == 1) echo " selected"?>>未支付</option>
							<option value="2"<? if ($status == 2) echo " selected"?>>已付款</option>
							<option value="3"<? if ($status == 3) echo " selected"?>>已发货</option>
							<option value="4"<? if ($status == 4) echo " selected"?>>确认收货</option>
							<option value="5"<? if ($status == 5) echo " selected"?>>订单取消</option>
						</select>&nbsp;

						<form name="exportForm" method="post" action="order_export.php" style="margin:0px; display:inline-block;" target="_blank">
							<input id="d5221" class="Wdate" style="width:110px;" name="startTime" required type="text" onFocus="var d5222=$dp.$('d5222');WdatePicker({onpicked:function(){d5222.focus();},maxDate:'#F{$dp.$D(\'d5222\')}'})"/>
							<input id="d5222" class="Wdate" style="width:110px;" name="endTime" required type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'d5221\')}'})"/>
							<input type="submit" value="导出订单" style="width:80px;">
						</form>
					</div>
				</td>
				<td align="right">
					<form name="searchForm" method="get" action="" style="margin:0px;display: none">
						查询：<input name="keyword" type="text" value="<?=urldecode($keyword)?>" placeholder="请输入订单编号" size="30" maxlength="50" />
						<input type="submit" value="查询" style="width:60px;">
					</form>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<form name="form1" action="" method="post">
				<tr class="listHeaderTr">
					<td width="15%">姓名</td>
					<td>邮箱</td>
					<td>电话</td>
					<td>创建时间</td>
					<td>查看详情</td>
				</tr>
				<?
                //设置每页数
                $page_size = DEFAULT_PAGE_SIZE;
                if ( empty($state) ) {
                    $where = '1=1';
                } else {
                    $where = "status=$status";
                }
                if ( !empty( $keyword ) ) {
                    $where = " orderId like '%$keyword%'";
                }
                $sql = "select * from orders where $where order by createdTime desc";
				$sql .= " limit " . ($page - 1) * $page_size . ", " . $page_size;
				$rst = $db->query($sql);
				while ($row = $db->fetch_array($rst))
				{
					$css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
				?>
					<tr class="<?=$css?>">
						<td><?=$row["userName"]?></td>
						<td><?=$row["email"]?></td>
						<td><?=$row["phone"]?></td>
						<td><?=date("Y-m-d H:i:s", $row['createdTime'])?></td>
                        <td><a href="<?=$editUrl?>&orderId=<?=$row["orderId"]?>">查看详情</a></td>
					</tr>
				<?
				}
				?>
                <?
                //总记录数
                $sql = "select count(*) as cnt from orders";
                $rst = $db->query($sql);
                $row = $db->fetch_array($rst);
                $record_count = $row["cnt"];
                $page_count = ceil($record_count / $page_size);
                $page_str = page($page, $page_count, $pageUrl);
                if ( $page_count > 0 ) {
                ?>
                    <tr class="listFooterTr">
                        <td colspan="10"><?=$page_str?></td>
                    </tr>
                <?
                }
                ?>
			</form>
		</table>
		<?
		$db->close();
		?>
	</body>
</html>
