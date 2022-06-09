<?php
// error_reporting(1);
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
session_start();
date_default_timezone_set("Asia/Bangkok");
require_once 'config.php';

$root = $_SERVER['DOCUMENT_ROOT'];


class System {
    public function __construct() {
        $this->connect();
    }

    // Kết Nối Database
    public function connect() {
        global $config;
        $conn = mysqli_connect($config['LOCALHOST'], $config['USERNAME'], $config['PASSWORD'], $config['DATABASE']) or die("Can't Connect To Database!");
        $conn->set_charset("utf8");
        return $conn;
    }

    // Lấy url
    public function home_url() 
    {

        if ( isset($_SERVER['HTTPS']) ) {
            if ( 'on' == strtolower($_SERVER['HTTPS']) )
                $tcp = 'https';
            if ( '1' == $_SERVER['HTTPS'] )
                $tcp = 'https';
        } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
                $tcp = 'https';
        }else {
                $tcp = 'http';
        }

	   $domain = $tcp.'://'.$_SERVER['HTTP_HOST'];
	   return $domain;
    }

    /***   Anti SQL Injection - Chỉ nhận dạng Số   ***/
    public function anti_sql($number) 
    {
        $pattern = '/[^a-z0-9]/';
        $id = preg_match($pattern, $number); // Outputs 1
        return $id; 
        // type boolean.
        // 1 = true là có tồn tài ký tự khác số .
        // 0 = false là không tồn tại ký tự khác số.
    }

    /***   đếm tất cả người dùng hệ thống   ***/
    public function count_user() {
        $result = mysqli_query($this->connect(), "SELECT `id` FROM `users` WHERE `status` = 1");
        $rowcount = mysqli_num_rows($result);
        return $rowcount;
    }

    /***   đếm các f* trong hệ thống   ***/
    public function id_invite($invite) {
        $result_f1 = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `invite` = '".$invite."' ");
        $f1 = array();
        while($row = mysqli_fetch_array($result_f1)) {
            array_push($f1, $row['f0']);
        }

        $f2 = array();
        for ($i = 0; $i < count($f1); $i++) { 
            $f1_temp = $f1[$i];
            $result_f2 = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `f0` = '".$f1_temp."' ");
            while($row = mysqli_fetch_array($result_f2)) {
                $result_f2s = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `invite` = '".$row['f0']."' ");
                while($row1 = mysqli_fetch_array($result_f2s)) {
                    array_push($f2, $row1['f0']);
                }
            }
        }

        $f3 = array();
        for ($i = 0; $i < count($f2); $i++) {
            $f2_temp = $f2[$i];
            $result_f3 = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `invite` = '".$f2_temp."' ");
            while($row = mysqli_fetch_array($result_f3)) {
                array_push($f3, $row['f0']);
            }
        }
        
        $data = array(
            'f1' => $f1,
            'f2' => $f2,
            'f3' => $f3
        );
        
        return json_encode($data);
    }

    /***   f1 f2 f3   ***/
    public function plusRoses($invite) {
        $result_f1 = mysqli_query($this->connect(), "SELECT username, invite FROM `users` WHERE `invite` = '".$invite."' ")->fetch_array();
        if ($result_f1) {
            $f1 = intval($result_f1['invite']);
            $user_f1 = intval($result_f1['username']);
        } else {
            $f1 = "";
            $user_f1 = "";
        }
        $result_f2 = mysqli_query($this->connect(), "SELECT username, invite FROM `users` WHERE `f0` = '".$f1."' ")->fetch_array();
        
        if ($result_f2) {
            $f2 = intval($result_f2['invite']);
            $user_f2 = intval($result_f2['username']);
        } else {
            $f2 = "";
            $user_f2 = "";
        }
        $result_f3 = mysqli_query($this->connect(), "SELECT username, invite FROM `users` WHERE `f0` = '".$f2."' ")->fetch_array();
        if ($result_f3) {
            $f3 = intval($result_f3['invite']);
            $user_f3 = intval($result_f3['username']);
        } else {
            $f3 = "";
            $user_f3 = "";
        }
        
        $data = array(
            'f1' => $f1,
            'f2' => $f2,
            'f3' => $f3,
            'user_f1' => $user_f1,
            'user_f2' => $user_f2,
            'user_f3' => $user_f3
        );
        return $data;
    }

    // Kiểm tra trạng thái công việc
    public function checkMission($id_misson) {
        $result_f1 = mysqli_query($this->connect(), "SELECT * FROM `mission` WHERE `id_misson` = '".$id_misson."' ")->fetch_assoc();
        return $result_f1;
    }

    // Check Rank
    public function checkRank($username) {
        $result_f1 = mysqli_query($this->connect(), "SELECT `rank` FROM `users` WHERE `username` = '".$username."' ")->fetch_assoc();
        $data = explode("vip", $result_f1['rank']);
        return $data[1];
    }

    // Xử lý công việc
    public function handLingMisson($id_mission, $username,$f0, $invite, $limited, $userRank)
    {
        $today = date('d/m/Y');
        $mission = $this->checkMission($id_mission);
        $limit = mysqli_query($this->connect(), "SELECT id FROM `mission_done` WHERE `username` = '".$username."' AND `time` = '".$today."' ");
        $countLimit = mysqli_num_rows($limit);
        $roses = $mission['roses'];
        $plusRoses = $this->plusRoses($invite);

        $f1 = $plusRoses['f1'];
        $f2 = $plusRoses['f2'];
        $f3 = $plusRoses['f3'];

        $cache = $this->cache();
        $roses_f1 = $cache['f1'];
        $roses_f2 = $cache['f2'];
        $roses_f3 = $cache['f3'];

        if ($countLimit <= $limited) {

            mysqli_query($this->connect(), "UPDATE users SET `money` = `money` + $roses WHERE `username`='" . $username . "'");
            
            if (strtolower($userRank) != "vip0") {
                mysqli_query($this->connect(), "UPDATE users SET `roses` = `roses` + $roses_f1 WHERE `f0`='" . $f1 . "'");
                mysqli_query($this->connect(), "UPDATE users SET `roses` = `roses` + $roses_f2 WHERE `f0`='" . $f2 . "'");
                mysqli_query($this->connect(), "UPDATE users SET `roses` = `roses` + $roses_f3 WHERE `f0`='" . $f3 . "'");
            }

            // mysqli_query($this->connect(), "INSERT INTO mission_done SET 
            // username = '".$username."'
            // , f0 = '".$f0."'
            // , invite = '".$invite."'
            // , id_misson = '".$id_mission."'
            // , price = '".$mission['price']."'
            // , roses = '".$mission['roses']."'
            // , rank = '".$mission['rank']."'
            // , time = '".$today."' 
            // ");
            return $plusRoses;
        } else {
            return $this->res_json('error', 'Bạn đã hoàn thành nhiệm vụ ngày hôm nay !');
        }
    }

    public function cache()
    {
        $cache = mysqli_query($this->connect(), "SELECT * FROM `cache` ")->fetch_assoc();
        return $cache;
    }

    /***   Đăng nhập   ***/
    public function login($user, $pass) {
        $user = str_replace('"',"\"",$user);
        $user = str_replace("'","\'",$user);
        $pass = str_replace('"',"\"",$pass);
        $pass = str_replace("'","\'",$pass);

        $result = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `username`='".$user."' AND `password`='".md5($pass)."' ");
        $rowcount = mysqli_num_rows($result);
        if($rowcount > 0) {
            return true;
        }else {
            return false;
        }
        
    }
    /***   Kiểm tra đăng nhập   ***/
    public function check_login() {
        if(isset($_SESSION['token'])) {
            return header("Location: /");
        }
    }
    /***   Kiểm tra đăng nhập   ***/
    public function check_login2() {
        if(!$_SESSION['token']) {
            return header("Location: /login");
        }
    }

    public function userInfo() {
        $token = $_SESSION['token'];
        $result = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `token`='".$token."'")->fetch_array();
        return $result;
    }


    /***   tạo ra chuỗi ngẫu nhiên gồm cả số và chữ (tạo token)    ***/
    public function Creat_Token($length){
        $token = openssl_random_pseudo_bytes($length);
        $token = bin2hex($token);
        return $token;
    }

    /****  Json_decode  ***/
    public function res_json($status, $message){
        $data = array(
            'status' => "$status",
            'message' => "$message",
        );
        return $data;
    }
    
    /***  Format Money  ***/
    public function money($data) {
        return str_replace(",", ".", number_format($data));
    }

    public function time()
    {
        $time = date("d M Y, H:i a");
        return $time;
    }

    public function status_user($username, $password)
    {
        $result = mysqli_query($this->connect(), "SELECT `status` FROM `users` WHERE `username`='".$username."' AND `password`='".md5($password)."' ")->fetch_array();
        return $result['status'];
    }

    public function random_id($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}