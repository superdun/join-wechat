<?php
/**
 * JS_API支付demo
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
 */
include_once("../init.php");
include_once("WxPayPubHelper/WxPayPubHelper.php");

//使用jsapi接口
$jsApi = new JsApi_pub();
//=========步骤1：网页授权获取用户openid============
//通过code获得openid
//if (!isset($_GET['code'])) {
//    //触发微信返回code码
//    $url = $jsApi->createOauthUrlForCode(curPageURL());
//    Header("Location: $url"); exit;
//} else {
//    //获取code码，以获取openid
//    $code = $_GET['code'];
//    $jsApi->setCode($code);
//    $openid = $jsApi->getOpenId();
//    if (empty($openid)) {
//        Header("Location: ../zy_hdss_a.php"); exit;
//    }
//}

//判断订单号是否存在
$orderId = $_GET['orderId'];
$id = (int)$_GET['id'];

if ( empty($orderId) || empty($id) ){
    Header("Location: ../zy_hdss_b.php?id=$id"); exit;
}

//查询订单信息
$order = $db->getByWhere('active', "orderId=$orderId");
if (!$order) {
    header("location: ../zy_hdss_b.php?id=$id");
}

$total_fee = $order['total'] * 100;
$body = $db->getField('info', 'title', "id=".$order['infoId']);
$out_trade_no = $order['orderId'];

if(empty($order['openId'])) {
    exit("用户Id不能为空");
}
if(empty($total_fee)) {
    exit("支付金额不能为空");
}

//echo $out_trade_no .'|'.$total_fee.'|'.WxPayConf_pub::NOTIFY_URL; exit();

//=========步骤2：使用统一支付接口，获取prepay_id============
//使用统一支付接口
$unifiedOrder = new UnifiedOrder_pub();
$unifiedOrder->setParameter("openid", $order['openId']);
$unifiedOrder->setParameter("body", $body);//商品描述
$unifiedOrder->setParameter("out_trade_no", $out_trade_no);//商户订单号
$unifiedOrder->setParameter("total_fee", $total_fee);//总金额
$unifiedOrder->setParameter("notify_url", WxPayConf_pub::NOTIFY_URL);//通知地址
$unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
$prepay_id = $unifiedOrder->getPrepayId();

//=========步骤3：使用jsapi调起支付============
$jsApi->setPrepayId($prepay_id);
$jsApiParameters = $jsApi->getParameters();
//logResult('Parameter', $jsApiParameters); exit;
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title>
    <script type="text/javascript">

        window.history.forward(1);

        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function (res) {
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                    if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        alert('支付取消');
                        window.location = '../zy_hdss_b.php?id=<?=$id?>';
                    }

                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                        alert('报名成功');
                        window.location = '../zy_hdss_b.php?id=<?=$id?>';
                    }

                    if (res.err_msg == "get_brand_wcpay_request:fail") {
                        alert('支付失败,错误号'+res.err_code+res.err_desc+res.err_msg);
                        window.location = '../zy_hdss_b.php?id=<?=$id?>';
                    }
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            } else {
                jsApiCall();
            }
        }
        callpay();
    </script>
</head>
<body>
</body>
</html>