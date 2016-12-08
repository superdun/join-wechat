<?php

include_once("../init.php");
include_once("WxPayPubHelper/WxPayPubHelper.php");

$refund = new Refund_pub();
$refund->parameters["out_trade_no"] = "1442571270PF2016ZBKTD67613A6TSQD";
$refund->parameters["out_refund_no"] = "1442571270PF2016ZBKTD67613A6TSQD";
$refund->parameters["total_fee"] = "2";
$refund->parameters["refund_fee"] = "2";
$refund->parameters["op_user_id"] = WxPayConf_pub::MCHID;

//调用结果
$refundResult = $refund->getResult();

logResult('log', $refundResult);
if ($refundResult["return_code"] == "FAIL") {
    logResult('log', "退款消息：<br>错误代码：".$refundResult['return_msg']."<br>");
}

?>