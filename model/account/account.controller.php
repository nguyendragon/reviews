<?php
// status: 1 - Họat động
// status: 2 - Bị Khóa

require $_SERVER['DOCUMENT_ROOT'] . '/Core.php';
$dragon = new System;
$response_data = null;
$response_data = "";
$data;

switch ($_POST['type']) {
    case 'login':
        try {
            $dragon->check_login();
            $username = addslashes($_POST['username']);
            $password = addslashes($_POST['password']);
            if (!$username || !$password) {
                $result = $dragon->res_json('error', 'Lỗi hệ thống ! Vui lòng thử lại sau !');
                die(json_encode($result));
            }

            if ($dragon->login($username, $password) == true) {
                $status = $dragon->status_user($username, $password);
                if ($status == 1) {
                    $token = $dragon->Creat_Token(15);
                    $res = mysqli_query($dragon->connect(), "UPDATE users SET token = '" . $token . "' WHERE `username`='" . $username . "'");
                    $_SESSION['token'] = $token;
                    $result = $dragon->res_json('success', 'Đăng nhập thành công !');
                    echo json_encode($result);
                    exit();
                } else {
                    $result = $dragon->res_json('error', 'Tài khoản đã bị khóa do vi phạm chính sách !');
                    echo json_encode($result);
                }
            } else {
                $result = $dragon->res_json('error', 'Tài khoản hoặc mật khẩu không chính xác!');
                echo json_encode($result);
            }
        } catch (\Throwable $th) {
            $result = $dragon->res_json('error', 'Lỗi hệ thống! Vui lòng thử lại sau!');
            echo json_encode($result);
        }
		break;
    case 'register':
        try {
            if(isset($_POST['captcha123'])) {
                $secret_key = '6LfqipoeAAAAABFL2Yu4a-uvYv2sgXxI3hBK9swd';
                $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['captcha123']);
                $response_data = json_decode($response);
                $checkCapCha = $response_data->success;
                if ($checkCapCha == true) { 
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $username = addslashes($_POST['username']);
                    $password = addslashes($_POST['password']);
                    $repassword = addslashes($_POST['repassword']);

                    $invite = addslashes($_POST['invite']);
                    
                    $token = $dragon->Creat_Token(15);
                    $id_user = $dragon->random_id(16);
                    $f0 = rand(100000, 999999);
                    
                    if (!$username || !$password || !$repassword) {
                        $result = $dragon->res_json('error', 'Lỗi hệ thống ! Vui lòng thử lại sau!');
                        die(json_encode($result));
                    }

                    $check_user = $dragon->anti_sql($username);

                    // Đếm địa chỉ ip
                    $get_ip = mysqli_query($dragon->connect(), "SELECT COUNT(ip_address) AS ip_address FROM users WHERE ip_address = '" . $ip . "' ")->fetch_array();
                    $num_ip = $get_ip['ip_address'];


                    // Kiểm tra xem tài khoản được đăng ký chưa
                    $check_register = mysqli_query($dragon->connect(), "SELECT * FROM `users` WHERE `username`='".$username."' ");
                    $countUser = mysqli_num_rows($check_register);

                    // Kiểm mã giới thiệu
                    $check_invite = mysqli_query($dragon->connect(), "SELECT * FROM `users` WHERE `f0`='".$invite."' ");
                    $countInvite = mysqli_num_rows($check_invite);
                    
                    if ($username && $password == $repassword && strlen($username) >= 9 && strlen($username) <= 25 && $invite && $countInvite == 1 && !$check_user && $countUser < 1) {
                        $res = mysqli_query( $dragon->connect(), "INSERT INTO users SET 
                        username = '".$username."'
                        , id_user = '".$id_user."'
                        , password = '".md5($password)."'
                        , token = '".$token."'
                        , ip_address = '".$ip."'
                        , f0 = '".$f0."'
                        , invite = '".$invite."'
                        , status = '".'1'."'
                        , time = '".$dragon->time()."' 
                        ");
                        $_SESSION['token'] = $token;
                        $result = $dragon->res_json('success', 'Đăng ký thành công !');
                        echo json_encode($result);
                        exit();
                    } else if($check_user) {
                        $result = $dragon->res_json('error', 'Tài khoản không đúng định dạng !');
                        echo json_encode($result);
                    } else if(strlen($username) < 9 || strlen($username) > 25) {
                        $result = $dragon->res_json('error', 'Tài khoản phải có độ dài lớn hơn 10 và nhỏ hơn 25 !');
                        echo json_encode($result);
                    } else if($countUser >= 1) {
                        $result = $dragon->res_json('error', 'Tài khoản này đã tồn tại !');
                        echo json_encode($result);
                    } else if($countInvite != 1) {
                        $result = $dragon->res_json('error', 'Mã mời không tồn tại !');
                        echo json_encode($result);
                    } else {
                        $result = $dragon->res_json('error', 'Đăng ký thất bại !');
                        echo json_encode($result);
                    }
                } else {
                    $data = array(
                        'error_capcha' => "Vui lòng xác minh lại !",
                        
                    );
                    echo json_encode($data);
                    $result = $dragon->res_json('error', 'Đăng ký thất bại !');
                    echo json_encode($result);
                }
            }
        } catch (\Throwable $th) {
            // print_r($th);
            $result = $dragon->res_json('error', 'Lỗi hệ thống! Vui lòng thử lại sau!');
            echo json_encode($result);
        }
        break;
    default:
        $result = $dragon->res_json('error', 'Lỗi hệ thống! Vui lòng thử lại sau !');
        echo json_encode($result);
		break;
}