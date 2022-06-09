<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Core.php';
$dragon = new System;
if (isset($_GET['type'])) {
    switch ($_GET['type']) {
        case 'members':
            $user = $dragon->userInfo();
            echo $dragon->id_invite($user['f0']);
            break;
        default:
            $result = $dragon->res_json('error', 'Đã có lỗi xảy ra !');
            echo json_encode($result);
            break;
    }
}

if (isset($_POST['type'])) {
    switch ($_POST['type']) {
        case 'logout':
            session_destroy();
            $result = $dragon->res_json('success', 'Đăng xuất thành công');
            echo json_encode($result);
            exit();
            break;
        case 'addBank':
            $nameBanking = addslashes($_POST['nameBanking']);
            $accountNumber = addslashes($_POST['accountNumber']);
            $nameMember = addslashes($_POST['nameMember']);
            if (!$nameBanking || !$accountNumber || !$nameMember) {
                $result = $dragon->res_json('error', 'Lỗi hệ thống ! Vui lòng thử lại sau!');
                die(json_encode($result));
            }

            $user = $dragon->userInfo();
            $checkAccountNumber = $dragon->anti_sql($accountNumber);
            $check_Bank = mysqli_query($dragon->connect(), "SELECT * FROM `banking` WHERE `username`='".$user['username']."' ");
            $countBank = mysqli_num_rows($check_Bank);
            if (!$checkAccountNumber && $user['username'] && $countBank < 1) {
                $res = mysqli_query( $dragon->connect(), "INSERT INTO banking SET 
                username = '".$user['username']."'
                , name_bank = '".$nameBanking."'
                , stk = '".$accountNumber."'
                , name_user = '".$nameMember."'
                , time = '".$dragon->time()."' 
                ");
                $result = $dragon->res_json('success', 'Thêm ngân hàng thành công !');
                echo json_encode($result);
            } else if ($countBank >= 1) {
                $result = $dragon->res_json('error', 'Tài khoản đã ràng buộc thẻ ngân hàng !');
                echo json_encode($result);
            } else {
                $result = $dragon->res_json('error', 'Đã có lỗi xảy ra !');
                echo json_encode($result);
            }
            break;
        default:
            $result = $dragon->res_json('error', 'Đã có lỗi xảy ra !');
            echo json_encode($result);
            break;
    }
}