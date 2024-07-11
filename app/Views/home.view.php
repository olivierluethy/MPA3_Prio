<?php
// Function to decrypt data
function decrypt($data, $key, $iv) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        error_log("Decryption error: " . openssl_error_string()); // Log OpenSSL errors
        return 'Decryption error';
    }
    return $decrypted;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/home.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/openDone.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/footer.js"></script>
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include "header.php";
    ?>

    <?php if ($_SESSION["role"] == hash_hmac('sha256', 2, $salt)) { ?>
        <div class='write_essay'>
            <h1>You don't have access anymore!</h1>
            <p>You have completed a task 10 times too late. That's why you don't have access anymore.</p>
            <button id='showEssayField' onclick='write_essay()'>Write an essay to get access again</button>
            <form id='essay' action='add_essay' method='POST'>
                <label>Title:</label><br>
                <input id='title' type='text' name='title'><br>
                <textarea name='essay' id='essay_content'></textarea><br>
                <button type='submit'>Send essay</button>
            </form>
        </div>
    <?php } elseif ($_SESSION["role"] == hash_hmac('sha256', 0, $salt)) { 
        $open_tasks_counter = 0;
        $done_tasks_counter = 0;

        foreach ($getObjects as $object) {
            $iv = base64_decode($object['iv']);
            $decrypted_status = decrypt($object['status'], $encryption_key, $iv);
            if ($decrypted_status === '0') {
                $open_tasks_counter++;
            } elseif ($decrypted_status === '1') {
                $done_tasks_counter++;
            }
        }
        ?>

        <table class='switch'>
            <tr>
                <th>
                    <button id='openButton' onclick='navSwitch(1)'>
                        Open (<?= $open_tasks_counter ?: 'empty' ?>)
                    </button>
                </th>
                <th></th>
                <th>
                    <button id='doneButton' onclick='navSwitch(2)'>
                        Completed (<?= $done_tasks_counter ?: 'empty' ?>)
                    </button>
                </th>
            </tr>
        </table>

        <!-- Open Tasks -->
        <div id='open'>
            <?php if ($open_tasks_counter > 0) { ?>
                <table class="order">
                    <tr>
                        <td><h2>All open tasks sorted by:</h2></td>
                        <td>
                            <form action="" method="GET">
                                <select name="sort">
                                    <option value="priority" <?= (isset($_GET["sort"]) && $_GET["sort"] == "priority") ? "selected" : "" ?>>Priority</option>
                                    <option value="alphabet" <?= (isset($_GET["sort"]) && $_GET["sort"] == "alphabet") ? "selected" : "" ?>>Alphabet</option>
                                    <option value="deadline" <?= (isset($_GET["sort"]) && $_GET["sort"] == "deadline") ? "selected" : "" ?>>Deadline</option>
                                </select>
                                <button title='Sort all tasks' type='submit'>Sort <i class='fa fa-sort'></i></button>
                            </form>
                        </td>
                        <td><button title='Click on this button to add a task' class='plus' onclick='addTask()'><i class='fa fa-plus'></i></button></td>
                    </tr>
                </table>

                <main>
                    <?php 
                    $open_task_index = 0;
                    foreach ($getObjects as $task) { 
                        $iv = base64_decode($task['iv']);
                        $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                        if ($decrypted_status !== '0') continue; // Skip non-open tasks
                        
                        $rowClass = $open_task_index % 2 === 0 ? "task-even" : "task-odd";
                        $open_task_index++;
                    ?>
                        <div class='task <?= $rowClass ?>'>
                            <table>
                                <tr>
                                    <?php echo "Titel: " . $task['titel'];?><br>
                                    <?php echo "IV: " . $task['iv'];?><br>
                                    <?php echo "Key: " . $encryption_key;?><br>
                                    <th><h1><?= decrypt($task["titel"], $encryption_key, $iv) ?></h1></th>
                                    <th><textarea readonly class="ckeditor" name="description" id="description_open"><?= decrypt($task["beschreibung"], $encryption_key, $iv) ?></textarea></th>
                                    <th><textarea readonly class="ckeditor" name="motivation" id="motivation_open"><?= decrypt($task["motivation"], $encryption_key, $iv) ?></textarea></th>
                                    <th><?= date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))) ?></th>
                                    <?php if (strtotime(decrypt($task["created_at"], $encryption_key, $iv)) >= strtotime("-1 day")) { ?>
                                        <th>
                                            <img title='Edit task' onclick='editTask(<?= $task["aufgabeId"] ?>)' src='images/edit.png' alt=''>
                                            <img title='Delete task' onclick='deleteTask(<?= $task["aufgabeId"] ?>)' src='images/delete.png' alt=''>
                                        </th>
                                    <?php } else { ?>
                                        <th></th>
                                    <?php } ?>
                                    <th><h1 id='active_time<?= $task["aufgabeId"] ?>'>00:00:00</h1></th>
                                    <th><img title='Start recording' id='start<?= $task["aufgabeId"] ?>' onclick="start_recording(<?= $task["aufgabeId"] ?>)" src='images/clock off.png' alt=''></th>
                                    <th><button title='Complete task' id='completeBtn' onclick="completeTask(<?= $task["aufgabeId"] ?>)">Complete task <i class='fa fa-check'></i></button></th>
                                    <th>
                                        <img title='Increase priority' onclick="higherPrio(<?= $task["aufgabeId"] ?>)" src='images/up.png' alt=''><br>
                                        <p><?= decrypt($task["prioritaet"], $encryption_key, $iv) ?></p>
                                        <?php if (decrypt($task["prioritaet"], $encryption_key, $iv) > 0) { ?>
                                            <img title='Decrease priority' onclick="lowerPrio(<?= $task["aufgabeId"] ?>)" src='images/down.png' alt=''>
                                        <?php } ?>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                </main>
            <?php } else { ?>
                <div class='noData'>
                    <h1>No tasks available yet</h1>
                    <button onclick='addTask()'>Add task &nbsp<i class='fa fa-plus'></i></button>
                </div>
            <?php } ?>
        </div>

        <!-- Completed Tasks -->
        <div id='done'>
            <?php if ($done_tasks_counter > 0) { ?>
                <table class='order'>
                    <tr>
                        <td><h2>All done tasks</h2></td>
                    </tr>
                </table>
                <main>
                    <?php 
                    foreach ($getObjects as $task) { 
                        $iv = base64_decode($task['iv']);
                        $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                        if ($decrypted_status !== '1') continue; // Skip non-completed tasks
                    ?>
                        <div class='task'>
                            <table>
                                <tr>
                                    <th><h1><?= decrypt($task["titel"], $encryption_key, $iv) ?></h1></th>
                                    <th><textarea readonly class='ckeditor' name='description' id='description_done'><?= decrypt($task["beschreibung"], $encryption_key, $iv) ?></textarea></th>
                                    <th><textarea readonly class='ckeditor' name='motivation' id='motivation_done'><?= decrypt($task["motivation"], $encryption_key, $iv) ?></textarea></th>
                                    <th><?= date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))) ?></th>
                                    <th><img title='Delete task' onclick='deleteTask(<?= $task["aufgabeId"] ?>)' src='images/delete.png' alt=''></th>
                                    <th>
                                        <h1><?= decrypt($task["total_time"], $encryption_key, $iv) ?></h1>
                                        <h2>Total time</h2>
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                </main>
            <?php } else { ?>
                <div class='noData'>
                    <h1>No tasks completed yet</h1>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <script src="ckeditor/ckeditor.js"></script>

        <script>
        CKEDITOR.replace('essay_content');
        </script>
    <?php include "app/Views/footer.view.php"; ?>
</body>

</html>