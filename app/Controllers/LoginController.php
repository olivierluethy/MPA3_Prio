<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
</head>
<body>
    <!-- Navigation Bar -->
    <?php include "header.php"; ?>

    <?php if ($_SESSION["role"] === $blockedRole) { ?>
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
        <?php if (count($getObjects) > 0) { ?>
            <!-- Schalter für offene und abgeschlossene Aufgaben -->
            <table class='switch'>
                <tr>
                    <th><button id='openButton' onclick='navSwitch(1)'>Open (<?= $open_tasks_counter ?>)</button></th>
                    <th></th>
                    <th><button id='doneButton' onclick='navSwitch(2)'>Completed (<?= $done_tasks_counter ?>)</button></th>
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
                            if ($decrypted_status !== '0') continue; // Nur offene Aufgaben anzeigen
                        ?>
                            <div class='task'>
                                <table>
                                    <tr>
                                        <th>Title: <?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></th>
                                        <td><?php echo htmlspecialchars(decrypt($task["beschreibung"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(decrypt($task["motivation"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo "Datum: " . htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></td>
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
                                        <th>Title: <?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></th>
                                        <td><?php echo htmlspecialchars(decrypt($task["beschreibung"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(decrypt($task["motivation"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo "Datum: " . htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        <?php } ?>
                    </main>
                <?php } else { ?>
                    <div class='noData'>
                        <h1>No done tasks yet</h1>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <!-- Fallback: Keine Aufgaben gefunden -->
            <div class='noData'>
                <h1>No tasks available yet</h1>
                <button onclick='addTask()'>Add task &nbsp<i class='fa fa-plus'></i></button>
            </div>
        <?php } ?>
    <?php } ?>

    <!-- Footer -->
    <?php include "footer.php"; ?>
</body>
</html>
