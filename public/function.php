<?php
//取得今天日期
$dt = date("Y/m/d");
$tm = date("h:i:sa");

//取得連線端IP
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $myip = $_SERVER['HTTP_CLIENT_IP'];
} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $myip = $_SERVER['REMOTE_ADDR'];
}

//日期格式判斷
function isDate($str)
{
    if (preg_match("/^[0-9]{4}-[1-12]{2}-[1-31]{2}$/", $str)) {
        return true;
    } else {
        return false;
    }
}

//判斷是否等於0
function ckzero($z)
{
    if ($z == "0") {
        $z = "";
    } else {
        $z = @number_format(@$z, 0);
    }
    return $z;
}

//取得當月第一天與最後一天
function allmounth($m) {}
//字串過濾函數
function mystrreplace($str)
{
    $str1 = "";
    $str1 = str_replace("'", "", $str);
    $str1 = str_replace("-", "", $str1);
    return $str1;
}

//字串過濾函數,不過濾 -
function mystrreplace2($str)
{
    $str1 = "";
    $str1 = str_replace("'", "", $str);
    return $str1;
}

//清除逗號 ,
function cleanchr44($chr44)
{
    $chr44pstr = "";
    $chr44pstr = str_replace(",", "", $chr44);
    return $chr44pstr;
}

//清除空白
function cleanspace($chrspace)
{
    $chrspacepstr = "";
    $chrspacepstr = str_replace(" ", "", $chrspace);
    return $chrspacepstr;
}

//checkbox on/off 轉換成 0/1
function checkboxconv($ck)
{
    $converstr = "";
    if ($ck == "on") {
        $converstr = 1;
    } else {
        $converstr = 0;
    }
    return $converstr;
}
//轉換checkbox on=1 off=2
function ckboxchecked($cked)
{
    if ($cked == "1") {
        echo "checked ";
    } else {
        echo "";
    }
}
//checkbox 接收參數
function recivecheck($ckval)
{
    $val = "";
    if ($ckval == "true") {
        $val = "on";
    } else {
        $val = "off";
    }
    return $val;
}
//用圖形顯示是否需要簽核
function ck_img($ck)
{
    $val = "";
    if ($ck == "on") {
        $val = "<img src=icon/148.gif>";
    } else {
        $val = "<img src=icon/147.gif>";
    }
    return $val;
}
//判斷是否為數字
function ck_val_num($ck)
{
    $val = "";
    if (!is_numeric($ck)) {
        $val = "Not Number Type";
    }
    return $val;
}

//數字顯示小數點兩位
function f_number($n)
{
    if (is_numeric($n)) {
        echo number_format($n, 2);
    } else {
        $n = 0;
        echo number_format($n, 2);
    }
}

//顯示星星等級
function getstar($s)
{
    if ($s == '1') {
        $starimg = 'star1.jpg';
    }
    if ($s == '1.5') {
        $starimg = 'star1.5.jpg';
    }
    if ($s == '2') {
        $starimg = 'star2.jpg';
    }
    if ($s == '2.5') {
        $starimg = 'star2.5.jpg';
    }
    if ($s == '3') {
        $starimg = 'star3.jpg';
    }
    if ($s == '3.5') {
        $starimg = 'star3.5.jpg';
    }
    if ($s == '4') {
        $starimg = 'star4.jpg';
    }
    if ($s == '4.5') {
        $starimg = 'star4.5.jpg';
    }
    if ($s == '5') {
        $starimg = 'star5.jpg';
    }
    return $starimg;
}

//取得目前路徑
$self = $_SERVER['SCRIPT_FILENAME'];

//站台資訊
$EIPServer = array(
    'PHP_SELF', //0
    'argv', //1
    'argc', //2
    'GATEWAY_INTERFACE', //3 
    'SERVER_ADDR', //4
    'SERVER_NAME', //5
    'SERVER_SOFTWARE', //6
    'SERVER_PROTOCOL', //7
    'REQUEST_METHOD', //8
    'REQUEST_TIME', //9
    'REQUEST_TIME_FLOAT', //10 
    'QUERY_STRING', //11
    'DOCUMENT_ROOT', //12
    'HTTP_ACCEPT', //13
    'HTTP_ACCEPT_CHARSET', //14 
    'HTTP_ACCEPT_ENCODING', //15
    'HTTP_ACCEPT_LANGUAGE', //16
    'HTTP_CONNECTION', //17
    'HTTP_HOST', //18
    'HTTP_REFERER', //19
    'HTTP_USER_AGENT', //20
    'HTTPS', //21
    'REMOTE_ADDR', //22
    'REMOTE_HOST', //23
    'REMOTE_PORT', //24
    'REMOTE_USER', //25
    'REDIRECT_REMOTE_USER', //26 
    'SCRIPT_FILENAME', //27
    'SERVER_ADMIN', //28
    'SERVER_PORT', //29
    'SERVER_SIGNATURE', //30
    'PATH_TRANSLATED', //31
    'SCRIPT_NAME', //32
    'REQUEST_URI', //33
    'PHP_AUTH_DIGEST', //34
    'PHP_AUTH_USER', //35
    'PHP_AUTH_PW', //36
    'AUTH_TYPE', //37
    'PATH_INFO', //38
    'ORIG_PATH_INFO'
); //39 

//發送通知信件sendmail函數,$m=郵件收件者 , $c=郵件內容

//============================================================
// function otsysmail($m, $c, $s)
// {
//     $mail = new PHPMailer; // 先建立phpmailer實體
//     $mail->isSMTP();
//     $mail->SMTPAuth = false; //設定SMTP需要驗
//     $mail->SMTPDebug = 0; //是否顯示client-server對話字串 0=off 1= show client 2 = show client and server
//     $mail->Debugoutput = 'html'; //顯示對話串時的格式
//     $mail->Host = "192.168.0.2"; //電子郵件主機
//     $mail->Port = 25; //設定通訊port
//     $mail->Username = "LactyB2Bsystem";
//     $mail->Password = "suker123";
//     $mail->CharSet = "utf-8";  //信件內容編碼
//     $mail->Encoding = "base64"; //傳送編碼方式
//     $mail->setFrom('LactyB2Bsystem@lacty.com.vn', 'Lacty_B2B_System'); //寄件者
//     $mail->addReplyTo('su.ker@lacty.com.vn', 'Lacty_IT_Team'); //按回復郵件時的收件者
//     $mail->AddAddress($m);  //收件者
//     //$mail->Subject = 'Lacty B2B System mail,樂億_B2B_系統通知信件';//郵件主旨
//     $mail->Subject = $s;
//     $mail->Body = $c; //郵件內容
//     $mail->IsHTML(true); //設定郵件內容為HTML
//     $mail->send(); //郵件發送
// }


//偵測遠端瀏覽器版本與作業系統
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}
