<?
require("../init.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $data = $_POST["data"];

    if($_POST['action'] == 'message') {
        $table              = 'message';
        $data['sortnum']    = $db->getMax($table, "sortnum") + 10;
        $redirectURL        = filterHtml($_POST["redirectURL"]);
        $data['classId'] = filterHtml($data["classId"]);
        $data['name'] = filterHtml($data["name"]);
        $data['age'] = filterHtml($data["age"]);
        $data['sex'] = filterHtml($data["sex"]);
        $data['phone'] = filterHtml($data["phone"]);
        $data['createdTime']	= time();
        $data["ip"]             = $_SERVER['REMOTE_ADDR'];
        $data['status']         = 0;

        if (empty($data['name']) || empty($data['age']) || empty($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整!"
            )));
        } elseif (!isPhone($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "手机号格式不正确"
            )));
        }

        if ($db->add($table, $data)) {

            $db->updateBySql('kaiban', 'num=num-1', "id=".$data['classId']);

            exit(json_encode(array(
                'state' => 1,
                'msg' => "提交成功",
                'url' => $redirectURL."?id=".$data['classId']
            )));
        } else {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "提交失败"
            )));
        }
    } elseif($_POST['action'] == 'kaiban') {
        $redirectURL = filterHtml($_POST["redirectURL"]);
        $data['name'] = filterHtml($data["name"]);
        $data['age'] = filterHtml($data["age"]);
        $data['sex'] = filterHtml($data["sex"]);
        $data['phone'] = filterHtml($data["phone"]);

        if (empty($data['name']) || empty($data['age']) || empty($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整!"
            )));
        } elseif (!isPhone($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "手机号格式不正确"
            )));
        }

        $_SESSION["kaiban"] = $data;
        exit(json_encode(array(
            'state' => 1,
            'msg' => "提交成功",
            'url' => $redirectURL
        )));
    } elseif($_POST['action'] == 'kaibanAddress'){

        $table              = 'kaiban';
        $data['sortnum']    = $db->getMax($table, "sortnum") + 10;
        $redirectURL        = filterHtml($_POST["redirectURL"]);
        $data['area']	= filterHtml($data["area"]);
        $data['address']	= filterHtml($data["address"]);
        $data['ageBracket'] = filterHtml($data["ageBracket"]);

        if (empty($data['address']) || empty($data['area']) || empty($data['ageBracket'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整或请选择地区!"
            )));
        }

        $_SESSION["kaiban"]["sortnum"]      = $data['sortnum'];
        $_SESSION["kaiban"]["address"]      = $data['address'];
        $_SESSION["kaiban"]["area"]         = $data['area'];
        $_SESSION["kaiban"]["ageBracket"]   = $data['ageBracket'];
        $_SESSION["kaiban"]["createdTime"]  = time();
        $_SESSION["kaiban"]["ip"]           = $_SERVER['REMOTE_ADDR'];
        $_SESSION["kaiban"]["status"]       = 0;

        if ($db->add($table, $_SESSION["kaiban"])) {
            $_SESSION["kaiban"] = null;
            exit(json_encode(array(
                'state' => 1,
                'msg' => "提交成功",
                'url' => $redirectURL
            )));
        } else {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "提交失败"
            )));
        }

    } elseif($_POST['action'] == 'comment') {

        $table                  = 'comment';
        $redirectURL = filterHtml($_POST["redirectURL"]);
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['infoId']		    = filterHtml($data["infoId"]);
        $data['userId']		    = filterHtml($data["userId"]);
        $data['content']		= filterHtml($data["content"]);
        $data['createdTime']	= time();
        $data['status']         = 0;

        if (empty($data['infoId']) || empty($data['userId']) || empty($data['content']) ) {
            exit("缺少必要参数有误");
        }

        if ($db->add($table, $data)) {
            exit(json_encode(array(
                'state' => 1,
                'msg' => "评论成功,管理员审核中",
                'url' => $redirectURL
            )));
        } else {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "评论失败"
            )));
        }
    } elseif($_POST['action'] == 'vote') {

        $table                  = 'vote';
        $redirectURL = filterHtml($_POST["redirectURL"]);
        $data['infoId']		    = filterHtml($data["infoId"]);
        $data['ip']		    = $_SERVER["REMOTE_ADDR"];
        $data['createTime']		    = time();

        $todayTime = strtotime(date('Y-m-d'));
        $tomorrowTime = strtotime((date('Y-m-d',strtotime('+1 day'))));

        if (empty($data['infoId']) ) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "点赞失败"
            )));
        } elseif((int)$db->getCount('vote', "ip='".$data['ip']."' and infoId=".$data['infoId']." and createTime>$todayTime and createTime<=$tomorrowTime") >= 3){
            exit(json_encode(array(
                'state' => 0,
                'msg' => "同一IP每天只能点赞三次哦!"
            )));
        } else {
            if($db->add('vote', $data) && $db->updateBySql('info', 'vote=vote+1', "id=".$data['infoId'])){
                exit(json_encode(array(
                    'state' => 1,
                    'msg' => "点赞成功"
                )));
            }
        }
    } elseif($_POST['action'] == 'active') {

        $table                  = 'active';
        $redirectURL            = filterHtml($_POST["redirectURL"]);
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['infoId']		    = filterHtml($data["infoId"]);
        $data['name']		    = filterHtml($data["name"]);
        $data['phone']		    = filterHtml($data["phone"]);
        $data['content']		= filterHtml($data["content"]);
        $data['createdTime']	= time();
        $data['status']         = 1;

        if(!$info = $db->getByWhere("info", "id=".$data['infoId'])){
            exit(json_encode(array(
                'state' => 0,
                'msg' => "报名失败1"
            )));
        }

        $data['openId'] = $_SESSION['userInfo']['openid'];
        $data['orderId'] = build_order_no(8);
        $data['total'] = (float)$info['price'];

        if((float)$info['price'] > 0){
            $redirectURL = PATH."pay/index.php?orderId=".$data['orderId']."&id=".$data['infoId'];
        }

        if (empty($data['infoId']) || empty($data['name']) || empty($data['openId'])  || empty($data['orderId']) ) {
            exit("缺少必要参数有误");
        } elseif (!isPhone($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "手机号格式不正确"
            )));
        }

        if ($db->add($table, $data)) {
            exit(json_encode(array(
                'state' => 1,
                'msg' => "报名成功",
                'url' => $redirectURL
            )));
        } else {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "报名失败"
            )));
        }
    } elseif($_POST['action'] == 'message1') {
        $table                  = 'message';
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['name']           = filterHtml($data["name"]);
        $data['email']          = filterHtml($data["email"]);
        $data['phone']          = filterHtml($data["phone"]);
        $data['content']        = filterHtml($data["content"]);
        $data['createdTime']    = time();
        $data['ip']             = $_SERVER['REMOTE_ADDR'];
        $data['status']         = 0;

        if (empty($data['name']) || empty($data['email']) || empty($data['content'])) {
            exit($language->GetValue('incomplete'));
        }

        if (empty($site['email']) || !isMail($site['email']) || empty($site['emailPass']) || empty($site['smtpserver']) || empty($site['smtpserverport'])) {
            exit("站点邮箱设置不正确");
        }

        $message = "CONTACT<br>";
        $message .= "Name：".$data["name"]."<br>";
        $message .= "Email：".$data["email"]."<br>";
        $message .= "Phone：".$data["phone"]."<br>";
        $message .= "Content：".$data["content"]."<br>";
        $message .= "Created：".date('Y-m-d H:s:i',$data["createdTime"]);

        $smtpserver     = $site['smtpserver'];                                              //SMTP服务器
        $smtpserverport = $site['smtpserverport'];                                          //SMTP服务器端口
        $smtpusermail   = $site['email'];                                                   //SMTP服务器的用户邮箱
        $smtpuser       = $site['email'];                                                   //SMTP服务器的用户帐号
        $smtppass       = $site['emailPass'];                                               //SMTP服务器的用户密码

        $smtpemailto    = EMAIL;                                                            //发送给谁
        $mailtitle      = $site['title'];                                                   //邮件主题
        $mailcontent    = $message;                                                         //邮件内容
        $mailtype       = "HTML";                                                           //邮件格式（HTML/TXT）,TXT为文本邮件
        $smtp           = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);   //这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug    = false;                                                            //是否显示发送的调试信息
        $state          = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

        if(empty($state)){
            exit("对不起，邮件发送失败！请检查邮箱填写是否有误");
        }

    } elseif($_POST['action'] == 'email') {
        $table                  = 'email';
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['email']          = filterHtml($data["email"]);
        $data['createdTime']    = time();
        $data['ip']             = $_SERVER['REMOTE_ADDR'];
        $data['status']         = 0;

        if (empty($data['email'])) {
            exit($language->GetValue('incomplete'));
        }
        if (!isMail($data['email'])) {
            exit("请填写正确的邮箱格式");
        }

        if (empty($site['email']) || !isMail($site['email']) || empty($site['emailPass']) || empty($site['smtpserver']) || empty($site['smtpserverport'])) {
            exit("站点邮箱设置不正确");
        }

        if ($email = $db->getByWhere('info', "class_id=105105 and state>0", 'order by state desc, sortnum desc')) {

            require_once "include/email.class.php";

            $smtpserver     = $site['smtpserver'];                                              //SMTP服务器
            $smtpserverport = $site['smtpserverport'];                                          //SMTP服务器端口
            $smtpusermail   = $site['email'];                                                   //SMTP服务器的用户邮箱
            $smtpuser       = $site['email'];                                                   //SMTP服务器的用户帐号
            $smtppass       = $site['emailPass'];                                               //SMTP服务器的用户密码

            $smtpemailto    = $data['email'];                                                   //发送给谁
            $mailtitle      = $email['title'];                                                  //邮件主题
            $mailcontent    = replaceUploadBack2($email['content']);                                                //邮件内容
            $mailtype       = "HTML";                                                           //邮件格式（HTML/TXT）,TXT为文本邮件
            $smtp           = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);   //这里面的一个true是表示使用身份验证,否则不使用身份验证.
            $smtp->debug    = false;                                                            //是否显示发送的调试信息
            $state          = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

            if(empty($state)){
                exit("对不起，邮件发送失败！请检查邮箱填写是否有误");
            }
        } else {
            exit("邮箱为空或邮件模板为空");
        }
    } else {
        exit("Controller Error");
    }

    return;
}
?>
