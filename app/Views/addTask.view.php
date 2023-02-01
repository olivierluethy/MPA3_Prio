<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/add.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Add Task</title>
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

    <form action="add_task" method="POST">
        <h2>Add Task</h2>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" id="titel"><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description"></textarea><br>
        <label for="motivation">Motivation:</label><br>
        <textarea name="motivation" id="motivation"></textarea><br>
        <label for="lname">Deadline:</label><br>
        <input type="date" id="deadline" name="deadline"><br>
        <label for="lname">Priority:</label><br>
        <input type="number" id="priority" name="priority" id="prioritaet"><br>
        <input type="submit" class="" name="addTask" value="Add task"><br><br>
    </form>

    <script src="public/js/validationAddTask.js"></script>
    <script src="ckeditor/ckeditor.js"></script>
    <script src="public/js/routes.js"></script>

    <script>
    CKEDITOR.replace('description');
    CKEDITOR.replace('motivation');
    </script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>