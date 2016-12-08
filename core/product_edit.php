<?
require_once "init.php";
require_once "isadmin.php";
require_once "config.php";
require_once "uploadImg.php";

$class_id       = trim($_GET["class_id"]);
$select_class   = empty($_GET["select_class"]) ? $class_id : trim($_GET["select_class"]);
$page           = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;
$select_state   = (int)$_GET["select_state"];
$keyword        = urlencode(trim($_GET["keyword"]));
$id             = (int)$_GET["id"];

if (empty($class_id) || !checkClassID($class_id, 2)) {
    info("指定了错误的分类！");
}

if (strlen($select_class) % CLASS_LENGTH != 0 && !checkClassID($select_class, strlen($select_class) / CLASS_LENGTH)) {
    info("选择了错误的分类！");
}

//权限检查
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && $session_admin_grade != ADMIN_ADVANCED && hasInclude($session_admin_popedom, substr($class_id, 0, CLASS_LENGTH)) != true && hasInclude($session_admin_popedom, $class_id) != true) {
    info("没有权限！");
}

$listUrl = "product_list.php?class_id=$class_id&select_class=$select_class&select_state=$select_state&keyword=$keyword&page=$page";
$editUrl = "product_edit.php?class_id=$class_id&select_class=$select_class&select_state=$select_state&keyword=$keyword&page=$page&id=$id";

//查询分类属性设置
if(!$category = $db->getByWhere('product_class', "id='$class_id'")){
    info("指定的记录不存在");
} else {
    $category['hasSelect'] = $db->getField("product_class", "has_sub", "id='$class_id'");
}

$data =  array();
if ($id < 1) {
    $data['sortnum']        = $db->getMax("product", "sortnum", "class_id like '$class_id%'") + 10;
    $data['select_id']      = $select_class;
    $data['state']          = 1;
    $data['createdTime']    = time();
} else {
    if(!$data = $db->getByWhere('product', "id=$id")){
        info("指定的记录不存在");
    }
}

