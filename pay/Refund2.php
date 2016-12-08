<?php

include_once("WxPayPubHelper/WxPayPubHelper.php");

class Refund extends Common_util_pub {

    private $appid, $mch_id, $nonce_str, $sign, $out_trade_no, $out_refund_no, $total_fee, $refund_fee, $op_user_id;

    public function __construct($appid, $mch_id, $out_trade_no, $out_refund_no, $total_fee, $refund_fee, $op_user_id){
        $this->appid            = $appid;
        $this->mch_id           = $mch_id;
        $this->out_trade_no     = $out_trade_no;
        $this->out_refund_no    = $out_refund_no;
        $this->total_fee        = $total_fee;
        $this->refund_fee       = $refund_fee;
        $this->op_user_id       = $op_user_id;
        $this->url              = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

        $this->init();
    }

    //初始化
    public function init(){
        //组装数据
        $array = array(
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'nonce_str' => $this->nonce_str,
            'sign' => $this->sign,
            'out_trade_no' => $this->out_trade_no,
            'out_refund_no' => $this->out_refund_no,
            'total_fee' => $this->total_fee,
            'refund_fee' => $this->refund_fee,
            'op_user_id' => $this->op_user_id,
        );

        //生成签名
        $array['nonce_str']  = $this->createNoncestr();
        $array['sign']  = $this->formatBizQueryParaMap($array, false);

        $xml = $this->arrayToXml($array);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);    //对证书来源进行检查
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
        curl_setopt($ch, CURLOPT_SSLCERT, WxPayConf_pub::SSLCERT_PATH);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
        curl_setopt($ch, CURLOPT_SSLKEY, WxPayConf_pub::SSLKEY_PATH);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'pem');
        curl_setopt($ch, CURLOPT_CAINFO, WxPayConf_pub::SSLCA_PATH);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            print_r($data);
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误代码：$error";
            curl_close($ch);
            echo false;
        }

    }
}
?>