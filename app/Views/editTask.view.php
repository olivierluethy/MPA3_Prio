<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validationAddTask.js"></script>
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Edit Task</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <main class="mx-auto max-w-2xl px-4 py-10">
        <form action="edit_task?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" method="POST" class="card space-y-4">
            <h2 class="text-2xl font-bold text-white">Edit Task</h2>
            <div>
                <label for="title" class="label">Title:</label>
                <input type="text" id="title" name="title" value="<?= $title ?>" class="input">
            </div>
            <div>
                <label for="description" class="label">Description:</label>
                <textarea name="description" id="description"><?= $description ?></textarea>
            </div>
            <div>
                <label for="motivation" class="label">Motivation:</label>
                <textarea name="motivation" id="motivation"><?= $motivation ?></textarea>
            </div>
            <div>
                <label for="deadline" class="label">Deadline:</label>
                <input type="date" id="deadline" name="deadline" value="<?= $deadline ?>" class="input">
            </div>
            <div>
                <label for="priority" class="label">Select the Priority:</label>
                <select id="priority" name="priority" class="input">
                    <?php foreach ($possiblePriorities as $possiblePriority): ?>
                        <option value="<?= htmlspecialchars($possiblePriority, ENT_QUOTES, 'UTF-8'); ?>" <?= ($possiblePriority == $priority) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($possiblePriority, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" name="addTask" value="Edit task" class="btn-primary w-full">
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
