<?php
// Funktion zur Entschlüsselung
function decrypt($data, $key, $iv) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
    }
    return $decrypted;
}
// Plain-text preview from CKEditor HTML (strip tags + collapse whitespace)
function preview_text(string $html): string {
    return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
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
        <div class="mx-auto max-w-4xl px-4 py-6">
            <!-- Open / Completed switch -->
            <div class='switch mx-auto mb-8 flex w-full max-w-md gap-2 rounded-full border border-solid border-surface-700 bg-surface-800 p-1.5 shadow-lg'>
                <button id='openButton' onclick='navSwitch(1)'
                        class="flex-1 cursor-pointer rounded-full border-0 bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition-colors">
                    Open (<?= (int) $open_tasks_counter ?>)
                </button>
                <button id='doneButton' onclick='navSwitch(2)'
                        class="flex-1 cursor-pointer rounded-full border-0 bg-surface-800 px-6 py-3 text-sm font-semibold text-surface-400 transition-colors">
                    Completed (<?= (int) $done_tasks_counter ?>)
                </button>
            </div>

            <!-- Open tasks -->
            <div id='open'>
                <?php if ($open_tasks_counter > 0) { ?>
                    <div class="order mb-4 flex flex-wrap items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-white">Open tasks</h2>
                        <div class="flex items-center gap-3">
                            <form action="" method="GET" class="flex items-center gap-2">
                                <label for="sort" class="whitespace-nowrap text-sm text-surface-400">Sort by</label>
                                <select id="sort" name="sort" onchange="this.form.submit()" aria-label="Sort tasks by"
                                        class="rounded-lg border border-solid border-surface-600 bg-surface-900 px-3 py-2 text-sm text-surface-100 focus:border-brand-500 focus:outline-none">
                                    <option value="priority" <?= ($_GET["sort"] ?? '') == "priority" ? "selected" : "" ?>>Priority</option>
                                    <option value="alphabet" <?= ($_GET["sort"] ?? '') == "alphabet" ? "selected" : "" ?>>Alphabet</option>
                                    <option value="deadline" <?= ($_GET["sort"] ?? '') == "deadline" ? "selected" : "" ?>>Deadline</option>
                                </select>
                                <noscript><button type="submit" class="btn-primary">Sort</button></noscript>
                            </form>
                            <button type="button" class='plus btn-primary !h-11 !w-11 !rounded-full !p-0' title="Add task" aria-label="Add task" onclick='openTaskModal()'><?= icon('plus', 'h-5 w-5') ?></button>
                        </div>
                    </div>
                    <main class="space-y-5">
                        <?php foreach ($getObjects as $task) {
                            $iv = base64_decode($task['iv']);
                            $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                            if ($decrypted_status !== '0') continue;

                            $taskId     = (int) $task['aufgabeId'];
                            $t_title    = decode_all(decrypt($task["titel"], $encryption_key, $iv));
                            $t_desc     = decode_all(decrypt($task["beschreibung"], $encryption_key, $iv));
                            $t_motiv    = decode_all(decrypt($task["motivation"], $encryption_key, $iv));
                            $t_deadline = decode_all(decrypt($task["deadline"], $encryption_key, $iv));
                            $t_prio     = decode_all(decrypt($task['prioritaet'], $encryption_key, $iv));
                            $descText   = preview_text($t_desc);
                            $motivText  = preview_text($t_motiv);
                            $isFresh    = strtotime(decrypt($task["created_at"], $encryption_key, $iv)) >= strtotime("-1 day");
                            $showToggle = (mb_strlen($descText) > 140 || mb_strlen($motivText) > 140);
                        ?>
                        <div class='task rounded-xl border border-solid border-surface-700 bg-surface-800 p-5 shadow-lg'>
                            <!-- Header: title, due date, priority + reorder -->
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-semibold text-white"><?= display_text(decrypt($task["titel"], $encryption_key, $iv)) ?></h3>
                                    <p class="mt-1 text-sm text-surface-400">
                                        <?= date_with_icon(decrypt($task["deadline"], $encryption_key, $iv), 'Due') ?>
                                    </p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="badge bg-brand-500/15 text-brand-200 ring-1 ring-inset ring-brand-500/30" title="Priority <?= htmlspecialchars($t_prio, ENT_QUOTES, 'UTF-8') ?>">P<?= htmlspecialchars($t_prio, ENT_QUOTES, 'UTF-8') ?></span>
                                    <div class="flex items-center overflow-hidden rounded-lg border border-solid border-surface-600">
                                        <button type="button" title="Move up (raise priority)" aria-label="Move task up" onclick="higherPrio(<?= $taskId ?>)"
                                                class="inline-flex h-8 w-8 cursor-pointer items-center justify-center border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-surface-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500">
                                            <?= icon('chevron-up', 'h-4 w-4') ?>
                                        </button>
                                        <?php if ($t_prio > 0): ?>
                                        <button type="button" title="Move down (lower priority)" aria-label="Move task down" onclick="lowerPrio(<?= $taskId ?>)"
                                                class="inline-flex h-8 w-8 cursor-pointer items-center justify-center border-0 border-l border-solid border-surface-600 bg-surface-700 text-surface-200 transition-colors hover:bg-surface-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500">
                                            <?= icon('chevron-down', 'h-4 w-4') ?>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Body: description + motivation previews -->
                            <div class="mt-4 grid gap-4 sm:grid-cols-2 <?= $showToggle ? 'task-clamp' : '' ?>">
                                <div>
                                    <div class="mb-1 text-xs font-medium uppercase tracking-wide text-surface-500">Description</div>
                                    <?php if (trim($t_desc) !== ''): ?><div class="task-prose"><?= sanitize_html($t_desc) ?></div><?php else: ?><p class="text-sm italic text-surface-500">No description</p><?php endif; ?>
                                </div>
                                <div>
                                    <div class="mb-1 text-xs font-medium uppercase tracking-wide text-surface-500">Motivation</div>
                                    <?php if (trim($t_motiv) !== ''): ?><div class="task-prose"><?= sanitize_html($t_motiv) ?></div><?php else: ?><p class="text-sm italic text-surface-500">No motivation</p><?php endif; ?>
                                </div>
                            </div>
                            <?php if ($showToggle): ?>
                                <button type="button" class="mt-2 text-xs font-medium text-brand-300 hover:text-brand-200 focus:outline-none" data-expanded="0" onclick="toggleDetails(this)">Show more</button>
                            <?php endif; ?>

                            <!-- Actions: time tracking · edit/delete · complete -->
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-0 border-t border-solid border-surface-700 pt-4">
                                <!-- Time-tracking group (timer + play) -->
                                <div class="flex items-center gap-2 rounded-full border border-solid border-surface-600 bg-surface-900 py-1 pl-4 pr-1">
                                    <i class="fas fa-clock text-surface-500"></i>
                                    <span id='active_time<?= $taskId ?>' class="font-mono text-sm font-semibold text-surface-100">00:00:00</span>
                                    <button type="button" id='start<?= $taskId ?>' onclick="start_recording(<?= $taskId ?>)" title="Start recording" aria-label="Start recording"
                                            class="time-toggle inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border-0 bg-brand-600 text-white transition-colors hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                        <?= icon('play', 'icon-play h-4 w-4') ?>
                                        <?= icon('stop', 'icon-stop h-4 w-4') ?>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2">
                                    <?php if ($isFresh): ?>
                                        <button type="button" title="Edit task" aria-label="Edit task" onclick="openEditTask(this)"
                                                data-id="<?= $taskId ?>"
                                                data-title="<?= htmlspecialchars($t_title, ENT_QUOTES, 'UTF-8') ?>"
                                                data-description="<?= htmlspecialchars($t_desc, ENT_QUOTES, 'UTF-8') ?>"
                                                data-motivation="<?= htmlspecialchars($t_motiv, ENT_QUOTES, 'UTF-8') ?>"
                                                data-deadline="<?= htmlspecialchars($t_deadline, ENT_QUOTES, 'UTF-8') ?>"
                                                data-priority="<?= htmlspecialchars($t_prio, ENT_QUOTES, 'UTF-8') ?>"
                                                class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-surface-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                            <?= icon('pencil', 'h-5 w-5') ?>
                                        </button>
                                        <button type="button" title="Delete task" aria-label="Delete task" onclick='deleteTask(<?= $taskId ?>)'
                                                class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-red-300 transition-colors hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                                            <?= icon('trash', 'h-5 w-5') ?>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" title='Complete task' id='completeBtn' onclick="completeTask(<?= $taskId ?>)"
                                            class="inline-flex cursor-pointer items-center gap-2 whitespace-nowrap rounded-lg border border-solid border-green-600/50 bg-green-600/10 px-4 py-2 text-sm font-medium text-green-300 transition-colors hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <i class='fa fa-check'></i> Complete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </main>
                <?php } else { ?>
                    <div class='noData flex flex-col items-center gap-4 py-16 text-center'>
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-800 text-surface-500"><?= icon('flag', 'h-8 w-8') ?></div>
                        <h1 class="text-xl font-semibold text-surface-200">No open tasks yet</h1>
                        <p class="text-sm text-surface-400">Create your first task to get started.</p>
                        <button type="button" onclick='openTaskModal()' class="btn-primary">Add task &nbsp;<?= icon('plus', 'h-5 w-5') ?></button>
                    </div>
                <?php } ?>
            </div>

            <!-- Completed tasks -->
            <div id='done'>
                <?php if ($done_tasks_counter > 0) { ?>
                    <div class='order mb-4'>
                        <h2 class="text-lg font-semibold text-white">Completed tasks</h2>
                    </div>
                    <main class="space-y-5">
                        <?php foreach ($getObjects as $task) {
                            $iv = base64_decode($task['iv']);
                            $decrypted_status = decrypt($task['status'], $encryption_key, $iv);
                            if ($decrypted_status !== '1') continue;

                            $taskId     = (int) $task['aufgabeId'];
                            $t_desc     = decode_all(decrypt($task["beschreibung"], $encryption_key, $iv));
                            $t_motiv    = decode_all(decrypt($task["motivation"], $encryption_key, $iv));
                            $descText   = preview_text($t_desc);
                            $motivText  = preview_text($t_motiv);
                            $showToggle = (mb_strlen($descText) > 140 || mb_strlen($motivText) > 140);
                        ?>
                            <div class='task rounded-xl border border-solid border-surface-700 bg-surface-800 p-5 opacity-90 shadow-lg'>
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-semibold text-white"><?= display_text(decrypt($task["titel"], $encryption_key, $iv)) ?></h3>
                                        <p class="mt-1 text-sm text-surface-400">
                                            <?= date_with_icon(decrypt($task["deadline"], $encryption_key, $iv), 'Due') ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-green-500/15 text-green-300 ring-1 ring-inset ring-green-500/30"><i class="fa fa-check mr-1"></i>Completed</span>
                                </div>

                                <div class="mt-4 grid gap-4 sm:grid-cols-2 <?= $showToggle ? 'task-clamp' : '' ?>">
                                    <div>
                                        <div class="mb-1 text-xs font-medium uppercase tracking-wide text-surface-500">Description</div>
                                        <?php if (trim($t_desc) !== ''): ?><div class="task-prose"><?= sanitize_html($t_desc) ?></div><?php else: ?><p class="text-sm italic text-surface-500">No description</p><?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="mb-1 text-xs font-medium uppercase tracking-wide text-surface-500">Motivation</div>
                                        <?php if (trim($t_motiv) !== ''): ?><div class="task-prose"><?= sanitize_html($t_motiv) ?></div><?php else: ?><p class="text-sm italic text-surface-500">No motivation</p><?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($showToggle): ?>
                                    <button type="button" class="mt-2 text-xs font-medium text-brand-300 hover:text-brand-200 focus:outline-none" data-expanded="0" onclick="toggleDetails(this)">Show more</button>
                                <?php endif; ?>
                            </div>
                        <?php } ?>
                    </main>
                <?php } else { ?>
                    <div class='noData flex flex-col items-center gap-4 py-16 text-center'>
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-800 text-surface-500"><i class="fas fa-check text-2xl"></i></div>
                        <h1 class="text-xl font-semibold text-surface-200">No tasks completed yet</h1>
                        <p class="text-sm text-surface-400">Completed tasks will appear here.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <!-- CKEditor is needed by the in-page task modal (taskModal.js) -->
    <script src="ckeditor/ckeditor.js"></script>
    <script>
    // Expand / collapse the description & motivation previews on a card.
    function toggleDetails(btn) {
        var card = btn.closest('.task');
        if (!card) return;
        var expand = btn.getAttribute('data-expanded') !== '1';
        card.querySelectorAll('.task-clamp').forEach(function (el) {
            el.classList.toggle('is-expanded', expand);
        });
        btn.setAttribute('data-expanded', expand ? '1' : '0');
        btn.textContent = expand ? 'Show less' : 'Show more';
    }

    // Rich-text editor for the (rare) blocked-user essay field
    if (window.CKEDITOR && document.getElementById('essay_content')) {
        CKEDITOR.replace('essay_content');
    }

    // The shared task modal now saves via AJAX; refresh the Tasks list on success
    // (same end result the full-page redirect used to give).
    document.addEventListener('prio:taskChanged', function () { location.reload(); });
    </script>

    <?php include "app/Views/taskModal.view.php"; ?>
    <?php include "app/Views/addRapportModal.view.php"; ?>
    <?php include "app/Views/footer.view.php"; ?>
</body>
</html>
