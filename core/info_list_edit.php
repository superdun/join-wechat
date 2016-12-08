<?
require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");
require(dirname(__FILE__) . "/uploadImg.php");


$class_id = trim($_GET["class_id"]);
$info_id = trim($_GET["info_id"]);
$id = trim($_GET["id"]);
$page = (int)$_GET["page"] > 0 ? (int)$_GET["page"] : 1;

if (empty($info_id)) {
    info("指定的信息ID号无效！");
}

//权限检查
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && $session_admin_grade != ADMIN_ADVANCED && hasInclude($session_admin_popedom, substr($class_id, 0, CLASS_LENGTH)) != true && hasInclude($session_admin_popedom, $class_id) != true) {
    info("没有权限！");
}

$listUrl = "info_list_list.php?class_id=$class_id&info_id=$info_id&page=$page";
$editUrl = "info_list_edit.php?class_id=$class_id&info_id=$info_id&page=$page&id=$id";


//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);



$sql = "select * from info where id='$info_id'";
$rst = $db->query($sql);
if (!$row = $db->fetch_array($rst)) {
    $db->close();
    info("指定的信息ID号无效！");
}


//提交表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sortnum = (int)$_POST["sortnum"];
    $state = (int)$_POST["state"];
    $title = htmlspecialchars(trim($_POST["title"]));
    $title2 = htmlspecialchars(trim($_POST["title2"]));
    $website = trim($_POST["website"]);

    $pic_file = &$_FILES["pic"];
    $pic = uploadImg($pic_file, "gif,jpg,jpeg,png,swf,fla,wmv");            //上传图片
    $del_pic = (int)$_POST["del_pic"];

    $pic_file2 = &$_FILES["pic2"];
    $pic2 = uploadImg($pic_file2, "gif,jpg,jpeg,png,swf,fla,wmv");            //上传图片
    $del_pic2 = (int)$_POST["del_pic2"];

    $annex_file = &$_FILES["annex"];
    $annex = uploadImg($annex_file, "gif,jpg,png,jpeg,pdf,doc,xls,ppt,rar,zip,flv");    //上传附件
    $del_annex = (int)$_POST["del_annex"];

    $content = $_POST["content"];
    $files = $_POST["content_files"];

    $create_time = formatDate("Y-m-d H:i:s", $_POST["create_time"]);
    $now = date("Y-m-d H:i:s");

    if (empty($title)) {
        $db->close();
        info("填写的参数错误！");
    }

    if ($id < 1) {
        $aid = $db->getMax("info_list", "id", "") + 1;
        $sortnum = $db->getMax("info_list", "sortnum", "info_id='$info_id'") + 10;

        $sql = "insert into info_list(id, sortnum, title, title2, admin_id, info_id, pic, pic2, website, annex, intro, content, files, create_time, modify_time, state) values(" . $aid . ", $sortnum, '$title', '$title2', $session_admin_id, '$info_id', '$pic', '$pic2', '$website', '$annex', '$intro', '$content', '$files', '$create_time', '$now', $state)";
    } else {
        //权限 普通管理员只能修改自己发表但未审核的信息
        if ($session_admin_grade == ADMIN_COMMON && ($db->getTableFieldValue("info_list", "state", "where id=$id") == 1 || $db->getTableFieldValue("info_list", "admin_id", "where id=$id") != $session_admin_id)) {
            info("没有权限！");
        }

        if ((!empty($pic) || $del_pic == 1) && (!empty($pic2) || $del_pic2 == 1) && (!empty($annex) || $del_annex == 1) ) {
            $oldPic = $db->getTableFieldValue("info_list", "pic", "where id=$id");
            $oldPic2 = $db->getTableFieldValue("info_list", "pic2", "where id=$id");
            $oldAnnex = $db->getTableFieldValue("info_list", "annex", "where id=$id");
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic='$pic', pic2='$pic2', annex='$annex', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if ( (!empty($pic) || $del_pic == 1) && (!empty($annex) || $del_annex == 1) ){
            $oldPic = $db->getTableFieldValue("info_list", "pic", "where id=$id");
            $oldAnnex = $db->getTableFieldValue("info_list", "annex", "where id=$id");
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic='$pic', annex='$annex', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if ( (!empty($pic2) || $del_pic2 == 1) && (!empty($annex) || $del_annex == 1) ){
            $oldPic2 = $db->getTableFieldValue("info_list", "pic2", "where id=$id");
            $oldAnnex = $db->getTableFieldValue("info_list", "annex", "where id=$id");
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic2='$pic2', annex='$annex', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if ( (!empty($pic) || $del_pic == 1) && (!empty($pic2) || $del_pic2 == 1) ){
            $oldPic = $db->getTableFieldValue("info_list", "pic", "where id=$id");
            $oldPic2 = $db->getTableFieldValue("info_list", "pic2", "where id=$id");
            $oldAnnex = "";
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic='$pic', pic2='$pic2', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if (!empty($pic) || $del_pic == 1) {
            $oldPic = $db->getTableFieldValue("info_list", "pic", "where id=$id");
            $oldAnnex = "";
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic='$pic', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if (!empty($pic2) || $del_pic2 == 1) {
            $oldPic2 = $db->getTableFieldValue("info_list", "pic2", "where id=$id");
            $oldAnnex = "";
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', pic2='$pic2', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else if (!empty($annex) || $del_annex == 1) {
            $oldPic = "";
            $oldPic2 = "";
            $oldAnnex = $db->getTableFieldValue("info_list", "annex", "where id=$id");
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', annex='$annex', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        } else {
            $sql = "update info_list set sortnum=$sortnum, title='$title', title2='$title2', info_id='$info_id', website='$website', intro='$intro', content='$content', files='$files', create_time='$create_time', modify_time='$now', state=$state where id=$id";
        }
    }

//     echo $sql; exit();

    $rst = $db->query($sql);
    if ($rst) {
        //修改成功后删除老图片、附件
        if ($id > 0) {
            deleteFile($oldPic, 1);
            deleteFile($oldPic2, 1);
            deleteFile($oldAnnex, 1);
        } else {
            //修改段落数量
            $sql = "update info set lists=lists+1 where id=$info_id";
            if (!$db->query($sql)){
                $db->query("rollback");
                $db->close();
                info("修改段落数量失败！");
            }
        }
    } else {
        //添加或修改失败后 删除上传的图片、附件
        deleteFile($pic, 1);
        deleteFile($pic2, 1);
        deleteFile($annex, 1);
        //添加失败还要删除编辑器内上传的图片
        if ($id < 1) {
            deleteFiles($files, 2);
        }

        info("添加/编辑信息失败！");
    }
    $db->close();
    header("Location: $listUrl");
    exit;
}

//echo $id;exit;

if ($id < 1) {
    $sortnum = $db->getMax("info_list", "sortnum", "info_id='$info_id") + 10;
    $state = 1;
    $create_time = date("Y-m-d H:i:s");
} else {
    $sql = "select * from info_list where info_id=$info_id and id=$id";
    $rst = $db->query($sql);
    if ($row = $db->fetch_array($rst)) {
        $sortnum = $row["sortnum"];
        $title = $row["title"];
        $title2 = $row["title2"];
        $website = $row["website"];
        $pic = $row["pic"];
        $pic2 = $row["pic2"];
        $annex = $row["annex"];
        $intro = $row["intro"];
        $content = $row["content"];
        $files = $row["files"];
        $state = $row["state"];
        $create_time = $row["create_time"];
    } else {
        $db->close();
        info("指定的记录不存在！");
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
    <script charset="utf-8" src="kindeditor/kindeditor-min.js"></script>
    <script>
        KindEditor.ready(function (K) {
            K.create('textarea[name="content"]', {
                uploadJson: 'kindeditor/php/upload_json.php',
                fileManagerJson: 'kindeditor/php/file_manager_json.php',
                allowFileManager: true,
                afterCreate: function () {
                    var self = this;
                    K.ctrl(document, 13, function () {
                        self.sync();
                        K('form[name=form1]')[0].submit();
                    });
                    K.ctrl(self.edit.doc, 13, function () {
                        self.sync();
                        K('form[name=form1]')[0].submit();
                    });
                }
            });
            K.create('textarea[name="intro"]', {
                items: _items,
                afterCreate: function () {
                    var self = this;
                    K.ctrl(document, 13, function () {
                        self.sync();
                        K('form[name=form1]')[0].submit();
                    });
                    K.ctrl(self.edit.doc, 13, function () {
                        self.sync();
                        K('form[name=form1]')[0].submit();
                    });
                }
            });
        });
    </script>
    <script type="text/javascript">
        function check(form) {
            if (form.sortnum.value.match(/\D/)) {
                alert("请输入合法的序号！");
                form.sortnum.focus();
                return false;
            }

            if (form.title.value == "") {
                alert("请填入标题名称!");
                form.title.focus();
                return false;
            }

            if (form.pic.value != "") {
                var ext = form.pic.value.substr(form.pic.value.length - 3).toLowerCase();

                if (ext != "gif" && ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "swf" && ext != "wmv") {
                    alert("图片必须是GIF、JPG或PNG格式！");
                    return false;
                }
            }

            if (form.pic2.value != "") {
                var ext = form.pic2.value.substr(form.pic2.value.length - 3).toLowerCase();

                if (ext != "gif" && ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "swf" && ext != "wmv") {
                    alert("图片必须是GIF、JPG或PNG格式！");
                    return false;
                }
            }

            if (form.annex.value != "") {
                var ext = form.annex.value.substr(form.annex.value.length - 3).toLowerCase();

                if (ext != "gif" && ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "pdf" && ext != "doc" && ext != "xls" && ext != "ppt" && ext != "zip" && ext != "rar" && ext != "flv") {
                    alert("附件必须是PDF、DOC、XLS、PPT、ZIP、RAR或FLV格式！");
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr class="position">
        <td class="position">当前位置: 管理中心
            -&gt; <?= $db->getTableFieldValue("info_class", "name", "where id='$class_id'") ?> -&gt; 新增/编辑
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

<form name="form1" action="" method="post" enctype="multipart/form-data" onSubmit="return check(this);">
    <table width="100%" border="0" cellSpacing="1" cellPadding="0" align="center" class="editTable">
        <tr class="editHeaderTr">
            <td class="editHeaderTd" colSpan="2">修改资料</td>
        </tr>
        <tr class="editTr">
            <td class="editLeftTd">排列序号</td>
            <td class="editRightTd">
                <input type="text" name="sortnum" value="<?= $sortnum ?>" maxlength="10" size="5">
            </td>
        </tr>
        <?
        if ($session_admin_grade != ADMIN_COMMON) {
            ?>
            <tr class="editTr">
                <td class="editLeftTd">状态</td>
                <td class="editRightTd">
                    <select name="state" style="width:80px;">
                        <option value="1"<? if ($state == 1) echo "selected";?>>显示</option>
                        <option value="0"<? if ($state == 0) echo "selected";?>>隐藏</option>
                    </select>
                </td>
            </tr>
        <?
        }
        ?>

        <!--<tr class="editTr">
            <td class="editLeftTd">发表时间</td>
            <td class="editRightTd">
                <input type="text" name="create_time" value="<?/*= $create_time */?>" maxlength="20" size="24"> 时间格式为2013-10-01 00:00:00
            </td>
        </tr>-->
        <tr class="editTr">
            <td class="editLeftTd">标题名称</td>
            <td class="editRightTd"><input type="text" value="<?= $title ?>" name="title" size="80">
            </td>
        </tr>
        <!--<tr class="editTr">
            <td class="editLeftTd">副标题</td>
            <td class="editRightTd"><input type="text" value="<?/*= $title2 */?>" name="title2" size="100"></td>
        </tr>
        <tr class="editTr">
            <td class="editLeftTd">链接网址</td>
            <td class="editRightTd"><input type="text" value="<?/*= $website */?>" name="website" maxlength="300"
                                           size="50"></td>
        </tr>-->

        <tr class="editTr">
            <td class="editLeftTd">缩略图</td>
            <td class="editRightTd">
                <input type="file" name="pic" size="40">
                <?
                if ($pic != "") {
                    ?>
                    <input type="checkbox" name="del_pic" value="1"> 删除现有图片
                <?
                }
                ?>
            </td>
        </tr>
        <!--<tr class="editTr">
            <td class="editLeftTd">大图</td>
            <td class="editRightTd">
                <input type="file" name="pic2" size="40">
                <?/*
                if ($pic2 != "") {
                    */?>
                    <input type="checkbox" name="del_pic2" value="1"> 删除现有图片
                <?/*
                }
                */?>
            </td>
        </tr>
        <tr class="editTr">
            <td class="editLeftTd">附件</td>
            <td class="editRightTd">
                <input type="file" name="annex" size="40">
                <?/*
                if ($annex != "") {
                    */?>
                    <input type="checkbox" name="del_annex" value="1"> 删除现有附件
                <?/*
                }
                */?>
            </td>
        </tr>
        <tr class="editTr">
            <td class="editLeftTd">简介</td>
            <td class="editRightTd"><textarea name="intro"
                                              style="width:99%; height:150px;"><?php /*echo $intro; */?></textarea></td>
        </tr>
        <tr class="editTr">
            <td class="editLeftTd">详细内容</td>
            <td class="editRightTd"><textarea name="content"
                                              style="width:99%; height:350px;"><?php /*echo $content; */?></textarea>
            </td>
        </tr>-->
        <tr class="editFooterTr">
            <td class="editFooterTd" colSpan="2">
                <input type="submit" value=" 确 定 ">
                <input type="reset" value=" 重 填 ">
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">document.form1.title.focus();</script>
<?
$db->close();
?>
</body>
</html>
