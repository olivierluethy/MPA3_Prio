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
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <title>Login</title>
</head>

<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="part1" onclick="home()">
            <img src="images/logo.png" alt="">
            <h1>Prio</h1>
        </div>
        <div class="part2">
        <?php
            /* Wenn Benutzer noch nicht eingeloggt ist */
            if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                echo "<button class='loginBtn' onclick='goToLogin()'>Login  <i class='fas fa-sign-in-alt'></i></button>";
            }else{
                /* Admin */
                if($_SESSION['role'] == 1){
                    echo "<a href='admin'>Admin Area</a>";
                    echo "<button onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Gesperrt */
                if($_SESSION['role'] == 2){
                    echo "<button class='active' onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Normaler Benutzer */
                if($_SESSION['role'] == 0) {
                    echo "<button class='active' onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button onclick='zeiterfassung()'>Time recording <i class='fas fa-clock'></i></button>";
                    echo "<button onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
            }
            ?>
        </div>
    </nav>

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
                <input type="password" id="password_register" name="password" placeholder="Enter Email"><br>
                <label for="verypass">Verify Password:</label><br>
                <input type="password" id="verypass" name="verypass" placeholder="Enter Password again"><br><br>
                <input type="submit" value="Register">
            </form>
        </div>
    </div>

    <?php include('app/Views/footer.view.php'); ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/login.js"></script>
    <script src="public/js/main.js"></script>
</body>

</html>