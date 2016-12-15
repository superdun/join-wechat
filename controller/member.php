<?
//会员功能
require_once("../init.php");
require_once("../core/uploadImg.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $data = $_POST["data"];
    $action = filterHtml($_POST["action"]);

    if ($action == "sendMessage") {//短信验证方法
        $phone = trim($_POST["phone"]);
        if (empty($phone)) {
            $result = array(
                'state' => 0,
                'msg' => '手机号不能为空'
            );
        }
        if ($db->getCount('member', "phone=$phone")) {
            $result = array(
                'state' => 0,
                'msg' => "该手机号已经注册过，请更换号码"
            );
        } else {
//            $_SESSION[$phone . '_code'] = random(4, 1);
//            $data = $db->getByWhere('website_config', 'id=1');
//            if ($data) {
//                require_once("../include/sendMessage.php");
//                sendMessage::send($data['target'], $data['account'], $data['password'], $phone, "您的验证码是：" . $_SESSION[$phone . '_code'] . "。请不要把验证码泄露给其他人。");
//            }
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "register") {
        $table                  = 'member';
        $redirectURL            = filterHtml($_POST["redirectURL"]);
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['userId']         = uniqid();
        $data['name']		    = filterHtml($data["name"]);
        $data['password']       = filterHtml($data["password"]);
        $data['password2']      = filterHtml($data["password2"]);
        $data['phone']          = filterHtml($data["phone"]);
        $data['email']          = filterHtml($data["email"]);
        $data['createdTime']	= time();
        $data['last_login_time']= time();
        $data['last_login_ip']  = $_SERVER["REMOTE_ADDR"];
        $data['status']         = 1;
        $data['checkin']        = 0;

        if (empty($data['name']) || empty($data['password']) || empty($data['password2']) || empty($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整!"
            )));
        } elseif ($db->getCount('member', "name='".$data['name']."'")) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "用户名已存在"
            )));
        } elseif (strlen($data['password']) < 8) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "登录密码不能少于8位"
            )));
        } elseif ($data['password'] !== $data['password2']) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "两次输入的密码不一致"
            )));
        } elseif (!isPhone($data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "手机号格式不正确"
            )));
        } elseif ($db->getCount('member', "phone=".$data['phone'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "该手机号已经注册过"
            )));
