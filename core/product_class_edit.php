<?
require_once "init.php";
require_once "isadmin.php";
require_once "config.php";
require_once "uploadImg.php";

$parentId   = trim($_GET["parentId"]);
$id         = trim($_GET["id"]);

if (strlen($id) % 3 != 0) {
    info("指定了错误的分类ID号！");
}

if ($parentId) {

    if(strlen($parentId) % 3 != 0){
        info("指定的父分类不正确！");
    }

    if(!$parent = $db->getByWhere("product_class", "id='$parentId'")){
        info("指定的父分类不存在！");
    }
}

$listUrl = "product_class_list.php?parentId=$parentId";

$data =  array();
if (empty($id)) {
    $data['id']         = $db->getMax("product_class", "id", "id like '" . $parentId . CLASS_SPACE . "'");
    $data['id']         = empty($data['id']) ? $parentId . CLASS_DEFAULT : $data['id'] + 1;
    $data['sortnum']    = $db->getMax("product_class", "sortnum", "id like '" . $parentId . CLASS_SPACE . "'") + 10;
    $data['state']      = 1;
    $data['isTop']      = 1;
    $data['isBlank']    = 0;
    //$data['has_sub']    = 1;
} else {
    if(!$data = $db->getByWhere('product_class', "id='$id'")){
        info("指定的记录不存在");
    }
}

