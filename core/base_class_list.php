<?
require_once "init.php";
require_once "isadmin.php";
require_once "config.php";

$id	= trim($_GET["id"]);

$listUrl = "base_class_list.php";
$editUrl = "base_class_edit.php";

//删除
if (!empty($id)) {
	//是否有分类
	if ($db->getCount("info_class", "id like '" . $id . CLASS_SPACE . "'") > 0) {
		info("分类下有子类，请先删除子类！");
	}
	
	//是否有信息
	if ($db->getCount("info", "class_id='$id'") > 0) {
		info("分类下有信息，请先删除信息！");
	}

    //删除分类
    if(!$data = $db->getByWhere("info_class", "id='$id'")){
        info("指定的分类不存在！");
    }
    if($db->delete("info_class", "id='$id'")){
        //删除图片、附件
        deleteFile($data['pic'], 1);
        deleteFiles($data['files'], 2);
        //echo "<script type='text/javascript'>window.top.frames('menu').window.location='menu.php?menu_id=$id';window.location='" . $listUrl . "';</script>";
    } else {
        info("删除分类失败！");
    }
}
?>

<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="images/admin.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="images/common.js"></script>
	</head>
	<body>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="position">
				<td class="position">当前位置: 管理中心 -&gt; 一级分类管理</td>
			</tr>
		</table>
		<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr height="30">
				<td>
					<a href="<?=$listUrl?>">[刷新列表]</a>&nbsp;
					<a href="<?=$editUrl?>">[增加]</a>&nbsp;
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="listTable">
			<tr class="listHeaderTr">
				<td width="10%">ID号</td>
                <td width="10%">序号</td>
				<td>分类名称</td>
				<td>别名</td>
				<td>链接地址</td>
				<td>图标</td>
				<td>图片</td>
                <td width="8%">显示状态</td>
				<td width="8%">删除</td>
			</tr>
			<?
            $list = $db->getList("info_class", "id like '" . CLASS_SPACE . "'", "order by sortnum asc");
            foreach($list as $key=>$val){
                $css = ($css == "listTr") ? "listAlternatingTr" : "listTr";
			?>
				<tr class="<?=$css?>">
					<td><?=$val["id"]?></td>
                    <td><?=$val["sortnum"]?></td>
					<td><a href="<?=$editUrl?>?id=<?=$val["id"]?>"><?=$val["name"]?></a></td>
                    <td><?=$val["menu"]?></td>
                    <td><?=$val["url"]?></td>
                    <td>
                        <?
                        if (!empty($val["icon"])) {
                            ?>
                            <a href="<?=UPLOAD_PATH_FOR_ADMIN . $val["icon"]?>" target="_blank">图标</a>
                            <?
                        } else {
                            echo "无";
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if (!empty($val["pic"])) {
                            ?>
                            <a href="<?=UPLOAD_PATH_FOR_ADMIN . $val["pic"]?>" target="_blank">图片</a>
                            <?
                        } else {
                            echo "无";
                        }
                        ?>
                    </td>
                    <td><?=($val["isTop"] == 1) ? "显示": "<font color='#FF6600'>隐藏</font>"?></td>
					<td><a href="<?=$listUrl?>?id=<?=$val["id"]?>" onClick="return del();">删除</a></td>
				</tr>
			<?
			}
			?>
			<tr class="listFooterTr">
				<td colspan="10"></td>
			</tr>
		</table>
	</body>
</html>
