<?php 
require("../init.php");
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $class_id = $_POST['class_id'];
    $sum = $_POST['sum'];
    $pagesize = 1;
    $result = $db->getList("info","class_id=$class_id","","limit ". $sum * $pagesize . ", " . $pagesize);
    echo json_encode($result);
}
?>