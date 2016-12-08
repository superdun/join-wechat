<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");


//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, MESSAGE_ADVANCEDID) == false)
{
    info("没有权限！");
}

$id		= (int)$_GET["id"];
$page	= (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
if ($id < 1)
{
    info("参数有误！");
}

$listUrl = "member_list.php?page=$page";

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $name	= htmlspecialchars(trim($_POST["name"]));
    $phone	= htmlspecialchars(trim($_POST["phone"]));
    $email	= htmlspecialchars(trim($_POST["email"]));
    $pass	= trim($_POST["pass"]);
    $pass2	= trim($_POST["pass2"]);
    $status		= (int)$_POST["status"];

    if( empty($name) ||  empty($email) ){
        info("帐号、邮箱必填！");
    }

    if (empty($id))
    {
        if ( empty($pass) || empty($pass2) || (!empty($pass) && strlen($pass) < 8) || (!empty($pass2) && strlen($pass2) < 8) ){
            info("登录密码不能为空或者少于8位！");
        } elseif ( $pass !== $pass2 ) {
            info("两次密码不一致！");
        }
        $ip = $_SERVER["REMOTE_ADDR"];
        $time = time();
        $id = $db->getMax("member", "id", "") + 1;
        $sql = "insert into member(id, name, password, email, phone, status, createdTime, last_login_time, last_login_ip) values ($id, '$name', '" . md5($pass) . "', '$email', '$phone', $status, $time, $time, '$ip')";
    }
    else
    {
        if ( empty($pass) && empty($pass2) ) {
            $sql = "update member set name='$name', email='$email', phone='$phone', status=$status where id='$id'";
        } else {
            if ( (!empty($pass) && strlen($pass) < 8) || (!empty($pass2) && strlen($pass2) < 8) ){
                info("登录密码不能为空或者少于8位！");
            } elseif ( $pass !== $pass2 ) {
                info("两次密码不一致！");
            }
            $sql = "update member set name='$name', password='" . md5($pass) . "', email='$email', phone='$phone', status=$status where id='$id'";
        }
    }
    $rst = $db->query($sql);
    $db->close();
    header("Location: $listUrl");
} else {
    if ($id >0 )
    {
        $sql = "select * from member where id='$id'";
        $rst = $db->query($sql);
        if ($row = $db->fetch_array($rst))
        {
            $id			= $row["id"];
            $name	= $row["name"];
            $phone		= $row["phone"];
            $email		= $row["email"];
            $status		= $row["status"];
        }
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
		<script type="text/javascript" src="images/jquery-1.8.2.min.js"></script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
                <td class="position">当前位置: 管理中心 -&gt; 高级管理 -&gt; 会员管理</td>
			</tr>
		</table>
        <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr height="30">
                <td>
                    <a href="<?=$listUrl?>">[返回列表]</a>&nbsp;
                </td>
            </tr>
        </table>
        <form name="form1" action="" method="post">
            <table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
                <tr class="editHeaderTr">
                    <td class="editHeaderTd" colSpan="2">会员资料</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">帐号</td>
                    <td class="editRightTd"><input type="text" name="name" value="<?=$name?>" maxlength="50" size="30" readonly></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">密码</td>
                    <td class="editRightTd"><input type="password" name="pass" maxlength="50" size="30"> 密码不修改请该选项不填写</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">确认密码</td>
                    <td class="editRightTd"><input type="password" name="pass2" maxlength="50" size="30"> 密码不修改请该选项不填写</td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">手机</td>
                    <td class="editRightTd"><input type="text" name="phone" value="<?=$phone?>" maxlength="50" size="30"></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">邮箱</td>
                    <td class="editRightTd"><input type="text" name="email" value="<?=$email?>" maxlength="50" size="30"></td>
                </tr>
                <tr class="editTr">
                    <td class="editLeftTd">状态</td>
                    <td class="editRightTd">
                        <input type="radio" name="status" value="0"<? if ($status == 0) echo " checked"?>>禁用
                        <input type="radio" name="status" value="1"<? if ($status == 1) echo " checked"?>>正常
                    </td>
                </tr>
                <tr class="editFooterTr">
                    <td class="editFooterTd" colSpan="2">
                        <input type="submit" value=" 确 定 ">
                        <input type="reset" value=" 重 填 ">
                    </td>
                </tr>
            </table>
        </form>
	</body>
</html>
