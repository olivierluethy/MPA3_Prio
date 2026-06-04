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
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/openDone.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Home</title>
</head>
<body class="bg-surface-900 text-surface-100">
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include "header.php";
    ?>

    <?php
    if ($_SESSION["role"] === $blockedRole) { ?>
        <!-- Benutzer mit gesperrter Rolle -->
        <div class='write_essay mx-auto max-w-2xl px-4 py-10 text-center'>
            <h1 class="text-2xl font-bold text-red-400">You don't have access anymore!</h1>
            <p class="mt-3 text-surface-300">You have completed a task 10 times too late. That's why you don't have access anymore.</p>
            <button id='showEssayField' onclick='write_essay()' class="btn-primary mt-6">Write an essay to get access again</button>
            <form id='essay' action='add_essay' method='POST' class="card mt-8 hidden text-left">
                <label class="label">Title:</label>
                <input id='title' type='text' name='title' class="input"><br>
                <label class="label mt-4">Essay:</label>
                <textarea name='essay' id='essay_content' class="input"></textarea><br>
                <button type='submit' class="btn-primary mt-4">Send essay</button>
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
        <div class="mx-auto max-w-7xl px-4 py-6">
            <!-- Schalter für offene und abgeschlossene Aufgaben -->
            <div class='switch mx-auto mb-8 flex w-full max-w-md gap-2 rounded-full border border-solid border-surface-700 bg-surface-800 p-1.5 shadow-lg'>
                <button id='openButton' onclick='navSwitch(1)'
                        class="flex-1 cursor-pointer rounded-full border-0 bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition-colors">
                    Open
                    <?php echo ($open_tasks_counter == 0) ? "(empty)" : "($open_tasks_counter)"; ?>
                </button>
                <button id='doneButton' onclick='navSwitch(2)'
                        class="flex-1 cursor-pointer rounded-full border-0 bg-surface-800 px-6 py-3 text-sm font-semibold text-surface-400 transition-colors">
                    Completed
                    <?php echo ($done_tasks_counter == 0) ? "(empty)" : "($done_tasks_counter)"; ?>
                </button>
            </div>

            <!-- Offene Aufgaben -->
            <div id='open'>
                <?php if ($open_tasks_counter > 0) { ?>
                    <div class="order mb-4 flex flex-wrap items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-white">All open tasks sorted by:</h2>
                        <div class="flex items-center gap-3">
                            <form action="" method="GET" class="flex items-center gap-2">
                                <select name="sort" class="rounded-lg border border-solid border-surface-600 bg-surface-900 px-3 py-2 text-sm text-surface-100 focus:border-brand-500 focus:outline-none">
                                    <option value="priority" <?= ($_GET["sort"] ?? '') == "priority" ? "selected" : "" ?>>Priority</option>
                                    <option value="alphabet" <?= ($_GET["sort"] ?? '') == "alphabet" ? "selected" : "" ?>>Alphabet</option>
                                    <option value="deadline" <?= ($_GET["sort"] ?? '') == "deadline" ? "selected" : "" ?>>Deadline</option>
                                </select>
                                <button type='submit' class="btn-primary">Sort <i class='fa fa-sort'></i></button>
                            </form>
                            <button class='plus btn-primary !h-12 !w-12 !rounded-full !p-0' title="Add task" onclick='addTask()'><i class='fa fa-plus'></i></button>
                        </div>
                    </div>
                    <main class="space-y-6">
                        <?php foreach ($getObjects as $task) {
                            $iv = base64_decode($task['iv']);
                            $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                            if ($decrypted_status !== '0') continue;
                        ?>
                        <div class='task overflow-x-auto rounded-xl border border-solid border-surface-700 bg-surface-800 p-5 shadow-lg'>
                            <table class="w-full">
                                <tr class="align-top">
                                    <th class="px-3 py-2 text-left"><h1 class="text-lg font-semibold text-white"><?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></h1></th>
                                    <th class="px-3 py-2 text-left font-normal">
                                        <textarea readonly class='ckeditor' name='description' id='description_open'>
                                            <?php echo htmlspecialchars_decode(decrypt($task["beschreibung"], $encryption_key, $iv)) ?>
                                        </textarea>
                                    </th>
                                    <th class="px-3 py-2 text-left font-normal">
                                        <textarea readonly class='ckeditor' name='motivation' id='motivation_open'>
                                            <?php echo htmlspecialchars_decode(decrypt($task["motivation"], $encryption_key, $iv)) ?>
                                        </textarea>
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-2 text-sm text-surface-300"><i class="fas fa-calendar-days mr-1"></i><?php echo htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></th>
                                    <th class="px-3 py-2">
                                        <?php if (strtotime(decrypt($task["created_at"], $encryption_key, $iv)) >= strtotime("-1 day")){ ?>
                                            <!-- Task is younger than 24 hours -->
                                            <div class="flex items-center justify-center gap-2">
                                                <img title='Edit task' onclick='editTask(<?= $task["aufgabeId"] ?>)' src='images/edit.png' alt='Edit' class="h-6 w-6 cursor-pointer transition hover:scale-110">
                                                <img title='Delete task' onclick='deleteTask(<?= $task["aufgabeId"]?>)' src='images/delete.png' alt='Delete' class="h-6 w-6 cursor-pointer transition hover:scale-110">
                                            </div>
                                        <?php } ?>
                                    </th>
                                    <th class="px-3 py-2">
                                        <h1 id='active_time<?php echo $task["aufgabeId"]; ?>' class="whitespace-nowrap font-mono text-lg font-semibold text-brand-300">00:00:00</h1>
                                    </th>
                                    <th class="px-3 py-2">
                                        <img title='Start recording' id='start<?php echo $task["aufgabeId"]; ?>' onclick="start_recording(<?= $task['aufgabeId']; ?>)" src='images/clock off.png' alt='Start recording' class="mx-auto h-9 w-9 cursor-pointer transition hover:scale-110">
                                    </th>
                                    <th class="px-3 py-2">
                                        <button title='Complete task' id='completeBtn' onclick="completeTask(<?= $task['aufgabeId']; ?>)"
                                                class="inline-flex cursor-pointer items-center gap-2 whitespace-nowrap rounded-lg border-0 bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-500">
                                            Complete task <i class='fa fa-check'></i>
                                        </button>
                                    </th>
                                    <th class="px-3 py-2">
                                        <div class="flex flex-col items-center gap-1">
                                            <img title='Increase priority' onclick="higherPrio(<?=$task['aufgabeId']?>)"
                                                src='images/up.png' alt='' class="h-5 w-5 cursor-pointer transition hover:scale-110">
                                            <p class="badge bg-surface-700 text-surface-100"><?= htmlspecialchars(decrypt($task['prioritaet'], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php if (decrypt($task['prioritaet'], $encryption_key, $iv) > 0) { ?>
                                            <img title='Decrease priority' onclick="lowerPrio(<?=$task['aufgabeId']?>)"
                                                src='images/down.png' alt='' class="h-5 w-5 cursor-pointer transition hover:scale-110">
                                            <?php } ?>
                                        </div>
                                    </th>
                                </tr>
                            </table>
                        </div>
                        <?php } ?>
                    </main>
                <?php } else { ?>
                    <div class='noData flex flex-col items-center gap-4 py-16 text-center'>
                        <h1 class="text-2xl font-bold text-red-400">No tasks available yet</h1>
                        <button onclick='addTask()' class="btn-primary">Add task &nbsp;<i class='fa fa-plus'></i></button>
                    </div>
                <?php } ?>
            </div>

            <!-- Abgeschlossene Aufgaben -->
            <div id='done'>
                <?php if ($done_tasks_counter > 0) { ?>
                    <div class='order mb-4'>
                        <h2 class="text-lg font-semibold text-white">All done tasks</h2>
                    </div>
                    <main class="space-y-6">
                        <?php foreach ($getObjects as $task) {
                            $iv = base64_decode($task['iv']);
                            $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                            if ($decrypted_status !== '1') continue;
                        ?>
                            <div class='task overflow-x-auto rounded-xl border border-solid border-surface-700 bg-surface-800 p-5 shadow-lg opacity-90'>
                                <table class="w-full">
                                    <tr class="align-top">
                                        <th class="px-3 py-2 text-left"><h1 class="text-lg font-semibold text-white"><?php echo htmlspecialchars(decrypt($task["titel"], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></h1></th>
                                        <th class="px-3 py-2 text-left font-normal">
                                            <textarea readonly class='ckeditor' name='description' id='description_open'>
                                                <?php echo htmlspecialchars_decode(decrypt($task["beschreibung"], $encryption_key, $iv)) ?>
                                            </textarea>
                                        </th>
                                        <th class="px-3 py-2 text-left font-normal">
                                            <textarea readonly class='ckeditor' name='motivation' id='motivation_open'>
                                                <?php echo htmlspecialchars_decode(decrypt($task["motivation"], $encryption_key, $iv)) ?>
                                            </textarea>
                                        </th>
                                        <th class="whitespace-nowrap px-3 py-2 text-sm text-surface-300"><i class="fas fa-calendar-days mr-1"></i><?php echo htmlspecialchars(date("dS M Y", strtotime(decrypt($task["deadline"], $encryption_key, $iv))), ENT_QUOTES, 'UTF-8'); ?></th>
                                        <th class="px-3 py-2">
                                            <span class="badge bg-green-600/20 text-green-300"><i class="fa fa-check mr-1"></i>Done</span>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        <?php } ?>
                    </main>
                <?php } else { ?>
                    <div class='noData flex flex-col items-center gap-4 py-16 text-center'>
                        <h1 class="text-2xl font-bold text-surface-300">No tasks completed yet</h1>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <script src="ckeditor/ckeditor.js"></script>
    <script>
    ['essay_content', 'motivation_open', 'description_open', 'motivation_done', 'description_done'].forEach(function(id) {
        CKEDITOR.replace(id);
    });
    </script>

    <?php include "app/Views/addRapportModal.view.php"; ?>
    <?php include "app/Views/footer.view.php"; ?>
</body>
</html>
