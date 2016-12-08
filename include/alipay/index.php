<?php

require_once("../init.php");

$order = $db->getByWhere("orders", "orderId=".$_GET['orderId']);
if ( empty( $order ) ) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>支付宝即时到账交易接口接口</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body text=#000000 bgColor=#ffffff leftMargin=0 topMargin=4>
订单提交中...
<div id="main">
    <form name="alipayment" action="alipayapi.php" method="post">
        <input type="hidden" size="30" name="WIDout_trade_no" value="<?=$order['orderId']?>" /><!--商户订单号-->
        <input type="hidden" size="30" name="WIDsubject" value="优茶季网订单号：<?=$order['orderId']?>" /><!--订单名称-->
        <input type="hidden" size="30" name="WIDtotal_fee" value="<?=$order['totalPrice']?>" /><!--付款金额-->
        <input type="hidden" size="30" name="WIDbody" value="优茶季网订单,收货人：<?=$order['userName']?>" /><!--订单描述-->
        <input type="hidden" size="30" name="WIDshow_url" value="http://test.hfcfwl.com/member_orders.php" /><!--商品展示地址-->
    </form>
</div>
<script>
    alipayment.submit();
</script>
</body>
</html>