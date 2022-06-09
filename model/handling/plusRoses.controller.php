<?php
require $_SERVER['DOCUMENT_ROOT'] . '/Core.php';
$dragon = new System;
$user = $dragon->userInfo();
$cache = $dragon->cache();

if (isset($_POST['type'])) {
    switch ($_POST['type']) {
        case 'completeJob':
            try {
                $id_mission = addslashes($_POST['id_mission']);
                $data = $dragon->plusRoses($user['invite']); // danh sách f1 f2 f3
                $handle = $dragon->checkMission($id_mission); // kiểm tra hợp lệ nhiệm vụ
                if ($handle) {
                    $data = $dragon->checkRank($user['username']); // rank user $data
                    $data1 = explode("vip", $dragon->checkMission($id_mission)['rank']); // rank job $data1[1]
                    $limited = $cache[$user['rank']];
                    if ($data1[1] <= $data) {
                        $handling = $dragon->handLingMisson($id_mission, $user['username'],$user['f0'], $user['invite'], $limited, $user['rank']);
                        $result = $dragon->res_json('success', 'Đánh giá thành công !');
                        echo json_encode($result);
                    } else {
                        $result = $dragon->res_json('error', 'Không đủ cấp bậc để làm nhiệm vụ !');
                        echo json_encode($result);
                    }
                } else {
                    $result = $dragon->res_json('error', 'Không tìm thấy nhiệm vụ !');
                    echo json_encode($result);
                }
            } catch (\Throwable $th) {
                echo $th;
                // $result = $dragon->res_json('error', 'Đã có lỗi xảy ra !');
                // echo json_encode($result);
            }
            break;
        default:
            $result = $dragon->res_json('error', 'Đã có lỗi xảy ra !');
            echo json_encode($result);
            break;
    }
}