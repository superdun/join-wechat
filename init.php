<?
ob_start();                                            //使用缓冲
session_start();

//装载必须的文件
require_once("config.php");
require_once("include/onlyDB.php");
require_once("include/onlyException.php");
require_once("include/page.php");
require_once("include/functions.php");
require_once("include/language.php");
require_once("include/phpmailer/class.phpmailer.php");
require_once('include/cart.php');

header("Content-Type:text/html;charset=utf-8;");    //指定字符集
date_default_timezone_set('Asia/Shanghai');

//输入检查
function process_variables(&$val, $key)
{
    if (is_array($val)) {
        foreach ($val as $k => $v) {
            process_variables($v, $k);
        }
    } else {
        $val = addslashes($val);
    }
}

if (!get_magic_quotes_gpc()) {
    array_walk($_GET, "process_variables");
    array_walk($_POST, "process_variables");
    array_walk($_FILES, "process_variables");
    array_walk($_COOKIE, "process_variables");
    if (is_array(@$_SESSION)) {
        array_walk($_SESSION, "process_variables");
    }
}

//实例语言
$language = new Language();

$cart = new Cart();

//建立数据库连接对象
$data       = array();
$site       = array();
$db         = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);
$site       = $db->getByWhere("config_base", "id=1");

//栏目列表
$secondArray   = array();   //存放所有二级栏目,以一级栏目为Key
$thirdArray    = array();   //存放所有三级栏目,以二级栏目为Key
$categoryArray = $db->getListField("info_class", "*", "id like '___' and isTop>0", "order by sortnum asc");
foreach($categoryArray as $key=>$val){
    $secondList = $db->getListField("info_class", "*", "id like '".$val['id']."___' and isTop>0", " order by sortnum asc");

    $secondArray[$val['id']] = $secondList;

    foreach($secondList as $key2 => $val2){
        $thirdList = $db->getListField("info_class", "*", "id like '".$val2['id']."___' and isTop>0", " order by sortnum asc");
        $secondList[$key2]['children'] = $thirdList;

        $thirdArray[$val2['id']] = $thirdList;
    }

    $categoryArray[$key]['children'] = $secondList;
    $categoryArray[$categoryArray[$key]['id']] = $categoryArray[$key];
    unset($categoryArray[$key]);
}

if( isset($_SESSION['userId']) ){
    $userId = (int)encrypt($_SESSION['userId'], "D");
    $user = $db->getByWhere('member', "id=$userId");
} else {
    $userId = false;
}