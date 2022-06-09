<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Core.php';
$dragon = new System;
$dragon->check_login();
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
            <input type="text" id="email" placeholder="email" />
            <input type="password" id="password" placeholder="password" />
            <input type="text" id="session" value="<?php if(isset($token)){echo $token;} else {echo '';} ?>" placeholder="password" />
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
                url: "/api/webapi/account",
                data: {
                    type: 'login',
                    username: $('#email').val(),
                    password: $('#password').val(),
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        location.href = "/";
                    }
                }
            });
        });
    </script>
</body>
</html>