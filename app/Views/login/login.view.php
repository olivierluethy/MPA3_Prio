<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/login.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/password_strength_checker.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/login.js"></script>
    <script defer src="public/js/main.js"></script>
    <script defer src="public/js/footer.js"></script>
    <script defer src="public/js/responsive.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <title>Login</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once(__ROOT__.'/header.php');
    ?>

    <div class="login">
        <div class="form">
            <h1 id="title">Login</h1>
            <div class="switch">
                <button id="registerButton" onclick="navSwitch(1)">Register</button>
                <button id="loginButton" onclick="navSwitch(2)">Login</button>
            </div>
            <form id="login" action="login" method="POST">
                <label for="email">Email:</label><br>
                <input type="email" id="email_login" name="email" placeholder="Enter Email"><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password_login" name="password" placeholder="Enter Password"><br><br>
                <input type="submit" value="Login">
            </form>

            <form id="register" action="register" method="POST">
                <label for="email">Email:</label><br>
                <input type="email" id="email_register" name="email" placeholder="Enter Email"><br>
                <label for="password">Password:</label><br>
                <input type="password" onkeyup="trigger()" id="password_register" name="password" placeholder="Enter Password"><br>
                <div class="password_strength_area">
                    <div class="indicator">
                        <span class="weak"></span>
                        <span class="medium"></span>
                        <span class="strong"></span>
                    </div>
                    <div class="text">Enter A Password</div>
                </div><br>
                <label for="verypass">Verify Password:</label><br>
                <input type="password" id="verypass" name="verypass" placeholder="Enter Password again"><br><br>
                <input type="submit" value="Register">
            </form>
        </div>
    </div>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>