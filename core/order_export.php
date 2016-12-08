<?
error_reporting(E_ALL);

require(dirname(__FILE__) . "/init.php");
require(dirname(__FILE__) . "/isadmin.php");
require(dirname(__FILE__) . "/config.php");

require_once '../include/Classes/PHPExcel.php';

$title  = trim($_POST["startTime"]) .'-'. trim($_POST["endTime"]);

$startTime  = strtotime( trim($_POST["startTime"]) );
$endTime    = strtotime( trim($_POST["endTime"]) );

if (empty($startTime) || empty($endTime) )
{
    info("开始时间和结束时间不能为空！");
}

//权限检查
if ($session_admin_grade != ADMIN_HIDDEN && $session_admin_grade != ADMIN_SYSTEM && $session_admin_grade != ADMIN_ADVANCED && hasInclude($session_admin_popedom, substr($class_id, 0, CLASS_LENGTH)) != true && hasInclude($session_admin_popedom, $class_id) != true)
{
    info("没有权限！");
}

//连接数据库
$db = new onlyDB($config["db_host"], $config["db_user"], $config["db_pass"], $config["db_name"]);
$sql = "select * from `orders` where create_time>=$startTime and create_time<=$endTime and state=2";

// echo $sql;
// exit();

$rst = $db->query($sql);
$data = array();
while ($row = $db->fetch_array($rst)) {
    array_push($data, $row);
}

//print_r($seat); exit;
//$db->close();

$objPHPExcel=new PHPExcel();
$objPHPExcel->getProperties()->setCreator('http://www.phpernote.com')
    ->setLastModifiedBy('http://www.phpernote.com')
    ->setTitle('Office 2007 XLSX Document')
    ->setSubject('Office 2007 XLSX Document')
    ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Result file');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1','订单号')
    ->setCellValue('B1','收件人')
    ->setCellValue('C1','联系电话')
    ->setCellValue('D1','收件地址');
    // ->setCellValue('D1','数量');

ob_clean();

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);

$i=2;
foreach($data as $k=>$v){

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i,' '.$v['orderId'])
        ->setCellValue('B'.$i,$v['userName'])
        ->setCellValue('C'.$i,$v['userPhone'])
        ->setCellValue('D'.$i,$v['userAddress']);
        // ->setCellValue('D'.$i,$v['create_time']);
    $i++;
}

$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);
//$filename=urlencode($active['title']).'_'.date('Y-m-dHis');

//生成xlsx文件
/*
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
*/

//生成xls文件
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="'.$title.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>