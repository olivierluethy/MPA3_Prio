<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/home.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
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
                    echo "<button class='logoutBtn' onclick='zuLogout()'>Ausloggen  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Normaler Benutzer */
                if($_SESSION['role'] == 0) {
                    echo "<button class='taskBtn' onclick='aufgaben()'>Aufgaben <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button class='timeRecord' onclick='bild_hinzufuegen()'>Zeiterfassungen <i class='fas fa-clock'></i></button>";
                    echo "<button class='logoutBtn' onclick='zuLogout()'>Ausloggen  <i class='fas fa-sign-out-alt'></i></button>";
                }
            }
            ?>
        </div>
    </nav>

    <div class="essay">
        <?php
        echo "<h1><u>". $getEssay[0][1] . "</u></h1>
              <td><textarea readonly class='ckeditor' id='essay'>"?><?php echo $getEssay[0][2] . "</textarea></td>
              <div class='acre'>
                <button class='acbtn' onclick='accept(" . $getEssay[0][0] . ", " . $getEssay[0][4] . ")'>Accept</button>
                <button class='rebtn' onclick='refuse(" . $getEssay[0][0] . ", " . $getEssay[0][4] . ")'>Refuse</button>
              </div>";
        ?>
    </div>

    <script src="ckeditor/ckeditor.js"></script>
    
    <script>
    CKEDITOR.replace('essay');
    </script>

    <?php include('app/Views/footer.view.php'); ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/validationBenutzerBearbeiten.js"></script>
</body>

</html>