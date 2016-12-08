<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");
require(dirname(__FILE__) . "/uploadImg.php");

//高级管理权限
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && hasInclude($session_admin_advanced, CONFIG_ADVANCEDID) == false) {
	info("没有权限！");
}

if(!$config = $db->getByWhere('config_base', "id=1")){
	info("指定的记录不存在");
}

//提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data = $_POST["data"];
    $data['name']           = filterHtml($data['name']);
    $data['tel']            = filterHtml($data['tel']);
    $data['phone']          = filterHtml($data['phone']);
    $data['userName']       = filterHtml($data['userName']);
    $data['password']      	= filterHtml($data['password']);
    $data['host']     		= filterHtml($data['host']);
    $data['port']			= filterHtml($data['port']);
    $data['icp']            = filterHtml($data['icp']);
    $data['title']          = filterHtml($data['title']);
    $data['keywords']       = filterHtml($data['keywords']);
    $data['description']    = filterHtml($data['description']);
    $data['content']        = replaceUpload($data['content']);
    $data['contact']        = replaceUpload($data['contact']);
    $data['copyright']      = replaceUpload($data['copyright']);
    $data['javascript'] 	= trim($data["javascript"]);

    if (empty($data['name'])) {
        info("请填写网站名称！");
    }

    /**
     * 图片上传、删除处理
	 * 一定要先处理删除，再处理上传
     */
	//删除Logo
	if ($_POST['deleteLogo']) {
		if ($config['logo']) deleteFile($config['logo']);
		$data['logo'] = '';
	}
    //上传LOGO
    if (isset($_FILES["logo"])) {
        $logo = $_FILES["logo"];
        $logo = uploadImg($logo, "jpg,jpeg,png,gif,swf");
        if (!empty($logo)) {
            //删除原图片
            if ($config['logo']) deleteFile($config['logo']);
            $data['logo'] = $logo;
        }
    }

	//删除Logo
	if ($_POST['deleteWechat']) {
		if ($config['wechat']) deleteFile($config['wechat']);
		$data['wechat'] = '';
	}
    //上传Wechat
    if (isset($_FILES["wechat"])) {
        $wechat = $_FILES["wechat"];
        $wechat = uploadImg($wechat, "jpg,jpeg,png,gif");
        if (!empty($wechat)) {
            //删除原图片
            if ($config['wechat']) deleteFile($config['wechat']);
            $data['wechat'] = $wechat;
        }
    }

    if ($db->update('config_base', $data, "id=1")) {
        info("保存成功");
    } else {
        info("保存失败");
    }
}
?>


<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="images/admin.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="images/common.js"></script>
		<script charset="utf-8" src="kindeditor/kindeditor.js"></script>
		<script>
			KindEditor.ready(function(K) {
				K.create('#content', {
                    items: _items
				});
				K.create('#contact', {
                    items: _items
				});
				K.create('#copyright', {
                    items: _items
				});
			});
		</script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心  -&gt; 高级管理 -&gt; 基本设置</td>
			</tr>
		</table>
		<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="20">
				<td></td>
			</tr>
		</table>
		<form name="form1" method="post" onSubmit="return check(this);" enctype="multipart/form-data">
			<table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
				<tr class="editHeaderTr">
					<td class="editHeaderTd" colSpan="2">基本设置</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">公司名称</td>
					<td class="editRightTd">
						<input type="text" name="data[name]" id="name" value="<?=$config['name']?>" size="50" maxlength="100" required>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">Logo</td>
					<td class="editRightTd">
						<input type="file" name="logo" size="40">
						<?
						if (!empty($config['logo'])){
						?>
                            <span><a href="<?= PATH . UPLOAD_PATH . $config['logo'] ?>" target="_blank">查看图片</a></span>
                            <input type="checkbox" name="deleteLogo" id="deleteLogo" value="1">
                            <label for="deleteLogo">删除图片</label>
						<?
						}
						?>
                        <label class="red"> (文件必须是JPG、JPEG、PNG、GIF、SWF格式)</label>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">Wechat</td>
					<td class="editRightTd">
						<input type="file" name="wechat" size="40">
						<?
						if (!empty($config['wechat'])){
						?>
                            <span><a href="<?= PATH . UPLOAD_PATH . $config['wechat'] ?>" target="_blank">查看图片</a></span>
                            <input type="checkbox" name="deleteWechat" id="deleteWechat" value="1">
                            <label for="deleteWechat">删除图片</label>
						<?
						}
						?>
                        <label class="red"> (文件必须是JPG、JPEG、PNG、GIF)</label>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">服务热线</td>
					<td class="editRightTd">
						<input type="text" name="data[tel]" value="<?=$config['tel']?>" size="50" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">手机</td>
					<td class="editRightTd">
						<input type="text" name="data[phone]" value="<?=$config['phone']?>" size="50" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">邮箱地址</td>
					<td class="editRightTd">
						<input type="text" name="data[userName]" value="<?=$config['userName']?>" size="50" autoComplete="off" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">邮箱密码</td>
					<td class="editRightTd">
						<input type="password" name="data[password]" value="<?=$config['password']?>" autoComplete="off" size="50" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">SMTP服务器</td>
					<td class="editRightTd">
						<input type="text" name="data[host]" value="<?=$config['host']?>" size="50" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">SMTP服务器端口</td>
					<td class="editRightTd">
						<input type="text" name="data[port]" value="<?=$config['port']?>" size="50" maxlength="100">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">ICP备案号</td>
					<td class="editRightTd">
						<input type="text" name="data[icp]" value="<?=$config['icp']?>" size="30" maxlength="30">
					</td>
				</tr>
                <tr class="editTr">
                    <td class="editLeftTd">网站标题</td>
                    <td class="editRightTd">
                        <input type="text" name="data[title]" value="<?=$config['title']?>" size="50" maxlength="100">
                    </td>
                </tr>
				<tr class="editTr">
					<td class="editLeftTd">网站关键字</td>
					<td class="editRightTd" style="padding:10px;">
						<input type="text" name="data[keywords]" value="<?=$config['keywords']?>" size="100" maxlength="200">
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">网站描述</td>
					<td class="editRightTd" style="padding:10px;">
						<input type="text" name="data[description]" value="<?=$config['description']?>" size="100" maxlength="200">
					</td>
				</tr>
                <tr class="editTr">
                    <td class="editLeftTd">其它信息</td>
                    <td class="editRightTd" style="padding:10px;">
                        <textarea class="textareaSmall" id="content" name="data[content]"><?=replaceUploadBack($config['content'])?></textarea>
                    </td>
                </tr>
				<tr class="editTr">
					<td class="editLeftTd">联系我们</td>
					<td class="editRightTd" style="padding:10px;">
						<textarea class="textareaSmall" id="contact" name="data[contact]"><?=replaceUploadBack($config['contact'])?></textarea>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">版权信息</td>
					<td class="editRightTd" style="padding:10px;">
						<textarea class="textareaSmall" id="copyright" name="data[copyright]"><?=replaceUploadBack($config['copyright'])?></textarea>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">Javascript 代码</td>
					<td class="editRightTd" style="padding:10px;">
						<textarea style="width: 100%" name="data[javascript]" cols="105" rows="8"><?=$config['javascript']?></textarea>
					</td>
				</tr>
				<tr class="editTr">
					<td class="editLeftTd">备注</td>
					<td class="editRightTd">
						请确保Javascript代码的安全性，防止可能引用错误甚至恶意的代码，造成网站瘫痪和数据丢失。
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
