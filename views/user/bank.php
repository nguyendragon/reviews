<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Core.php';
$dragon = new System;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
    <div class="login">
        <div class="form">
            <form class="login-form">
            <span class="material-icons">lock</span>
            <input type="text" id="bank" placeholder="Ngân hàng" />
            <input type="text" id="stk" placeholder="Số tài khoản ngân hàng" />
            <input type="text" id="restk" placeholder="Nhập lại số tài khoản ngân hàng" />
            <input type="text" id="name" placeholder="Họ và tên thật" />
            <button>login</button>
            </form>  
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $('button').click(function (e) { 
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/api/webapi/user/add/bank",
                data: {
                    type: 'addBank',
                    nameBanking: $('#bank').val(),
                    accountNumber: $('#stk').val(),
                    nameMember: $('#name').val()
                },
                dataType: "json",
                success: function (response) {
                    // if (response.status == "success") {
                    //     location.href = "/";
                    // }
                }
            });
        });
    </script>
</body>
</html>