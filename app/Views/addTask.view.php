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
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Add Task</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");
    ?>

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
    <script src="public/js/footer.js"></script>

    <script>
    CKEDITOR.replace('description');
    CKEDITOR.replace('motivation');
    </script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>