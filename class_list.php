<?
require_once("init.php");
$area   = htmlspecialchars(trim($_GET["area"]));

require_once "wechat/jssdk.php";
$jssdk = new JSSDK("wxa22ff2d6db1e54cf", "06022b63f8c80460359a49a2310fbcf9");
$signPackage = $jssdk->getSignPackage();
?>

<!doctype html>
<html>
<head>
    <? require_once("head.php"); ?>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=SI5GU3vAeHL6SHuoUHR8Cq4lwsfEz6fk"></script>
</head>
<body>

    <div id="allmap" style="width: 100%; height: 350px;"></div>
    <section class="banji-a">
        <div class="class_list">区域选择：
            <select  onchange="parent.location.href=options[selectedIndex].value">
                <option value="?area=0">全部</option>
                <option value="?area=黄浦区"<?if($area == '黄浦区') echo " selected"?>>黄浦区</option>
                <option value="?area=徐汇区"<?if($area == '徐汇区') echo " selected"?>>徐汇区</option>
                <option value="?area=长宁区"<?if($area == '长宁区') echo " selected"?>>长宁区</option>
                <option value="?area=静安区"<?if($area == '静安区') echo " selected"?>>静安区</option>
                <option value="?area=普陀区"<?if($area == '普陀区') echo " selected"?>>普陀区</option>
                <option value="?area=闸北区"<?if($area == '闸北区') echo " selected"?>>闸北区</option>
                <option value="?area=虹口区"<?if($area == '虹口区') echo " selected"?>>虹口区</option>
                <option value="?area=杨浦区"<?if($area == '杨浦区') echo " selected"?>>杨浦区</option>
                <option value="?area=闵行区"<?if($area == '闵行区') echo " selected"?>>闵行区</option>
                <option value="?area=宝山区"<?if($area == '宝山区') echo " selected"?>>宝山区</option>
                <option value="?area=嘉定区"<?if($area == '嘉定区') echo " selected"?>>嘉定区</option>
                <option value="?area=浦东新区"<?if($area == '浦东新区') echo " selected"?>>浦东新区</option>
                <option value="?area=金山区"<?if($area == '金山区') echo " selected"?>>金山区</option>
                <option value="?area=松江区"<?if($area == '松江区') echo " selected"?>>松江区</option>
                <option value="?area=青浦区"<?if($area == '青浦区') echo " selected"?>>青浦区</option>
                <option value="?area=奉贤区"<?if($area == '奉贤区') echo " selected"?>>奉贤区</option>
                <option value="?area=崇明县"<?if($area == '崇明县') echo " selected"?>>崇明县</option>
            </select>
        </div>
        <div class="banji-item">
            <ul class="listinfo_01">
                <?
                $where  = " and 1=1";
                if($area){
                    $where .= " and area='$area'";
                }
                $list = $db->getList("kaiban", "status>0 $where");
                foreach($list as $val){
                ?>
                    <li class="clearfix">
                        <?
                        if($val['status'] == 2 || $val['num'] < 1){
                        ?>
                            <span>已满额</span>
                            <a href="javascript:;"><?=$val['title']?></a>
                        <?
                        } else {
                        ?>
                            <a href="<?=PATH?>class_view.php?id=<?=$val['id']?>"><span class="bg-blue">还差<?=$val['num']?>人</span></a>
                            <a href="<?=PATH?>class_view.php?id=<?=$val['id']?>"><?=$val['title']?></a>
                        <?
                        }
                        ?>
                    </li>
                <?
                }
                ?>
            </ul>
        </div>
    </section>
</body>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    /*
     * 注意：
     * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
     * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
     * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
     *
     * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
     * 邮箱地址：weixin-open@qq.com
     * 邮件主题：【微信JS-SDK反馈】具体问题
     * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
     */
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'openLocation',
            'getLocation',
            'translateVoice'
            // 所有要调用的 API 都要加到这个列表中
        ]
    });
    wx.ready(function () {

        wx.checkJsApi({
            jsApiList: [
                'openLocation',
                'getLocation'
            ],
            success: function (res) {
                // alert(JSON.stringify(res));
                // alert(JSON.stringify(res.checkResult.getLocation));
                if (res.checkResult.getLocation == false) {
                    alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                    return;
                }
            }
        });

        // 在这里调用 API
        wx.getLocation({
            success: function (res) {
                var location = [];
                location['latitude'] = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                location['longitude'] = res.longitude; // 经度，浮点数，范围为180 ~ -180
                location['speed'] = res.speed; // 速度，以米/每秒计
                location['accuracy'] = res.accuracy; // 位置精度

                //alert(JSON.stringify(res));

                // 百度地图API功能
                if(location['latitude'] != "" && location['longitude'] != ""){
                    var map = new BMap.Map("allmap");    // 创建Map实例
                    //map.centerAndZoom(new BMap.Point(location['longitude'], location['latitude']), 11);  // 初始化地图,设置中心点坐标和地图级别
                    //map.addControl(new BMap.MapTypeControl());   //添加地图类型控件
                    //map.setCurrentCity("上海");          // 设置地图显示的城市 此项是必须设置的
                    //map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
                    map.centerAndZoom("上海",13);

                    var data_info = [];
                    <?
                    foreach($list as $val){
                    ?>
                        data_info.push([<?=$val['long']?>, <?=$val['lat']?>, "<p>班级名称：<?=$val['title']?></p><?=cutstr_html($val['intro'])?>"]);
                    <?
                    }
                    ?>
                    var opts = {
                        width : 200,     // 信息窗口宽度
                        height: 150,     // 信息窗口高度
                        title : "信息窗口" // 信息窗口标题
                    };
                    for(var i=0;i<data_info.length;i++){
                        var marker = new BMap.Marker(new BMap.Point(data_info[i][0],data_info[i][1]));  // 创建标注
                        var content = data_info[i][2];
                        map.addOverlay(marker);               // 将标注添加到地图中
                        addClickHandler(content,marker);
                    }
                    function addClickHandler(content,marker){
                        marker.addEventListener("click",function(e){
                            openInfo(content,e)}
                        );
                    }
                    function openInfo(content,e){
                        var p = e.target;
                        var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
                        var infoWindow = new BMap.InfoWindow(content);  // 创建信息窗口对象
                        map.openInfoWindow(infoWindow,point); //开启信息窗口
                    }
                }
            },
            cancel: function (res) {
                alert('用户拒绝授权获取地理位置');
            }
        });

    });
</script>

</html>
