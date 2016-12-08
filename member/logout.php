
<?php
include_once "../init.php";
$redirectURL	= trim($_GET["redirectURL"]);

$_SESSION['userId'] = "";
$user = array();
$userId = false;

if($redirectURL){
    header("location: ".urldecode($redirectURL));exit();
} else {
    header("location: login.php");exit();
}

