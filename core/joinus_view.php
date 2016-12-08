<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, JOINUS) == false)
{
	info("没有权限！");
}


$id		= (int)$_GET["id"];
$page	= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
if ($id < 1)
{
	info("参数有误！");
}


$listUrl = "joinus_list.php?page=$page";


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

$sql = "select id, sortnum, name, sex, edu, birth, identity, address, tel, email, hangye, qudao, xiaoshou, yuangong, jaddress, touru, trfs, jyfs, dmzk, yydm, dmcx, sj, yw, az, sh, qt, ppmc, dpsl, mj, dmsz, yxl, hyzjs, xgjy, zjtr, xsgx, gggx, jysj, xxjh, files, create_time, ip, state from joinus where id=$id";
$rst = $db->query($sql);
if ($row = $db->fetch_array($rst))
{
	$sortnum		= $row["sortnum"];
	$name			= $row["name"];
	$sex			= $row["sex"];
	$edu			= $row["edu"];
	$birth			= $row["birth"];
	$identity		= $row["identity"];
	$address		= $row["address"];
	$tel			= $row["tel"];
	$email			= $row["email"];
	$hangye			= $row["hangye"];
	$qudao			= $row["qudao"];
	$xiaoshou		= $row["xiaoshou"];
	$yuangong		= $row["yuangong"];
	$jaddress		= $row["jaddress"];
	$touru			= $row["touru"];
	$trfs			= $row["trfs"];
	$jyfs			= $row["jyfs"];
	$dmzk			= $row["dmzk"];
	$yydm			= $row["yydm"];
	$dmcx			= $row["dmcx"];
	$sj				= $row["sj"];
	$yw				= $row["yw"];
	$az				= $row["az"];
	$sh				= $row["sh"];
	$qt				= $row["qt"];

	$ppmc			= $row["ppmc"];
	$dpsl			= $row["dpsl"];
	$mj				= $row["mj"];
	$dmsz			= $row["dmsz"];
	$yxl			= $row["yxl"];

	$ppmccnt		= explode("|", $ppmc);
	$dpsl			= explode("|", $dpsl);
	$mj				 = explode("|", $mj);
	$dmsz			= explode("|", $dmsz);
	$yxl			= explode("|", $yxl);

	$hyzjs			= $row["hyzjs"];
	$xgjy			= $row["xgjy"];
	$zjtr			= $row["zjtr"];
	$xsgx			= $row["xsgx"];
	$gggx			= $row["gggx"];
	$jysj			= $row["jysj"];
	$xxjh			= $row["xxjh"];
	$files			= $row["files"];
	$create_time	= $row["create_time"];
	$ip				= $row["ip"];
	$state			= $row["state"];

	if ($state == 0)
	{
		$sql = "update joinus set state=1 where id=$id";
		$db->query($sql);

		$state = 1;
	}
}
else
{
	$db->close();
	info("指定的记录不存在！");
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
				<td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 在线加盟</td>
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
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">一、申请人资料</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">序号</td>
                    <td class="editRightTd"><?=$sortnum?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">姓名</td>
                    <td class="editRightTd"><?=$name?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">性别</td>
                    <td class="editRightTd"><?=$sex?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">学历</td>
                    <td class="editRightTd"><?=$edu?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">出生年月</td>
                    <td class="editRightTd"><?=$birth?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">身份证号</td>
                    <td class="editRightTd"><?=$identity?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">电话/传真</td>
                    <td class="editRightTd"><?=$tel?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">电子邮箱</td>
                    <td class="editRightTd"><?=$email?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">目前从事行业</td>
                    <td class="editRightTd"><?=$hangye?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">得知本加盟信息的渠道</td>
                    <td class="editRightTd"><?=$qudao?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">上一年的销售总额（万元）</td>
                    <td class="editRightTd"><?=$xiaoshou?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">员工人数</td>
                    <td class="editRightTd"><?=$yuangong?></td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">二、经营事项</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">加盟意向</td>
                    <td class="editRightTd">申请在 <?=$jaddress?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">计划投入</td>
                    <td class="editRightTd">金额（人民币）<?=$touru?> 左右</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">投入方式</td>
                    <td class="editRightTd"><?=$trfs?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">申请人参与经营方式</td>
                    <td class="editRightTd"><?=$jyfs?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">店面状况</td>
                    <td class="editRightTd"><?=$dmzk?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">已有店面</td>
                    <td class="editRightTd"><?=nl2br($yydm)?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">计划人员配置</td>
                    <td class="editRightTd">店面促销（<?=$dmcx?>）人、设计（<?=$sj?>）人、业务（<?=$yw?>）人、安装（<?=$az?>）人、售后（<?=$sh?>）人、其他： <?=$qt?></td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">三、市场分析</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">申请所在地较有名气的移门、衣柜品牌</td>
                    <td class="editRightTd">
						<table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
							<tr class="editTr">
								<td class="editRightTd" align="center">品牌名称</td>
								<td class="editRightTd" align="center">店铺数量</td>
								<td class="editRightTd" align="center">面积（平方米）</td>
								<td class="editRightTd" align="center">店面所在商场、街道名称</td>
								<td class="editRightTd" align="center">月销量</td>
							</tr>
							<?
							for ($i=0; $i<count($ppmccnt); $i++) {
							?>
								<tr class="editTr">
									<td class="editRightTd" align="center"><?=$ppmccnt[$i]?></td>
									<td class="editRightTd" align="center"><?=$dpsl[$i]?></td>
									<td class="editRightTd" align="center"><?=$mj[$i]?></td>
									<td class="editRightTd" align="center"><?=$dmsz[$i]?></td>
									<td class="editRightTd" align="center"><?=$yxl[$i]?></td>
								</tr>
							<?
							}
							?>
						</table>
					</td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">四、您现在从事什么行业？营销方式有哪些？经营状况如何？请简述 </td>
                </tr>
				<tr class="editTr">
                    <td class="editLeftTd"></td>
                    <td class="editRightTd"><?=nl2br($hyzjs)?></td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">五、你认为自己申请经营产品有哪方面的优势？ </td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">相关从业经验</td>
                    <td class="editRightTd"><?=$xgjy?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">资金投入</td>
                    <td class="editRightTd"><?=$zjtr?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">销售网络及其关系</td>
                    <td class="editRightTd"><?=$xsgx?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">当地公共关系</td>
                    <td class="editRightTd"><?=$gggx?></td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">六、准备经营时间 </td>
                </tr>
				<tr class="editTr">
                    <td class="editLeftTd"></td>
                    <td class="editRightTd"><?=$jysj?></td>
                </tr>
				<tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">七、对客来福移门衣柜在当地推广、经营详细计划 </td>
                </tr>
				<tr class="editTr">
                    <td class="editLeftTd"></td>
                    <td class="editRightTd"><?=nl2br($xxjh)?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">留言时间</td>
                    <td class="editRightTd"><?=$create_time?></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">留言IP</td>
                    <td class="editRightTd"><?=$ip?></td>
                </tr>
			</form>
		</table>
		<?
        $db->close();
		?>
	</body>
</html>
