<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/zeituebersicht.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <title>Time Records</title>
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
                    echo "<button onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Normaler Benutzer */
                if($_SESSION['role'] == 0) {
                    echo "<button onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button class='active' onclick='zeiterfassung()'>Time recording <i class='fas fa-clock'></i></button>";
                    echo "<button onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
            }
            ?>
        </div>
    </nav>

    <!-- Zeiterfassungen -->
    <?php
    if($getAllTimesCounter > 0){
        echo "
        <table class='leiste'>
            <tr>
                <th><h2>Time overview</h2></th>
                <th></th>
                <th></th>
            </tr>
        </table>
        <table class='data'>
            <tr style='border-radius: 30px;'>
                <td>Task</td>
                <td>Description</td>
                <td>Recorded Time</td>
                <td>Date</td>
                <td>Edit</td>
                <td>Delete</td>
            </tr>";
        if($getTimesUnderADayCounter > 0){
            foreach ($getTimesUnderADay as $getTimesUnderADay2){
            echo "<tr>
                <td>". $getTimesUnderADay2['titel'] . "</td>
                <td><textarea readonly class='ckeditor' name='description' id='description'>"?><?php echo $getTimesUnderADay2['beschreibung'] . "</textarea></td>
                <td>". $getTimesUnderADay2['zeit'] . "</td>";

                $date = date('dS M Y', strtotime($getTimesUnderADay2['created_at']))?>
                <td><?php echo $date ?></td>

                <?php echo "
                <td><button title='Change Time' onclick='editTime(". $getTimesUnderADay2['zeiteintraegeId'] . ")'><img src='images/edit.png' alt=''></button></td>
                <td><button title='Delete Time' onclick='deleteTime(". $getTimesUnderADay2['zeiteintraegeId'] . ")'><img src='images/delete.png' alt=''></button></td>
                </tr>";
            }
        }
        if($getTimesOverADayCounter > 0){
            foreach ($getTimesOverADay as $getTimesOverADay2){
            echo "<tr>
                <td>". $getTimesOverADay2['titel'] . "</td>
                <td><textarea readonly class='ckeditor' name='description' id='description'>"?><?php echo $getTimesOverADay2['beschreibung'] . "</textarea></td>
                <td>". $getTimesOverADay2['zeit'] . "</td>
                <td>". $getTimesOverADay2['created_at'] . "</td>
                <td>Can't be edited anymore</td>
                <td>Can't be deleted anymore</td>
                </tr>";
            }
        }
        echo "</table>";
    }else {
        echo "<div class='noData'>
                <h1>Sorry, no time entries available yet</h1>
                <p>Select a task and click on the clock to create one!</p>
              </div>";
    }?>

    <script src="ckeditor/ckeditor.js"></script>
    
    <script>
    CKEDITOR.replace('description');
    </script>

    <script src="public/js/routes.js"></script>
    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>