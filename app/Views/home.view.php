<?php
// Funktion zur Entschlüsselung
function decrypt($data, $key, $iv) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
    }
    return $decrypted;
}
// Rollenwerte vorab berechnen
$blockedRole = hash_hmac('sha256', 2, $salt);
$normalRole = hash_hmac('sha256', 0, $salt);
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

    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/openDone.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include "header.php";
    ?>

<?php
 if ($_SESSION["role"] === $blockedRole) { ?>
    <!-- Benutzer mit gesperrter Rolle -->
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
<?php } elseif ($_SESSION["role"] === $normalRole) { ?>
    <!-- Benutzer mit normaler Rolle -->
    <?php
    // Aufgaben zählen
    $open_tasks_counter = 0;
    $done_tasks_counter = 0;

    foreach ($getObjects as $task) {
        $iv = base64_decode($task['iv']);
        $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
        if ($decrypted_status === '0') {
            $open_tasks_counter++;
        } elseif ($decrypted_status === '1') {
            $done_tasks_counter++;
        }
    }
    ?>

    <!-- Schalter für offene und abgeschlossene Aufgaben -->
    <table class='switch'>
        <tr>
            <th>
                <button id='openButton' onclick='navSwitch(1)'>
                    Open
                    <?php echo ($open_tasks_counter == 0) ? "(empty)" : "($open_tasks_counter)"; ?>
                </button>
            </th>
            <th></th>
            <th>
                <button id='doneButton' onclick='navSwitch(2)'>
                    Completed
                    <?php echo ($done_tasks_counter == 0) ? "(empty)" : "($done_tasks_counter)"; ?>
                </button>
            </th>
            <th></th>
        </tr>
    </table>

    <!-- Offene Aufgaben -->
    <div id='open'>
        <?php if ($open_tasks_counter > 0) { ?>
            <table class="order">
                <tr>
                    <td><h2>All open tasks sorted by:</h2></td>
                    <td>
                        <form action="" method="GET">
                            <select name="sort">
                                <option value="priority" <?= ($_GET["sort"] ?? '') == "priority" ? "selected" : "" ?>>Priority</option>
                                <option value="alphabet" <?= ($_GET["sort"] ?? '') == "alphabet" ? "selected" : "" ?>>Alphabet</option>
                                <option value="deadline" <?= ($_GET["sort"] ?? '') == "deadline" ? "selected" : "" ?>>Deadline</option>
                            </select>
                            <button type='submit'>Sort <i class='fa fa-sort'></i></button>
                        </form>
                    </td>
                    <td><button class='plus' onclick='addTask()'><i class='fa fa-plus'></i></button></td>
                </tr>
            </table>

            <main>
                <?php foreach ($getObjects as $task) {
                    $iv = base64_decode($task['iv']);
                    $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                    if ($decrypted_status !== '0') continue;
                ?>
                <div class='task'>
                    <table>
                        <tr>
                            <th><h1><?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></h1></th>
                            <th>
                                <textarea readonly class='ckeditor' name='description' id='description_open'>
                                    <?php echo decrypt($task["beschreibung"], $encryption_key, $iv); ?>
                                </textarea>
                            </th>
                            <th>
                                <textarea readonly class='ckeditor' name='motivation' id='motivation_open'>
                                    <?php echo decrypt($task["motivation"], $encryption_key, $iv); ?>
                                </textarea>
                            </th>


                            <th><?php echo htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></th>
                            <th>
                                <?php if (strtotime(decrypt($task["created_at"], $encryption_key, $iv)) >= strtotime("-1 day")){ ?>
                                    <!-- Task is younger than 24 hours -->
                                    <img title='Edit task' onclick='editTask(<?= $task["aufgabeId"] ?>)' src='images/edit.png' alt='Edit'>
                                    <img title='Delete task' onclick='deleteTask(<?= $task["aufgabeId"]?>)' src='images/delete.png' alt='Delete'>
                                <?php } ?>
                            </th>
                            <th>
                                <h1 id='active_time<?php echo $task["aufgabeId"]; ?>'>00:00:00</h1>
                            </th>
                            <th>
                                <img title='Start recording' id='start<?php echo $task["aufgabeId"]; ?>' onclick="start_recording(<?= $task['aufgabeId']; ?>)" src='images/clock off.png' alt='Start recording'>
                            </th>
                            <th>
                                <button title='Complete task' id='completeBtn' onclick="completeTask(<?= $task['aufgabeId']; ?>)">
                                    Complete task <i class='fa fa-check'></i>
                                </button>
                            </th>
                            <th>
                                <img title='Increase priority' onclick="higherPrio(<?=$task['aufgabeId']?>)"
                                    src='images/up.png' alt=''><br>
                                <p><?= htmlspecialchars(decrypt($task['prioritaet'], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php if (decrypt($task['prioritaet'], $encryption_key, $iv) > 0) { ?>
                                <img title='Decrease priority' onclick="lowerPrio(<?=$task['aufgabeId']?>)"
                                    src='images/down.png' alt=''>
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

    <!-- Abgeschlossene Aufgaben -->
    <div id='done'>
        <?php if ($done_tasks_counter > 0) { ?>
            <table class='order'>
                <tr>
                    <td><h2>All done tasks</h2></td>
                </tr>
            </table>
            <main>
                <?php foreach ($getObjects as $task) {
                    $iv = base64_decode($task['iv']);
                    $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                    if ($decrypted_status !== '1') continue; // Nur abgeschlossene Aufgaben anzeigen
                ?>
                    <div class='task'>
                        <table>
                            <tr>
                                <th><h1><?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></h1></th>
                                <th><textarea readonly class='ckeditor' name='description' id='description_done'><?php echo htmlspecialchars(decrypt($task["beschreibung"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></textarea></th>
                                <th><?php echo htmlspecialchars(decrypt($task["motivation"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></th>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </main>
        <?php } else { ?>
            <div class='noData'>
                <h1>No tasks completed yet</h1>
                <p>When you complete an open task, it will appear here!</p>
            </div>
        <?php } ?>
    </div>
<?php } ?>
        
        <script src="ckeditor/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('essay_content');
            CKEDITOR.replace('motivation_open');
            CKEDITOR.replace('description_open');
        </script>

        <?php include "app/Views/addRapportModal.view.php"; ?>
        <?php include "app/Views/footer.view.php"; ?>
</body>

</html>
