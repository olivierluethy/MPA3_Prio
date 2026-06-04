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
    <script defer src="public/js/taskModal.js"></script>
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
                                <label for="sort" class="sr-only">Sort tasks by</label>
                                <select id="sort" name="sort" onchange="this.form.submit()" aria-label="Sort tasks by"
                                        class="rounded-lg border border-solid border-surface-600 bg-surface-900 px-3 py-2 text-sm text-surface-100 focus:border-brand-500 focus:outline-none">
                                    <option value="priority" <?= ($_GET["sort"] ?? '') == "priority" ? "selected" : "" ?>>Priority</option>
                                    <option value="alphabet" <?= ($_GET["sort"] ?? '') == "alphabet" ? "selected" : "" ?>>Alphabet</option>
                                    <option value="deadline" <?= ($_GET["sort"] ?? '') == "deadline" ? "selected" : "" ?>>Deadline</option>
                                </select>
                                <noscript><button type="submit" class="btn-primary">Sort</button></noscript>
                            </form>
                            <button type="button" class='plus btn-primary !h-12 !w-12 !rounded-full !p-0' title="Add task" aria-label="Add task" onclick='openTaskModal()'><?= icon('plus', 'h-5 w-5') ?></button>
                        </div>
                    </div>
                    <main class="space-y-6">
                        <?php foreach ($getObjects as $task) {
                            $iv = base64_decode($task['iv']);
                            $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                            if ($decrypted_status !== '0') continue;

                            // Decrypted values for the in-page edit modal (data-* attributes)
                            $t_title    = decrypt($task["titel"], $encryption_key, $iv);
                            $t_desc     = htmlspecialchars_decode(decrypt($task["beschreibung"], $encryption_key, $iv));
                            $t_motiv    = htmlspecialchars_decode(decrypt($task["motivation"], $encryption_key, $iv));
                            $t_deadline = decrypt($task["deadline"], $encryption_key, $iv);
                            $t_prio     = decrypt($task['prioritaet'], $encryption_key, $iv);
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
                                                <button type="button" title="Edit task" aria-label="Edit task" onclick="openEditTask(this)"
                                                        data-id="<?= (int) $task['aufgabeId'] ?>"
                                                        data-title="<?= htmlspecialchars($t_title, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-description="<?= htmlspecialchars($t_desc, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-motivation="<?= htmlspecialchars($t_motiv, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-deadline="<?= htmlspecialchars($t_deadline, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-priority="<?= htmlspecialchars($t_prio, ENT_QUOTES, 'UTF-8') ?>"
                                                        class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-surface-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                                    <?= icon('pencil', 'h-5 w-5') ?>
                                                </button>
                                                <button type="button" title="Delete task" aria-label="Delete task" onclick='deleteTask(<?= $task["aufgabeId"]?>)'
                                                        class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-red-300 transition-colors hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <?= icon('trash', 'h-5 w-5') ?>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </th>
                                    <th class="px-3 py-2">
                                        <h1 id='active_time<?php echo $task["aufgabeId"]; ?>' class="whitespace-nowrap font-mono text-lg font-semibold text-brand-300">00:00:00</h1>
                                    </th>
                                    <th class="px-3 py-2">
                                        <button type="button" id='start<?php echo $task["aufgabeId"]; ?>' onclick="start_recording(<?= $task['aufgabeId']; ?>)" title="Start recording" aria-label="Start recording"
                                                class="time-toggle mx-auto inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-solid border-surface-600 bg-surface-900 text-brand-300 transition-colors hover:border-brand-500 hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                            <?= icon('play', 'icon-play h-5 w-5') ?>
                                            <?= icon('stop', 'icon-stop h-5 w-5') ?>
                                        </button>
                                    </th>
                                    <th class="px-3 py-2">
                                        <button title='Complete task' id='completeBtn' onclick="completeTask(<?= $task['aufgabeId']; ?>)"
                                                class="inline-flex cursor-pointer items-center gap-2 whitespace-nowrap rounded-lg border-0 bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-500">
                                            Complete task <i class='fa fa-check'></i>
                                        </button>
                                    </th>
                                    <th class="px-3 py-2">
                                        <div class="flex flex-col items-center gap-1">
                                            <button type="button" title="Increase priority" aria-label="Increase priority" onclick="higherPrio(<?=$task['aufgabeId']?>)"
                                                    class="inline-flex h-7 w-7 cursor-pointer items-center justify-center rounded-md border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                                <?= icon('chevron-up', 'h-4 w-4') ?>
                                            </button>
                                            <p class="badge bg-surface-700 text-surface-100"><?= htmlspecialchars(decrypt($task['prioritaet'], $encryption_key, $iv), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php if (decrypt($task['prioritaet'], $encryption_key, $iv) > 0) { ?>
                                            <button type="button" title="Decrease priority" aria-label="Decrease priority" onclick="lowerPrio(<?=$task['aufgabeId']?>)"
                                                    class="inline-flex h-7 w-7 cursor-pointer items-center justify-center rounded-md border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                                <?= icon('chevron-down', 'h-4 w-4') ?>
                                            </button>
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
                        <button type="button" onclick='openTaskModal()' class="btn-primary">Add task &nbsp;<?= icon('plus', 'h-5 w-5') ?></button>
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

    <?php include "app/Views/taskModal.view.php"; ?>
    <?php include "app/Views/addRapportModal.view.php"; ?>
    <?php include "app/Views/footer.view.php"; ?>
</body>
</html>
