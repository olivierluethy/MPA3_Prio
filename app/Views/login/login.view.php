<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/password_strength_checker.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/login.js"></script>
    <script defer src="public/js/main.js"></script>    <script defer src="public/js/responsive.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
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

    <div class="login flex items-center justify-center px-4 py-12">
        <div class="form card w-full max-w-md">
            <h1 id="title" class="mb-6 text-center text-2xl font-bold text-white">Login</h1>
            <div class="switch mb-6 flex gap-2 rounded-full border border-solid border-surface-700 bg-surface-900 p-1.5">
                <button id="registerButton" onclick="navSwitch(1)"
                        class="flex-1 cursor-pointer rounded-full border-0 bg-surface-800 px-4 py-2 text-sm font-semibold text-surface-400 transition-colors">Register</button>
                <button id="loginButton" onclick="navSwitch(2)"
                        class="flex-1 cursor-pointer rounded-full border-0 bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition-colors">Login</button>
            </div>

            <form id="login" action="login" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="label">Email:</label>
                    <input type="email" id="email_login" name="email" placeholder="Enter Email" class="input">
                </div>
                <div>
                    <label for="password" class="label">Password:</label>
                    <input type="password" id="password_login" name="password" placeholder="Enter Password" class="input">
                </div>
                <input type="submit" value="Login" class="btn-primary w-full">
            </form>

            <form id="register" action="register" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="label">Email:</label>
                    <input type="email" id="email_register" name="email" placeholder="Enter Email" class="input">
                </div>
                <div>
                    <label for="password" class="label">Password:</label>
                    <input type="password" onkeyup="trigger()" id="password_register" name="password" placeholder="Enter Password" class="input">
                </div>
                <div class="password_strength_area">
                    <div class="indicator flex gap-1.5">
                        <span class="weak h-1.5 flex-1 rounded bg-surface-600"></span>
                        <span class="medium h-1.5 flex-1 rounded bg-surface-600"></span>
                        <span class="strong h-1.5 flex-1 rounded bg-surface-600"></span>
                    </div>
                    <div class="text mt-1.5 text-sm text-surface-400">Enter A Password</div>
                </div>
                <div>
                    <label for="verypass" class="label">Verify Password:</label>
                    <input type="password" id="verypass" name="verypass" placeholder="Enter Password again" class="input">
                </div>
                <input type="submit" value="Register" class="btn-primary w-full">
            </form>
        </div>
    </div>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
