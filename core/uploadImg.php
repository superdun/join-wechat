<?
function uploadImg($imgfile, $exts, $path=false)
{
	if  ( $path ) {
		$File_UPLOAD_PATH = '../' . UPLOAD_PATH;
	} else {
		$File_UPLOAD_PATH = UPLOAD_PATH_FOR_ADMIN;
	}

	if ($imgfile["size"])
	{
		if ($imgfile["size"] > MAX_IMAGE_SIZE)
		{
			info("上传图片太大，超过" . formatSizeStr(MAX_IMAGE_SIZE) . "！");
		}

		if (!is_uploaded_file($imgfile["tmp_name"]))
		{
			info("上传图片错误！");
		}

		$ext = strToLower(getFileExt($imgfile["name"]));

		$extsArray = explode(",", $exts);
		if (!is_array($extsArray))
		{
			$extsArray = array($extsArray);
		}

		$hasExt = false;
		foreach ($extsArray as $extsValue)
		{
			if ($extsValue == $ext)
			{
				$hasExt = true;
				break;
			}
		}

		if ($hasExt == false)
		{
			info("上传文件必须是" . $exts . "格式！");
		}

		if (!is_dir($File_UPLOAD_PATH))
		{
			if (!mkdir($File_UPLOAD_PATH, 0777))
			{
				info("无法建立保存图片的目录！");
			}
		}

		$ym = date("Y-m");
		$File_UPLOAD_PATH .= $ym . "/";
		if (!is_dir($File_UPLOAD_PATH))
		{
			if (!mkdir($File_UPLOAD_PATH, 0777))
			{
				info("无法建立保存图片的目录！");
			}
		}

		$tmp_name	= getTmpName();
		$image		= $tmp_name . "." . $ext;
		if (!move_uploaded_file($imgfile["tmp_name"], $File_UPLOAD_PATH . $image))
		{
			info("保存图片失败！");
		}

		$image = $ym . "/" . $image;
	}
	else
	{
		$image = "";
	}

	return $image;
}
?>
