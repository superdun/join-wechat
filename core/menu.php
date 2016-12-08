<?
require_once "init.php";
require_once "isadmin.php";
require_once "config.php";

$menu_id = $_GET["menu_id"] ? trim($_GET["menu_id"]) : "";
$key = 0;
?>

<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="images/admin.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
			function expand(el) {
				childObj = document.getElementById("child" + el);
				if (childObj.style.display == "none") {
					childObj.style.display = "block";
				} else {
					childObj.style.display = "none";
				}
			}
		</script>
	</head>
	<body>
		<table width="170" height="100%" border="0" cellspacing="0" cellpadding="0" background="images/menu_bg.jpg">
			<tr>
				<td valign="top" align="center">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="10"></td>
						</tr>
					</table>
					<?
                    $base = $db->getList("info_class", "id like '" . CLASS_SPACE . "'", "order by sortnum asc");
					foreach($base as $key=>$val){
						$key++;
						if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || $session_admin_grade == ADMIN_ADVANCED || hasBegin4Include($session_admin_popedom, $val["id"]) == true){
					?>
							<table width="150" border="0" cellspacing="0" cellpadding="0">
								<tr height="22">
									<td background="images/menu_bt.jpg" style="padding-left:30px">
                                        <a href="javascript:void(0)" onClick="expand(<?=$key?>)" class="menuParent">
                                            <span <?=$val["isTop"] != 1 ? " style='color:red'" : ''?>><?=$val["name"]?></span>
                                        </a>
									</td>
								</tr>
								<tr height="4"><td></td></tr>
							</table>
							<table id="child<?=$key?>" width="150" border="0" cellspacing="0" cellpadding="0" style="display:<?=($val["id"] == $menu_id) ? "block" : "none"?>">
								<?
                                $second = $db->getList("info_class", "id like '" . $val["id"] . CLASS_SPACE . "'", "order by sortnum asc");
                                foreach($second as $val2){
									if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || $session_admin_grade == ADMIN_ADVANCED || hasInclude($session_admin_popedom, $val["id"]) == true || hasInclude($session_admin_popedom, $val2["id"]) == true){
								?>
										<tr height="20">
											<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
											<td><a href="info_list.php?class_id=<?=$val2["id"]?>" class="menuChild" target="main"><span <?=$val2["isTop"] != 1 ? " style='color:red'" : ''?>><?=$val2["name"]?></span></a></td>
										</tr>
								<?
									}
								}

								if ($session_admin_grade == ADMIN_HIDDEN || ($session_admin_grade == ADMIN_SYSTEM && $val["state"] == 1))
								{
								?>
									<tr height="20">
										<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
										<?
										if ($val["state"] == 1) {
										?>
											<td><a href="second_class_list.php?class_id=<?=$val["id"]?>" class="menuChild" target="main">分类管理</a></td>
										<?
										} else {
										?>
											<td><a href="second_class_list.php?class_id=<?=$val["id"]?>" class="menuChild" target="main"><font color="#FF0000">分类管理</font></a></td>
										<?
										}
										?>
									</tr>
								<?
								}
								?>
								<tr height="4">
									<td colspan="2"></td>
								</tr>
							</table>
					<?
						}
					}

                    //产品管理
                    $product = $db->getList("product_class", "id like '" . CLASS_SPACE . "'", "order by sortnum asc");
                    foreach($product as $val){
                        $key++;
                        if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || $session_admin_grade == ADMIN_ADVANCED || hasBegin4Include($session_admin_popedom, $val["id"]) == true){
                        ?>
                            <table width="150" border="0" cellspacing="0" cellpadding="0">
                                <tr height="22">
                                    <td background="images/menu_bt.jpg" style="padding-left:30px">
                                        <a href="javascript:void(0)" onClick="expand(<?=$key?>)" class="menuParent">
                                            <span <?=$val["isTop"] != 1 ? " style='color:red'" : ''?>><?=$val["name"]?></span>
                                        </a>
                                    </td>
                                </tr>
                                <tr height="4"><td></td></tr>
                            </table>
                            <table id="child<?=$key?>" width="150" border="0" cellspacing="0" cellpadding="0" style="display:<?=($val["id"] == $menu_id) ? "block" : "none"?>">
                                <?
                                $product2 = $db->getList("product_class", "id like '" . $val["id"] . CLASS_SPACE . "'", "order by sortnum asc");
                                foreach($product2 as $val2){
                                    if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || $session_admin_grade == ADMIN_ADVANCED || hasInclude($session_admin_popedom, $row["id"]) == true || hasInclude($session_admin_popedom, $val2["id"]) == true){
                                        ?>
                                        <tr height="20">
                                            <td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
                                            <td><a href="product_list.php?class_id=<?=$val2["id"]?>" class="menuChild" target="main"><span <?=$val2["isTop"] != 1 ? " style='color:red'" : ''?>><?=$val2["name"]?></span></a></td>
                                        </tr>
                                        <?
                                    }
                                }
                                ?>
                                <tr height="4">
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                        <?
                        }
                    }

					if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM)
					{
						$key++;
					?>
						<table width="150" border="0" cellspacing="0" cellpadding="0">
							<tr height="22">
								<td background="images/menu_bt.jpg" style="padding-left:30px"><a href="javascript:void(0)" onClick="expand(<?=$key?>)" class="menuParent">系统管理</a></td>
							</tr>
							<tr height="4">
								<td></td>
							</tr>
						</table>
						<table id="child<?=$key?>" width="150" border="0" cellspacing="0" cellpadding="0" style="DISPLAY:none">
							<tr height="20">
								<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
								<td><a href="config_base.php" class="menuChild" target="main">基本设置</a></td>
							</tr>
                            <tr height="20">
                                <td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
                                <td><a href="base_class_list.php" class="menuChild" target="main">栏目管理</a></td>
                            </tr>
							<tr height="20">
								<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
								<td><a href="admin_list.php" class="menuChild" target="main">角色管理</a></td>
							</tr>
							<tr height="4">
								<td colspan="2"></td>
							</tr>
						</table>
					<?
					}

					if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || count($session_admin_advanced) > 0)
					{
						$key++;
					?>
						<table width="150" border="0" cellspacing="0" cellpadding="0">
							<tr height="22">
								<td background="images/menu_bt.jpg" style="padding-left:30px"><a href="javascript:void(0)" onClick="expand(<?=$key?>)" class="menuParent">模块管理</a></td>
							</tr>
							<tr height="4">
								<td></td>
							</tr>
						</table>
						<table id="child<?=$key?>" width="150" border="0" cellspacing="0" cellpadding="0" style="DISPLAY:none">
							<?
							$sql = "select id, name, default_file from advanced where state=1 order by sortnum asc";
							$rst = $db->query($sql);
							while ($row = $db->fetch_array($rst))
							{
								if ($session_admin_grade == ADMIN_HIDDEN || $session_admin_grade == ADMIN_SYSTEM || hasInclude($session_admin_advanced, $row["id"]) == true)
								{
							?>
									<tr height="20">
										<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
										<td><a href="<?=$row["default_file"]?>" class="menuChild" target="main"><?=$row["name"]?></a></td>
									</tr>
							<?
								}
							}
							?>
							<tr height="4">
								<td colspan="2"></td>
							</tr>
						</table>
					<?
					}

					if ($session_admin_grade == ADMIN_HIDDEN) {
						$key++;
					?>
						<table width="150" border="0" cellspacing="0" cellpadding="0">
							<tr height="22">
								<td background="images/menu_bt.jpg" style="padding-left:30px"><a href="javascript:void(0)" onClick="expand(<?=$key?>)" class="menuParent">隐藏管理</a></td>
							</tr>
							<tr height="4">
								<td></td>
							</tr>
						</table>
						<table id="child<?=$key?>" width="150" border="0" cellspacing="0" cellpadding="0" style="DISPLAY:none">

							<?
							$sql = "select name, default_file from advanced where state=2 order by sortnum asc";
							$rst = $db->query($sql);
							while ($row = $db->fetch_array($rst)) {
							?>
                                <tr height="20">
                                    <td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
                                    <td><a href="<?=$row["default_file"]?>" class="menuChild" target="main"><?=$row["name"]?></a></td>
                                </tr>
							<?
							}
							?>

							<tr height="20">
								<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
								<td><a href="advanced_list.php" class="menuChild" target="main">高级功能管理</a></td>
							</tr>
							<tr height="20">
								<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
								<td><a href="product_class_list.php" class="menuChild" target="main">虚拟分类管理</a></td>
							</tr>
							<tr height="4">
								<td colspan="2"></td>
							</tr>
						</table>
					<?
					}
					?>
					<table width="150" border="0" cellspacing="0" cellpadding="0">
						<tr height="22">
							<td background="images/menu_bt.jpg" style="padding-left:30px"><a href="javascript:void(0)" onClick="expand(0)" class="menuParent">个人管理</a></td>
						</tr>
						<tr height="4">
							<td></td>
						</tr>
					</table>
					<table id="child0" width="150" border="0" cellspacing="0" cellpadding="0" style="DISPLAY:none">
						<tr height="20">
							<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
							<td><a href="admin_changepass.php" class="menuChild" target="main">修改口令</a></td>
						</tr>
						<tr height="20">
							<td width="30" align="center"><img src="images/menu_icon.gif" width="9" height="9"></td>
							<td><a href="logout.php" class="menuChild" target="_top" onClick="if (confirm('确定要退出吗？')) return true; else return false;">退出系统</a></td>
						</tr>
					</table>
				</td>
				<td bgcolor="#D1E6F7" width="1"></td>
			</tr>
		</table>
	</body>
</html>
