<?
require_once("../init.php");
require_once("isLogin.php");

$base['menu'] = "orders";
?>

<!doctype html>
<html style="font-size: 62.5%;">
<head>
    <? require_once("../head.php"); ?>
</head>
<body class="member">

<? require_once("../header.php"); ?>

<section>

    <? require_once("aside.php"); ?>

    <?
    $list = $db->getList("order", "userId=$userId and status>0", "order by createdTime desc");
    foreach($list as $key=>$val){
    ?>
        <div class="vip-order">
            <div class="time"><?=date('Y年m月d日 H:s:i', $val['createdTime'])?></div>
            <div class="ddh">订单号:<?=$val['orderId']?></div>
            <ul class="vip-list_01">
                <?
                $second_id  = substr($val["categoryId"], 0, 6);
                $third_id  = substr($val["categoryId"], 0, 9);
                ?>
                    <li>车型：<?=$db->getField("info_class", "name", "id=$second_id")?> - <?=$db->getField("info_class", "name", "id=$third_id")?></li>
                    <li>预约服务时间：<?=$val["time"]?></li>
                <?
                if($val["content"]){
                ?>
                    <li>备注：<?=$val["content"]?></li>
                <?
                }
                ?>

                <?
                $total = 0;
                foreach ($db->getList("order_product", "orderId=".$val["orderId"]." and userId=$userId") as $key2=>$val2) {
                    $total += (int)$val2['price'];
                ?>
                    <li><span>¥<?=$val2['price']?>元</span><?=$val2['productTitle']?></li>
                <?
                }
                ?>
            </ul>
            <div class="total-price">
                合计：¥<?=$total?>元
            </div>
        </div>
    <?
    }
    ?>
</section>

<? require_once("../footer.php"); ?>

</body>
</html>