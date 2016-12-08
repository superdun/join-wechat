<?
if ( !$userId ){
    header("location: ".PATH."member/login.php?redirectURL=$redirectURL"); exit();
}

$member = $db->getByWhere('member', "id=$userId");
if (!$member) {
    tips("未查找到该会员！", PATH."member/logout.php?redirectURL=$redirectURL");
}