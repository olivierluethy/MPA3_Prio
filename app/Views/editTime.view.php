<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/add.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <title>Benutzer bearbeiten</title>
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
                echo "<button class='loginBtn' onclick='goToLogin()'>Einloggen  <i class='fas fa-sign-in-alt'></i></button>";
            }else{
                /* Admin */
                if($_SESSION['role'] == 1){
                    echo "<a href='admin_bereich'>Admin Bereich</a>";
                    echo "<button onclick='zuLogout()'>Ausloggen  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Normaler Benutzer */
                if($_SESSION['role'] == 0) {
                    echo "<button onclick='aufgaben()'>Aufgaben <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button class='active' onclick='zeiterfassung()'>Zeiterfassungen <i class='fas fa-clock'></i></button>";
                    echo "<button onclick='zuLogout()'>Ausloggen  <i class='fas fa-sign-out-alt'></i></button>";
                }
            }
            ?>
        </div>
    </nav>

    <form action="edit_Time?id=<?= $getTime[0][0] ?>" method="POST">
        <h2>Edit time record</h2>
        <label for="passwort">Time:</label><br>
        <input id="appt-time" type="time" name="time" step="2" value="<?= $getTime[0][1] ?>" /><br>
        <input type="submit" value="Edit time"><br>
    </form>

    <?php include('app/Views/footer.view.php'); ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/validationBenutzerBearbeiten.js"></script>
</body>

</html>