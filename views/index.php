<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/Core.php';
    $dragon = new System;
    $dragon->check_login2();
    $user = $dragon->userInfo();
    $token = $_SESSION['token'];
    if($token != $user['token']) {
        session_destroy();
        header("Location: /login");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>
        <?=
        $_SESSION['token'];
        ?>
    </h1>
    <button class="btn1" id="RGHBEOIEBFV">Click me !</button>
    <button class="btn2">Đăng xuất</button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // $.ajax({
        //     type: "GET",
        //     url: "/api/webapi/user/info/members",
        //     data: {
        //         type: 'members'
        //     },
        //     dataType: "json",
        //     success: function (response) {
        //         console.log(response['f1']);
        //         console.log(response['f2']);
        //         console.log(response['f3']);
        //     }
        // });
        $('.btn1').click(function (e) {
            const id = $(this)[0].id;
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/api/webapi/user/roses",
                data: {
                    type: 'completeJob',
                    id_mission: id
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                }
            });
        });
        $('.btn2').click(function (e) { 
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/api/webapi/user/info/members",
                data: {
                    type: 'logout'
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        location.href = "/login";
                    }
                }
            });
        });
    </script>
</body>
</html>