<?
if ($member['checkin']==0){
    info('我们的管理员会于12小时内审核您的账户,通过审核后即可开通会员权限');
}
elseif($member['checkin']==2){
    info('您没有通过审核，如有疑问请咨询卓因客服.TEL:400-093-5090');
}