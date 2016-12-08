<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, JOB_APPLY_ADVANCEDID) == false)
{
	info("没有权限！");
}

$orderId	= trim($_GET["orderId"]);
$page		= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
$listUrl = "orders.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if ( $orderId != "" ) {

	$order = $db->getByWhere("orders", "orderId=".trim($_GET['orderId']));
	if ( empty( $order ) ) {
		header('Location: index.php');
		exit;
	}

	$data = $db->getList("order_product", "orderId=".$_GET['orderId']);
	foreach ( $data as $k => $v ) {
		$data[$k]['productTitle'] = $db->getTableFieldValue( "info", "title", "where id=".$v["productId"] );
		$data[$k]['genreTitle'] = $db->getTableFieldValue('productgenre', "title", "where id=".$v['genre'] );
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
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 订单详情</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[返回列表]</a>&nbsp;
				</td>
			</tr>
		</table>
        <table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
            <form name="form1" action="" method="post">
                <input type="hidden" name="orderId" maxlength="20" value="<?=$order['orderId']?>">

                <tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">订单详情</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">序号</td>
                    <td class="editRightTd"><?=$order['orderId']?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">姓名</td>
                    <td class="editRightTd"><?=$order['userName']?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">邮箱</td>
                    <td class="editRightTd"><?=$order['email']?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">手机</td>
                    <td class="editRightTd"><?=$order['phone']?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">创建时间</td>
                    <td class="editRightTd"><?=date("Y-m-d H:i:s", $order['createdTime'])?></td>
                </tr>
                <?
                if ( !empty($order['content']) ) {
                ?>
                    <tr class="editTr">
                        <td class="editLeftTd">备注</td>
                        <td class="editRightTd"><?=$order['content']?></td>
                    </tr>
                <?
                }
                ?>
            </table>
        </form>

        <table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable" style="margin-top: 10px;">
            <tr class="editHeaderTr">
                <td class="editHeaderTd" colSpan="5">商品列表</td>
            </tr>
            <tr class="listHeaderTr">
                <td width="10%">商品名称</td>
<!--                <td width="8%">规格</td>-->
<!--                <td width="8%">单价</td>-->
                <td width="8%">数量</td>
<!--                <td width="10%">小计</td>-->
            </tr>
            <?
            foreach ($data as $k => $v) {
                $css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
            ?>
                <tr class="<?=$css?>">
                    <td><a class="buy_also_pic" href="<?=PATH?>product_detail.php?id=<?=$v['productId']?>" target="_blank"><?=$v['productTitle']?></a></td>
<!--                    <td>--><?//=$v['genreTitle']?><!--</td>-->
<!--                    <td>--><?//=$v['price']?><!--</td>-->
                    <td><?=$v['num']?></td>
<!--                    <td>--><?//=$v['price'] * $v['num']?><!--</td>-->
                </tr>
                <?
            }
            ?>
            <tr class="listFooterTr" style="display: none">
                <td colspan="10" style="text-align: right;
    padding-right: 20px;
    color: #c00;
    font-size: 15px;
    font-weight: bold;">总计：¥<?= $order['totalPrice']; ?></td>
            </tr>
        </table>
		<?
		$db->close();
		?>
	</body>
</html>
