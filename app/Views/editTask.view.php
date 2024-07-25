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

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validationAddTask.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Edit Task</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <form action="edit_task?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" method="POST">
        <h2>Edit Task</h2>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?= $title ?>"><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description"><?= $description ?></textarea><br>
        <label for="motivation">Motivation:</label><br>
        <textarea name="motivation" id="motivation"><?= $motivation ?></textarea><br>
        <label for="deadline">Deadline:</label><br>
        <input type="date" id="deadline" name="deadline" value="<?= $deadline ?>"><br>
        <label for="priority">Select the Priority:</label><br>
        <select id="priority" name="priority" style="width:200px;height:25px;">
            <?php foreach ($possiblePriorities as $possiblePriority): ?>
                <option value="<?= htmlspecialchars($possiblePriority, ENT_QUOTES, 'UTF-8'); ?>" <?= ($possiblePriority == $priority) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($possiblePriority, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <input type="submit" name="addTask" value="Edit task"><br><br>
    </form>

    <script src="ckeditor/ckeditor.js"></script>

    <script>
    CKEDITOR.replace('description');
    CKEDITOR.replace('motivation');
    </script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>