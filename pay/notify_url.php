<?php
include_once("../init.php");
include_once("WxPayPubHelper/WxPayPubHelper.php");

$postStr = simplexml_load_string($GLOBALS["HTTP_RAW_POST_DATA"]);

if (!empty($postStr)) {

    $order = $db->getByWhere('active', "orderId='$postStr->out_trade_no'");

    if ($order && $order['status'] == 1) {

        if($db->update("active",array('status'=>2), "orderId=".$order['orderId'])){
            echo "success";

//            //这里重新获取access_token
//            $token = curlGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WxPayConf_pub::APPID."&secret=".WxPayConf_pub::APPSECRET);
//            $token = json_decode($token, true);
//
//            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $token['access_token'];
//            $template = array(
//                'touser' => $order['openid'],
//                'template_id' => "hBBlhNkwo-ZwyCYT12jgTdZeO_oSSoJ0j_lET9POfcI",
//                'url' => "",
//                'topcolor' => "#FF0000",
//                'data' => array(
//                    'first' => array('value' => "尊敬的" . $order['userName'] . "您好,您选择的座位已售空，请重新选择座位号", "color" => "#ff0000"),    //函数传参过来的name
//                    'class' => array('value' => $info['title'], 'color' => '#333'),
//                    'time' => array('value' => $info['active_time'], 'color' => '#333'),
//                    'add' => array('value' => '怀宁路与龙图路交口安徽担保大厦2层', 'color' => '#333'),
//                    'remark' => array('value' => '请您联系并发送订单号至官方公共号，进行退款事宜', "color" => "ff0000")
//                )
//            );
//
//            $json = json_encode($template);
//            $res = curlGet($url, 'post', $json);
//            $res = json_decode($res, true);
//
//            if ($res['errcode'] != 0) {
//                logResult('log', "订购成功后推送消息：<br>错误代码：".$res['errcode']."<br>"); exit;
//            }
//
//            $refund = new Refund_pub();
//            $refund->parameters["out_trade_no"] = $order['orderno'];
//            $refund->parameters["out_refund_no"] = $order['orderno'];
//            $refund->parameters["total_fee"] = $order['num'] * $order['price'] * 100;
//            $refund->parameters["refund_fee"] = $order['num'] * $order['price'] * 100;
//            $refund->parameters["op_user_id"] = WxPayConf_pub::MCHID;
//
//            //调用结果
//            $refundResult = $refund->getResult();
//
//            logResult('log', $refundResult);
//            if ($refundResult["return_code"] == "FAIL") {
//                logResult('log', "退款消息：<br>错误代码：".$refundResult['return_msg']."<br>");
//            }
        } else {
            echo "fail";
        }
    }
}

?>