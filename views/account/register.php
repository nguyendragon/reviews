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
            <input type="password" id="repassword" placeholder="password" />
            <input type="text" id="invite" placeholder="Invite" />
            <center>
                <div class="g-recaptcha" data-sitekey="6LfqipoeAAAAAPuMo2kEneULaQu48wyYyvLtrcf0"></div>
            </center>
            <button style="margin: 16px 0;">login</button>
            </form>  
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        $('button').click(function (e) { 
            e.preventDefault();
            
            if (grecaptcha.getResponse()) {
                $.ajax({
                    type: "POST",
                    url: "/api/webapi/account",
                    data: {
                        type: 'register',
                        username: $('#email').val(),
                        password: $('#password').val(),
                        repassword: $('#repassword').val(),
                        invite: $('#invite').val(),
                        captcha123: grecaptcha.getResponse(),
                    },
                    dataType: "json",
                    success: function (response) {
                        grecaptcha.reset();
                        if (response.status == "success") {
                            location.href = "/";
                        }
                    }
                });
            } else {
                console.log('Capcha');
            }
        });
    </script>
</body>
</html>