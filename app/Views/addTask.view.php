<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/validationAddTask.js"></script>
    <script defer src="public/js/routes.js"></script>
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Add Task</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <main class="mx-auto max-w-2xl px-4 py-10">
        <form action="add_task" method="POST" class="card space-y-4">
            <h2 class="text-2xl font-bold text-white">Add Task</h2>
            <div>
                <label for="title" class="label">Title:</label>
                <input type="text" id="title" name="title" class="input">
            </div>
            <div>
                <label for="description" class="label">Description:</label>
                <textarea name="description" id="description"></textarea>
            </div>
            <div>
                <label for="motivation" class="label">Motivation:</label>
                <textarea name="motivation" id="motivation"></textarea>
            </div>
            <div>
                <label for="deadline" class="label">Deadline:</label>
                <input type="date" id="deadline" name="deadline" class="input">
            </div>
            <div>
                <label for="priority" class="label">Select the Priority:</label>
                <select id="priority" name="priority" class="input">
                    <?php foreach ($possiblePriorities as $priority): ?>
                        <option value="<?php echo $priority; ?>"><?php echo $priority; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" class="btn-primary w-full" name="addTask" value="Add task">
        </form>
    </main>

    <script src="ckeditor/ckeditor.js"></script>

    <script>
    ['description', 'motivation'].forEach(function(id) {
        CKEDITOR.replace(id);
    });
    </script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