//        } elseif (!isMail($data['email'])) {
//            exit(json_encode(array(
//                'state' => 0,
//                'msg' => "邮箱格式不正确"
//            )));
//        } elseif ($db->getCount('member', "email='".$data['email']."'")) {
//            exit(json_encode(array(
//                'state' => 0,
//                'msg' => "该邮箱已经注册过"
//            )));
        } else {
            $data['password'] = md5($data['password']);
            unset($data['password2']);
            if ($id = $db->add('member', $data, true)) {

                //设置登录状态
                $_SESSION['userId'] = encrypt($id, "E");

                exit(json_encode(array(
                    'state' => 1,
                    'msg' => "注册成功",
                    'url' => $redirectURL
                )));
            } else {
                exit(json_encode(array(
                    'state' => 0,
                    'msg' => "注册失败请稍后重试"
                )));
            }
        }
    } elseif ($action == "login") {
        $table                  = 'member';
        $redirectURL            = filterHtml($_POST["redirectURL"]);
        $data['sortnum']        = $db->getMax($table, "sortnum") + 10;
        $data['name']		    = filterHtml($data["name"]);
        $data['password']       = md5(filterHtml($data["password"]));
        $data['createdTime']	= time();
        $data['status']         = 1;

        if (empty($data['name']) || empty($data['password'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "用户名或密码不能为空"
            )));
        } else {
//            if (isPhone($phone)) {
                $user = $db->getByWhere('member', "name='".$data['name']."' and password='".$data['password']."' and status>0");
                if ($user) {
                    $data = array(
                        'last_login_time' => time(),
                        'last_login_ip' => $_SERVER["REMOTE_ADDR"]
                    );
                    $db->update('member', $data, "id=" . $user["id"]);

                    //设置登录状态
                    $_SESSION['userId'] = encrypt($user["id"], "E");

                    exit(json_encode(array(
                        'state' => 1,
                        'msg' => "登录成功",
                        'url' => $redirectURL
                    )));
                } else {
                    exit(json_encode(array(
                        'state' => 0,
                        'msg' => "帐号或密码错误"
                    )));
                }
//            } else {
//                exit(json_encode(array(
//                    'state' => 0,
//                    'msg' => "请填写正确的手机号登录"
//                )));
//            }
        }
    } elseif ($action == "change") {
        $table                  = 'member';
        $redirectURL            = PATH;
        $data['oldPassword']    = filterHtml($data["oldPassword"]);
        $data['password']       = filterHtml($data["password"]);
        $data['password2']      = filterHtml($data["password2"]);

        if (empty($data['oldPassword']) || empty($data['password']) || empty($data['password2'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整"
            )));
        } elseif (!$db->getCount('member', "password='".md5($data['oldPassword'])."' and id=$userId")){
            exit(json_encode(array(
                'state' => 0,
                'msg' => "原密码不正确"
            )));
        } elseif (strlen($data['password']) < 8) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "登录密码不能少于8位"
            )));
        } elseif ($data['password'] !== $data['password2']){
            exit(json_encode(array(
                'state' => 0,
                'msg' => "两次输入的密码不一致"
            )));
        } elseif ($data['oldPassword'] === $data['password']) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "新密码不能和原密码相同"
            )));
        } else {
            $data['password'] = md5($data['password']);
            unset($data['password2']);
            unset($data['oldPassword']);
            if ($db->update('member', $data, "id=$userId")) {

                $_SESSION['userId'] = "";
                $user = array();

                exit(json_encode(array(
                    'state' => 1,
                    'msg' => "密码修改成功，请重新登录",
                    'url' => $redirectURL
                )));
            } else {
                exit(json_encode(array(
                    'state' => 0,
                    'msg' => "密码修改失败"
                )));
            }
        }
    } elseif ($action == "changeEmail") {
        $table                  = 'member';
        $redirectURL            = filterHtml($_POST["redirectURL"]);
        $data['email']          = filterHtml($data["email"]);

        if (empty($data['email'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "表单填写不完整"
            )));
        } elseif (!isMail($data['email'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "邮箱格式不正确"
            )));
        } elseif ($data['email'] === $user['email']) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "新邮箱不能和原邮箱相同"
            )));
        } elseif ($db->getCount('member', "email='".$data['email']."' and id<>$userId")) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "该邮箱已占用"
            )));
        } else {
            if ($db->update('member', $data, "id=$userId")) {
                exit(json_encode(array(
                    'state' => 1,
                    'msg' => "邮箱修改成功",
                    'url' => $redirectURL
                )));
            } else {
                exit(json_encode(array(
                    'state' => 0,
                    'msg' => "邮箱修改失败"
                )));
            }
        }
    }  elseif ($action == "forgot") {
        $table                  = 'member';
        $redirectURL            = filterHtml($_POST["redirectURL"]);
        $data['email']          = filterHtml($data["email"]);

        if (empty($data['email'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "邮箱不能为空"
            )));
        } elseif (!isMail($data['email'])) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "邮箱格式不正确"
            )));
        } elseif (!$db->getCount('member', "email='".$data['email']."'")) {
            exit(json_encode(array(
                'state' => 0,
                'msg' => "邮箱不存在"
            )));
        } else {
            $number = build_order_no(6, false);

            if (empty($site['host']) || empty($site['port']) || empty($site['userName']) || empty($site['password'])) {
                exit("站点邮箱设置不正确");
            }
            $member = $db->getByWhere('member', "email='".$data['email']."'");

            $array = array(
                "host" => $site['host'],            //邮箱主机
                "port" => $site['port'],            //邮箱端口
                "userName" => $site['userName'],    //用户名(一般为邮箱地址)
                "password" => $site['password'],    //密码(QQ邮箱为授权码)
                "receiver" => $data['email'],       //收件人
                "sender" => $site['userName'],      //发件人
                "senderName" => $site['userName'],  //发件人名称
                "subject" => $site['title'],        //邮件主题
                //邮件内容 //<a href=\"".$redirectURL."\" target=\"_blank\">点击登录</a>
                "content" => "尊敬的". $site['name'] ."用户您好，用户名为：【".$member['name']."】，密码重置为【".$number."】。"
            );

            if(sendMail($array) && $db->update('member', array("password"=>md5($number)), "email='".$data['email']."'")){
                exit(json_encode(array(
                    'state' => 1,
                    'msg' => "密码发送成功",
                    'url' => $redirectURL
                )));
            } else {
                exit(json_encode(array(
                    'state' => 0,
                    'msg' => "密码发送失败"
                )));
            }
        }
    } elseif ($action == "profile") {
        $userName = filterHtml(trim($_POST["userName"]));
        $email = filterHtml(trim($_POST["email"]));
        $datingStatus = (int)($_POST["datingStatus"]);
        $sex = (int)($_POST["sex"]);
        $year = (int)($_POST["year"]);
        $month = (int)($_POST["month"]);
        $day = (int)($_POST["day"]);
        $height = (int)($_POST["height"]);
        $weight = (int)($_POST["weight"]);
        $bodystyle = (int)($_POST["bodystyle"]);
        $blood = (int)($_POST["blood"]);
        $marriage = (int)($_POST["marriage"]);
        $child = (int)($_POST["child"]);
        $education = (int)($_POST["education"]);
        $ethnic = (int)($_POST["ethnic"]);
        $province = filterHtml(trim($_POST["province"]));
        $city = filterHtml(trim($_POST["city"]));
        $province2 = filterHtml(trim($_POST["province2"]));
        $city2 = filterHtml(trim($_POST["city2"]));
        $lovesort = filterHtml(trim($_POST["lovesort"]));
        $jobs = filterHtml(trim($_POST["jobs"]));
        $salary = filterHtml(trim($_POST["salary"]));
        $housing = filterHtml(trim($_POST["housing"]));
        $caring = filterHtml(trim($_POST["caring"]));

        if (empty($userName)) {
            $result = array(
                'state' => 0,
                'msg' => "用户名不能为空"
            );
            echo json_encode($result);
            exit;
        }
        if ($db->getCount('member', "id<>$userId and userName='$userName'")) {
            $result = array(
                'state' => 0,
                'msg' => "昵称已被占用，请更换",
            );
            echo json_encode($result);
            exit;
        }

        if (empty($email)) {
            $result = array(
                'state' => 0,
                'msg' => "邮箱不能为空"
            );
            echo json_encode($result);
            exit;
        }
        if ($db->getCount('member', "id<>$userId and email='$email'")) {
            $result = array(
                'state' => 0,
                'msg' => "邮箱已被占用，请更换",
            );
            echo json_encode($result);
            exit;
        }

        $data = array(
            "userName" => $userName,
            "datingStatus" => $datingStatus,
            "email" => $email,
            "sex" => $sex,
            "year" => $year,
            "month" => $month,
            "day" => $day,
            "height" => $height,
            "weight" => $weight,
            "bodystyle" => $bodystyle,
            "blood" => $blood,
            "marriage" => $marriage,
            "child" => $child,
            "education" => $education,
            "ethnic" => $ethnic,
            "province" => $province,
            "city" => $city,
            "province2" => $province2,
            "city2" => $city2,
            "lovesort" => $lovesort,
            "jobs" => $jobs,
            "salary" => $salary,
            "housing" => $housing,
            "caring" => $caring,
        );
        if ($db->update('member', $data, "id=$userId")) {
            $result = array(
                'state' => 1,
                'msg' => "修改成功"
            );
        } else {
            $result = array(
                'state' => 0,
                'msg' => "修改失败"
            );
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "declaration") {
        $content = filterHtml(trim($_POST["content"]));

        if (empty($content)) {
            $result = array(
                'state' => 0,
                'msg' => "内心独白不能为空"
            );
        } else {
            $data = array(
                "content" => $content,
                "state" => 0
            );
            if ($db->update('member', $data, "id=$userId")) {
                $result = array(
                    'state' => 1,
                    'msg' => "修改成功"
                );
            } else {
                $result = array(
                    'state' => 0,
                    'msg' => "修改失败"
                );
            }
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "sendSms") {
        $addresseeUserId = filterHtml(trim($_POST["addresseeUserId"]));
        $content = filterHtml(trim($_POST["content"]));

        $addresseeUser = $db->getByWhere('member', "userId='$addresseeUserId'");

        if (empty($addresseeUserId) || !$addresseeUser) {
            $result = array(
                'state' => 0,
                'msg' => "收信人ID不存在",
            );
            echo json_encode($result);
            exit;
        }

        if (empty($content) || empty($addresseeUserId)) {
            $result = array(
                'state' => 0,
                'msg' => "收件人或内容不能为空"
            );
        } else {
            $data = array(
                "sendUserId" => $userId,
                "addresseeUserId" => $addresseeUser['id'],
                "content" => $content,
                "state" => 0,
                "createTime" => time()
            );
            if ($db->add('letter', $data)) {
                $result = array(
                    'state' => 1,
                    'msg' => "发送成功"
                );
            } else {
                $result = array(
                    'state' => 0,
                    'msg' => "发送失败，请重新尝试"
                );
            }
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "checkUserName") {
        $userId = (int)($_POST["userId"]);
        $userName = filterHtml(trim($_POST["field"]));

        if (empty($userName)) {
            $result = array(
                'state' => 0,
                'msg' => "用户名不能为空"
            );
        } elseif ($db->getCount('member', "id<>$userId and userName='$userName'")) {
            $result = array(
                'state' => 0,
                'msg' => "昵称已被占用，请更换",
            );
        } else {
            $result = array(
                'state' => 1,
                'msg' => "昵称可用",
            );
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "checkPhone") {
        $userId = (int)($_POST["userId"]);
        $phone = trim($_POST["field"]);

        if (empty($phone) || !isPhone($phone)) {
            $result = array(
                'state' => 0,
                'msg' => "手机号不能为空或手机号格式不正确"
            );
        } elseif ($db->getCount('member', "id<>$userId and phone='$phone'")) {
            $result = array(
                'state' => 0,
                'msg' => "手机号已被占用，请更换",
            );
        } else {
            $result = array(
                'state' => 1,
                'msg' => "手机号可用  ",
            );
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "checkEmail") {
        $userId = (int)($_POST["userId"]);
        $email = trim($_POST["field"]);

        if (empty($email) || !isMail($email)) {
            $result = array(
                'state' => 0,
                'msg' => "邮箱不能为空或邮箱格式不正确"
            );
        } elseif ($db->getCount('member', "id<>$userId and email='$email'")) {
            $result = array(
                'state' => 0,
                'msg' => "邮箱已被占用，请更换",
            );
        } else {
            $result = array(
                'state' => 1,
                'msg' => "邮箱可用  ",
            );
        }
        echo json_encode($result);
        exit;
    } elseif ($action == "uploadAvatar") {
        $pic = &$_FILES["pic"];
        $pic = uploadImg($pic, "gif,jpg,jpeg,png");

        if (empty($pic)) {
            tips("图片格式不正确(gif,jpg,jpeg,png)");
        }

        $oldPic = $db->getField("member", "avatar", "where id=$userId");
        if ( $db->update( "member", array('avatar'=>$pic), "id=$userId") ) {
            deleteFile($oldPic, 1);
            header("location: ".PATH."member/index.php");
        } else {
            deleteFile($pic, 1);
            tips("上传失败");
        }
    } elseif ($action == "addGallery") {
        $pic = &$_FILES["pic"];
        $pic = uploadImg($pic, "gif,jpg,jpeg,png");

        if (empty($pic)) {
            tips("图片格式不正确(gif,jpg,jpeg,png)");
        }

        $sortnum = $db->getMax("member_gallery", "sortnum", "userId=$userId") + 10;
        $db->updateBySql("member", "gallery=gallery+1", "id=$userId");
        if ( $db->add( "member_gallery", array("sortnum"=>$sortnum, "pic"=>$pic, "userId"=>$userId, "state"=>1)) ) {
            header("Location: ".PATH.'member/gallery.php');
            exit;
        } else {
            tips("上传失败");
        }
    } elseif ($action == "deleteImage") {
        $id_array = explode(",", trim($_POST["ids"]));
        $redirectURL = trim($_POST["redirectURL"]);

        if (empty($id_array)) {
            $result = array(
                'state' => 0,
                'msg' => "参数不正确",
            );
        } else {
            foreach($id_array as $v){
                $data = $db->getByWhere("member_gallery", "id=$v");
                if($data){
                    deleteFiles($data['pic'], 1);
                    $db->delete('member_gallery', "id=$v");
                    $db->updateBySql("member", "gallery=gallery-1", "id=$userId");
                }
            }

            $result = array(
                'state' => 1,
                'msg' => "删除成功",
                'url' => $redirectURL
            );
        }

        echo json_encode($result);
        exit;
    }
}
?>