//提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data                   = $_POST["data"];
    $data['sortnum']        = (int)$data['sortnum'];
    $data['state']          = (int)$data['state'];
    //权限 普通管理员只能发表未审核的信息
    if ($session_admin_grade == ADMIN_COMMON) {
        $data['state']      = 0;
    }
    if($category['hasReview']){
        $data['review']     = (int)$data['review'];
    }
    $data['diy']            = (int)$data['diy'];
    $data['createdTime']    = strtotime($data['createdTime']);
    $data['title']          = filterHtml($data['title']);
    $data['title2']         = filterHtml($data['title2']);
    $data['seoTitle']       = filterHtml($data['seoTitle']);
    $data['keywords']       = filterHtml($data['keywords']);
    $data['description']    = filterHtml($data['description']);
    if($category['hasSelect']){
        $data['class_id'] = (int)$data['class_id'];
    } else {
        $data['class_id'] = $class_id;
    }
    if($category['hasAuthor']){
        $data['author']     = filterHtml($data['author']);
    }
    if($category['hasSource']){
        $data['source']     = filterHtml($data['source']);
    }
    if($category['hasWebsite']){
        $data['website']    = filterHtml($data['website']);
    }
    /**
     * 图片上传、删除处理
     * 一定要先处理删除，再处理上传
     */
    //缩略图
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
    //大图
    //删除图片
    if ($_POST['deletePic2']) {
        if ($data['pic2']) deleteFile($data['pic2']);
        $data['pic2'] = '';
    }
    if (isset($_FILES["pic2"])) {
        $pic2 = $_FILES["pic2"];
        $pic2 = uploadImg($pic2, "jpg,jpeg,png,gif,swf");
        if (!empty($pic2)) {
            //删除原图片
            if ($data['pic2']) deleteFile($data['pic2']);
            $data['pic2'] = $pic2;
        }
    }
    //附件
    //删除图片
    if ($_POST['deleteAnnex']) {
        if ($data['annex']) deleteFile($data['annex']);
        $data['annex'] = '';
    }
    if (isset($_FILES["annex"])) {
        $annex = $_FILES["annex"];
        $annex = uploadImg($annex, "gif,jpg,png,jpeg,pdf,doc,xls,ppt,rar,zip,flv,mp4");
        if (!empty($pic2)) {
            //删除原文件
            if ($data['annex']) deleteFile($data['annex']);
            $data['annex'] = $annex;
        }
    }
    $data['tags']      = filterHtml($data['tags']);
    $data['price']      = filterHtml($data['price']);
    if($category['hasSpc']){
        $data['spc']        = filterHtml($data['spc']);
    }
    if($category['hasViews']){
        $data['views']      = filterHtml($data['views']);
    }
    $data['intro']          = replaceUpload(filterHtml($data['intro']));
    $data['content']        = replaceUpload($data['content']);
    $data['content2']       = replaceUpload(filterHtml($data['content2']));
    $data['content3']       = replaceUpload(filterHtml($data['content3']));
    $data['content4']       = replaceUpload(filterHtml($data['content4']));

    if (empty($data['title']) || empty($data['class_id'])) {
        info("请填写标题和选择所属分类！");
    }

    if ($id < 1) {
        $data['sortnum'] = $db->getMax("product", "sortnum", "class_id='".$data['class_id']."'") + 10;

        if ($db->add("product", $data)) {
            header("location: $listUrl");
        } else {
            info("添加信息失败！");
        }
    } else {
        //权限 普通管理员只能修改自己发表但未审核的信息
        if ($session_admin_grade == ADMIN_COMMON && ($db->getField("product", "state", "where id=$id") == 1 || $db->getField("product", "admin_id", "where id=$id") != $session_admin_id)) {
            info("没有权限！");
        }

        if ($db->update("product", $data, "id=$id")) {
            header("location: $listUrl");
        } else {
            info("修改信息失败！");
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
    <script charset="utf-8" src="kindeditor/kindeditor-min.js"></script>
    <script>
        KindEditor.ready(function (K) {
            K.create('#intro', {
                items: _items
            });
            K.create('#content', {
                allowFileManager: true
            });
            K.create('#content2', {
                allowFileManager: true
            });
            K.create('#content3', {
                allowFileManager: true
            });
            K.create('#content4', {
                allowFileManager: true
            });
        });
    </script>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr class="position">
            <td class="position">当前位置: 管理中心
                -&gt; <?= $db->getField("product_class", "name", "id='$class_id'") ?> -&gt; 新增/编辑
            </td>
        </tr>
    </table>

    <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr height="30">
            <td>
                <a href="<?= $listUrl ?>">[返回列表]</a>
            </td>
        </tr>
    </table>

    <form name="form1" action="" method="post" enctype="multipart/form-data">
        <table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
            <tr class="editHeaderTr">
                <td class="editHeaderTd" colSpan="2">新增/编辑信息</td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">序号</td>
                <td class="editRightTd"><input type="text" name="data[sortnum]" id="sortnum" value="<?=$data['sortnum']?>" size="10" maxlength="5" required></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">状态</td>
                <td class="editRightTd">
                    <select name="data[state]" style="width:80px;">
                        <option value="0"<? if ($data['state'] == 0) echo "selected"; ?>>未审核</option>
                        <option value="1"<? if ($data['state'] == 1) echo "selected"; ?>>正常</option>
                        <option value="2"<? if ($data['state'] == 2) echo "selected"; ?>>置顶</option>
                        <option value="3"<? if ($data['state'] == 3) echo "selected"; ?>>总置顶</option>
                    </select>
                </td>
            </tr>

            <?
            if ($category['hasReview']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">是否允许评论</td>
                    <td class="editRightTd">
                        <input type="radio" name="data[review]" value="1"<? if ($data['review'] == 1) echo " checked"?>>允许
                        <input type="radio" name="data[review]" value="0"<? if ($data['review'] == 0) echo " checked"?>>拒绝
                    </td>
                </tr>
            <?
            }
            ?>

            <tr class="editTr">
                <td class="editLeftTd">自选配置</td>
                <td class="editRightTd">
                    <input type="checkbox" name="data[diy]" id="diy" value="1" <?if ($data['diy'] == 1) echo "checked" ?>> 是
                </td>
            </tr>

            <tr class="editTr">
                <td class="editLeftTd">上架时间</td>
                <td class="editRightTd">
                    <input type="text" name="data[createdTime]" value="<?= date("Y-m-d H:i:s", $data['createdTime']) ?>" maxlength="20" size="24"> 时间格式为2013-10-01 00:00:00
                </td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">标题</td>
                <td class="editRightTd"><input type="text" value="<?= $data['title'] ?>" name="data[title]" id="title" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">副标题</td>
                <td class="editRightTd"><input type="text" value="<?= $data['title2'] ?>" name="data[title2]" size="100"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">Tags</td>
                <td class="editRightTd">
                    <input type="text" value="<?= $data['tags'] ?>" name="data[tags]" maxlength="50" size="46">
                </td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">SEO标题</td>
                <td class="editRightTd"><input type="text" name="data[seoTitle]" value="<?= $data['seoTitle'] ?>" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">关键字</td>
                <td class="editRightTd"><input type="text" name="data[keywords]" value="<?= $data['keywords'] ?>" size="80"></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">描述</td>
                <td class="editRightTd"><input type="text" name="data[description]" value="<?= $data['description'] ?>" size="80"></td>
            </tr>

            <?
            if ($category['hasSelect']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">所属类别</td>
                    <td class="editRightTd">
                        <select name="data[class_id]" style="min-width: 260px;">
                            <?
                            $list = $db->getList("product_class", "id like '" . $class_id . "%'", "order by sortnum asc");
                            $list = getNodeData($list, $class_id, CLASS_LENGTH);
                            echo optionsTree($list, $data['class_id']);
                            ?>
                        </select>
                    </td>
                </tr>
            <?
            }
            if ($category['hasAuthor']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">作者</td>
                    <td class="editRightTd">
                        <input type="text" value="<?= $data['author'] ?>" name="data[author]" maxlength="50" size="30">
                    </td>
                </tr>
            <?
            }

            if ($category['hasSource']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">来源</td>
                    <td class="editRightTd">
                        <input type="text" value="<?= $data['source'] ?>" name="data[source]" maxlength="50" size="30">
                    </td>
                </tr>
            <?
            }

            if ($category['hasWebsite']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">链接网址</td>
                    <td class="editRightTd"><input type="text" value="<?= $data['website'] ?>" name="data[website]" maxlength="300" size="50"></td>
                </tr>
            <?
            }
            ?>

            <tr class="editTr">
                <td class="editLeftTd">缩略图</td>
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
                <td class="editLeftTd">大图</td>
                <td class="editRightTd">
                    <input type="file" name="pic2" size="40">
                    <?
                    if (!empty($data['pic2'])){
                        ?>
                        <span><a href="<?= PATH . UPLOAD_PATH . $data['pic2'] ?>" target="_blank">查看图片</a></span>
                        <input type="checkbox" name="deletePic2" id="deletePic2" value="1">
                        <label for="deletePic2">删除图片</label>
                        <?
                    }
                    ?>
                    <label class="red"> (文件必须是JPG、JPEG、PNG、GIF、SWF格式)</label>
                </td>
            </tr>

            <?
            if ($category['hasAnnex']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">附件</td>
                    <td class="editRightTd">
                        <input type="file" name="annex" size="40">
                        <?
                        if (!empty($data['annex'])){
                            ?>
                            <span><a href="<?= PATH . UPLOAD_PATH . $data['annex'] ?>" target="_blank">查看文件</a></span>
                            <input type="checkbox" name="deleteAnnex" id="deleteAnnex" value="1">
                            <label for="deleteAnnex">删除图片</label>
                            <?
                        }
                        ?>
                        <label class="red"> (文件必须是JPG、JPEG、PNG、GIF、SWF格式)</label>
                    </td>
                </tr>
            <?
            }
            ?>

            <tr class="editTr">
                <td class="editLeftTd">价格</td>
                <td class="editRightTd">
                    <input type="text" value="<?= $data['price'] ?>" name="data[price]" maxlength="50" size="46">
                </td>
            </tr>

            <?
            if ($category['hasSpc']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">商品编号</td>
                    <td class="editRightTd">
                        <input type="text" value="<?= $data['spc'] ?>" name="data[spc]" maxlength="50" size="20">
                    </td>
                </tr>
            <?
            }
            ?>

            <tr class="editTr">
                <td class="editLeftTd">浏览次数</td>
                <td class="editRightTd">
                    <input type="text" value="<?= $data['views'] ?>" name="data[views]" maxlength="50" size="30">
                </td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">简介</td>
                <td class="editRightTd"><textarea name="data[intro]" id="intro" class="textareaSmall"><?= $data['intro'] ?></textarea></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">产品介绍</td>
                <td class="editRightTd"><textarea name="data[content]" id="content" class="textarea"><?= $data['content'] ?></textarea></td>
            </tr>
            <tr class="editTr">
                <td class="editLeftTd">购买标配</td>
                <td class="editRightTd"><textarea name="data[content2]" id="content2" class="textarea"><?= $data['content2'] ?></textarea></td>
            </tr>

            <?
            if ($category['hasContent3']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">备注2</td>
                    <td class="editRightTd"><textarea name="data[content3]" id="content3" class="textarea"><?= $data['content3'] ?></textarea></td>
                </tr>
            <?
            }

            if ($category['hasContent4']) {
            ?>
                <tr class="editTr">
                    <td class="editLeftTd">备注3</td>
                    <td class="editRightTd"><textarea name="data[content4]" id="content4" class="textarea"><?= $data['content4'] ?></textarea></td>
                </tr>
            <?
            }
            ?>
            <tr class="editFooterTr">
                <td class="editFooterTd" colSpan="2">
                    <input type="submit" value=" 确 定 ">
                    <input type="reset" value=" 重 填 ">
                </td>
            </tr>
        </table>
    </form>
    <script type="text/javascript">document.getElementById('title').focus();</script>
</body>
</html>