//提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data                   = $_POST["data"];
    $data['id']             = (int)$data['id'];
    $data['sortnum']        = (int)$data['sortnum'];
    $data['name']           = filterHtml($data['name']);
    $data['menu']           = filterHtml($data['menu']);
    $data['title']          = filterHtml($data['title']);
    $data['url']            = filterHtml($data['url']);
    $data['isBlank']        = (int)$data['isBlank'];
    $data['seoTitle']       = filterHtml($data['seoTitle']);
    $data['keywords']       = filterHtml($data['keywords']);
    $data['description']    = filterHtml($data['description']);
    $data['isTop']          = (int)$data['isTop'];
    $data['state']          = (int)$data['state'];
    $data['content']        = replaceUpload(filterHtml($data['content']));

    /**
     * 图片上传、删除处理
     * 一定要先处理删除，再处理上传
     */
    //处理图标
    //删除图标
    if ($_POST['deleteIcon']) {
        if ($data['icon']) deleteFile($data['icon']);
        $data['icon'] = '';
    }
    if (isset($_FILES["icon"])) {
        $icon = $_FILES["icon"];
        $icon = uploadImg($icon, "jpg,jpeg,png,gif,swf");
        if (!empty($icon)) {
            //删除原图标
            if ($data['icon']) deleteFile($data['icon']);
            $data['icon'] = $icon;
        }
    }

    //处理图片
    //删除图片
    if ($_POST['deletePic']) {
        if ($data['pic']) deleteFile($data['pic']);
        $data['pic'] = '';
    }
    if (isset($_FILES["pic"])) {
        $pic = $_FILES["pic"];
        $pic = uploadImg($pic, "jpg,jpeg,png,gif,swf");
        if (!empty($pic)) {
            //删除原图片
            if ($data['pic']) deleteFile($data['pic']);
            $data['pic'] = $pic;
        }
    }

    if (empty($data['id']) || empty($data['name'])) {
        info("填写的参数不完整！");
    }

    if (empty($id)) {
        if (strlen($data['id']) % 3 != 0) {
            info("填写的分类ID号错误！");
        }

        //检查分类ID是否存在
        if ($db->getCount("product_class", "id=".$data['id']) > 0) {
            $data['id'] = $db->getMax("product_class", "id", "id like '" . $parentId . CLASS_SPACE . "'");
            $data['id'] = empty($data['id']) ? $parentId . CLASS_DEFAULT : $data['id'] + 1;
        }

        if ($db->add("product_class", $data)) {
            header("location: $listUrl");
        } else {
            info("添加分类失败！");
        }
    } else {
        if(strlen($id) >= 3){
            //若存在子分类，则这个分类是否有子类应该是允许的
            if (empty($data['has_sub']) && $db->getCount("product_class", "id like '" . $id . CLASS_SPACE . "'") > 0){
                info("请先删除该栏目下属子分类！");
            }
        }

        if ($db->update("product_class", $data, "id='$id'")) {
            header("location: $listUrl");
        } else {
            info("编辑分类失败！");
        }
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
            K.create('#content', {});
        });
    </script>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr class="position">
            <td class="position">当前位置: 管理中心 -&gt; 产品分类管理</td>
        </tr>
    </table>

    <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr height="30">
            <td><a href="<?=$listUrl?>">[返回列表]</a></td>
        </tr>
    </table>

    <form name="form1" action="" method="post" enctype="multipart/form-data">
        <table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
            <tr class="editHeaderTr">
                <td class="editHeaderTd" colSpan="2">产品分类管理</td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">ID号</td>
                <td class="editRightTd"><input type="text" name="data[id]" id="id" value="<?=$data['id']?>" size="10" maxlength="20" required></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">序号</td>
                <td class="editRightTd"><input type="text" name="data[sortnum]" id="sortnum" value="<?=$data['sortnum']?>" size="10" maxlength="5" required></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">分类名称</td>
                <td class="editRightTd"><input type="text" name="data[name]" id="name" value="<?=$data['name']?>" maxlength="50" size="30" required></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">别名</td>
                <td class="editRightTd"><input type="text" name="data[menu]" id="menu" value="<?=$data['menu'] ?>" size="50"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">副标题</td>
                <td class="editRightTd"><input type="text" name="data[title]" id="title" value="<?=$data['title']?>" size="50"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">链接地址</td>
                <td class="editRightTd">
                    <input type="text" name="data[url]" id="url" value="<?=$data['url']?>" size="50">
                    <input type="checkbox" name="data[isBlank]" id="isBlank" value="1" <?if ($data['isBlank'] == 1) echo "checked" ?>> 是否新窗口打开
                </td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">SEO标题</td>
                <td class="editRightTd"><input type="text" name="data[seoTitle]" id="seoTitle" value="<?=$data['seoTitle']?>" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">关键字</td>
                <td class="editRightTd"><input type="text" name="data[keywords]" id="keywords" value="<?=$data['keywords']?>" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">描述</td>
                <td class="editRightTd"><input type="text" name="data[description]" id="description" value="<?=$data['description']?>" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">是否显示</td>
                <td class="editRightTd">
                    <input type="radio" name="data[isTop]" value="1"<? if ($data['isTop'] == 1) echo " checked" ?>> 显示
                    <input type="radio" name="data[isTop]" value="0"<? if ($data['isTop'] == 0) echo " checked" ?>> 隐藏
                </td>
            </tr>

            <tr class="editTr">
                <td class="editLeftTd">是否允许删除</td>
                <td class="editRightTd">
                    <input type="radio" name="data[state]" value="1"<? if ($data['state'] == 1) echo " checked" ?>>允许
                    <input type="radio" name="data[state]" value="0"<? if ($data['state'] == 0) echo " checked" ?>>拒绝
                </td>
            </tr>

            <tr class="editTr">
                <td class="editLeftTd">子类管理</td>
                <td class="editRightTd">
                    <input type="radio" name="data[has_sub]" value="1"<? if ($data['has_sub'] == 1) echo " checked"?>>是
                    <input type="radio" name="data[has_sub]" value="0"<? if ($data['has_sub'] == 0) echo " checked"?>>否
                </td>
            </tr>

            <tr class="editTr">
                <td class="editLeftTd">图标</td>
                <td class="editRightTd">
                    <input type="file" name="icon" size="40">
                    <?
                    if (!empty($data['icon'])){
                        ?>
                        <span><a href="<?= PATH . UPLOAD_PATH . $data['icon'] ?>" target="_blank">查看图标</a></span>
                        <input type="checkbox" name="deleteIcon" id="deleteIcon" value="1">
                        <label for="deleteIcon">删除图标</label>
                        <?
                    }
                    ?>
                    <label class="red"> (文件必须是JPG、JPEG、PNG、GIF、SWF格式)</label>
                </td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">图片</td>
                <td class="editRightTd">
                    <input type="file" name="pic" size="40">
                    <?
                    if (!empty($data['pic'])){
                        ?>
                        <span><a href="<?= PATH . UPLOAD_PATH . $data['pic'] ?>" target="_blank">查看图片</a></span>
                        <input type="checkbox" name="deletePic" id="deletePic" value="1">
                        <label for="deletePic">删除图片</label>
                        <?
                    }
                    ?>
                    <label class="red"> (文件必须是JPG、JPEG、PNG、GIF、SWF格式)</label>
                </td>
            </tr>

            <tr class="editTr">
                <td class="editLeftTd">内容</td>
                <td class="editRightTd" style="padding:5px;">
                    <textarea class="textarea" id="content" name="data[content]"><?=replaceUploadBack($data['content'])?></textarea>
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
    <script type="text/javascript">document.getElementById('name').focus();</script>
</body>
</html>
