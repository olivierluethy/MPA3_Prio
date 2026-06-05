<?php
// Funktion zur Entschlüsselung
function decrypt($data, $key, $iv) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);
    if ($decrypted === false) {
        return 'Decryption error'; // Fehlerhinweis bei Fehlschlag
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
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/searchTask.js"></script>
    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/timeRecords.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Time Records</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include "header.php";
    ?>

    <main class="mx-auto max-w-5xl px-4 py-8">
        <?php if (count($getTitleOfTask) > 0): ?>
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Time overview</h1>
                    <p class="mt-1 text-sm text-surface-400">Reported work per task — totals, entries and activity at a glance.</p>
                </div>
                <div class="relative w-full sm:w-72">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-surface-400">
                        <i class="fas fa-magnifying-glass"></i>
                    </span>
                    <input class="search input pl-9" id="myInput" onkeyup="searchFor()" placeholder="Search tasks and reports" type="text" aria-label="Search tasks and reports">
                </div>
            </div>

            <!-- Task reporting cards -->
            <div id="timeCards" class="mt-8 space-y-8">
                <?php foreach ($getTitleOfTask as $task):
                    $ivTask      = base64_decode($task['iv']);
                    $taskTitle   = decode_all(decrypt($task["titel"], $encryption_key, $ivTask));
                    $taskStatus  = decrypt($task["status"], $encryption_key, $ivTask);          // '0' open, '1' done
                    $taskPrio    = decode_all(decrypt($task["prioritaet"], $encryption_key, $ivTask));
                    $taskCreated = decrypt($task["created_at"], $encryption_key, $ivTask);

                    // Collect this task's reports
                    $entries     = [];
                    $totalSec    = 0;
                    $lastTs      = null;
                    foreach ($getRapports as $rapport):
                        if ($rapport["fk_aufgabeId"] != $task["aufgabeId"]) continue;
                        $ivR     = base64_decode($rapport['iv']);
                        $rZeit   = decrypt($rapport["zeit"], $encryption_key, $ivR);
                        $rText   = decode_all(decrypt($rapport["rapport"], $encryption_key, $ivR));
                        $rCreate = decrypt($rapport["created_at"], $encryption_key, $ivR);
                        $rTs     = strtotime($rCreate);
                        $totalSec += max(0, strtotime($rZeit) - strtotime("00:00:00"));
                        if ($lastTs === null || $rTs > $lastTs) $lastTs = $rTs;
                        $entries[] = ['id' => $rapport["rapportId"], 'zeit' => $rZeit, 'seconds' => max(0, strtotime($rZeit) - strtotime("00:00:00")), 'text' => $rText, 'ts' => $rTs];
                    endforeach;

                    $entryCount  = count($entries);
                    $totalLabel  = format_duration($totalSec);
                    $isDone      = ($taskStatus === '1');
                ?>
                    <article class="data overflow-hidden rounded-2xl border border-solid border-surface-700 bg-surface-800 shadow-lg">
                        <!-- Card header -->
                        <div class="flex flex-wrap items-start justify-between gap-3 border-0 border-b border-solid border-surface-700 p-5">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="truncate text-lg font-semibold text-white"><?= htmlspecialchars($taskTitle, ENT_QUOTES, 'UTF-8') ?></h2>
                                    <?php if ($isDone): ?>
                                        <span class="badge bg-green-500/15 text-green-300 ring-1 ring-inset ring-green-500/30"><i class="fas fa-check mr-1"></i>Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-amber-500/15 text-amber-300 ring-1 ring-inset ring-amber-500/30"><i class="fas fa-circle-dot mr-1"></i>Open</span>
                                    <?php endif; ?>
                                    <span class="badge bg-brand-500/15 text-brand-200 ring-1 ring-inset ring-brand-500/30">Priority <?= htmlspecialchars($taskPrio, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <p class="mt-1 text-sm text-surface-400">
                                    <i class="fas fa-calendar-days mr-1"></i>Created <?= htmlspecialchars(date("d M Y", strtotime($taskCreated)), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                            <!-- Per-task actions -->
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <a href="export_pdf?id=<?= (int) $task['aufgabeId'] ?>"
                                   title="Full PDF report including the task description" aria-label="Export full PDF"
                                   class="btn-secondary !px-3 !py-2 text-sm">
                                    <?= icon('download', 'h-4 w-4') ?> Export PDF
                                </a>
                                <a href="export_pdf?id=<?= (int) $task['aufgabeId'] ?>&description=0"
                                   title="Time report without the task description (for HR / billing)" aria-label="Export summary-only PDF"
                                   class="btn-ghost !px-3 !py-2 text-sm">
                                    Summary only
                                </a>
                            </div>
                        </div>

                        <!-- Summary stats -->
                        <div class="grid grid-cols-2 gap-px bg-surface-700 sm:grid-cols-4">
                            <div class="bg-surface-800 p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-surface-400">Total time</div>
                                <div class="mt-1 text-lg font-semibold text-white"><?= $entryCount ? htmlspecialchars($totalLabel, ENT_QUOTES, 'UTF-8') : '—' ?></div>
                            </div>
                            <div class="bg-surface-800 p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-surface-400">Entries</div>
                                <div class="mt-1 text-lg font-semibold text-white"><?= (int) $entryCount ?></div>
                            </div>
                            <div class="bg-surface-800 p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-surface-400">Last activity</div>
                                <div class="mt-1 text-lg font-semibold text-white"><?= $lastTs ? htmlspecialchars(date("d M Y", $lastTs), ENT_QUOTES, 'UTF-8') : '—' ?></div>
                            </div>
                            <div class="bg-surface-800 p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-surface-400">Status</div>
                                <div class="mt-1 text-lg font-semibold <?= $isDone ? 'text-green-300' : 'text-amber-300' ?>"><?= $isDone ? 'Completed' : 'Open' ?></div>
                            </div>
                        </div>

                        <!-- Entries table -->
                        <div class="p-5">
                            <?php if ($entryCount > 0): ?>
                                <div class="max-h-96 overflow-auto rounded-lg border border-solid border-surface-700">
                                    <table class="w-full border-collapse text-sm">
                                        <thead class="sticky top-0 z-10">
                                            <tr class="bg-surface-900 text-left text-xs uppercase tracking-wide text-surface-400">
                                                <th class="px-4 py-3 font-semibold">Date</th>
                                                <th class="px-4 py-3 font-semibold">Duration</th>
                                                <th class="px-4 py-3 font-semibold">Report</th>
                                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($entries as $e): ?>
                                                <tr class="border-0 border-t border-solid border-surface-700 transition-colors hover:bg-surface-700/40">
                                                    <td class="whitespace-nowrap px-4 py-3 text-surface-300">
                                                        <i class="fas fa-calendar-days mr-1 text-surface-500"></i><?= htmlspecialchars(date("d M Y", $e['ts']), ENT_QUOTES, 'UTF-8') ?>
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3">
                                                        <span class="badge bg-brand-500/15 font-mono text-brand-200"><i class="fas fa-clock mr-1"></i><?= htmlspecialchars(format_duration($e['seconds']), ENT_QUOTES, 'UTF-8') ?></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-surface-200"><?= htmlspecialchars($e['text'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center justify-end gap-2">
                                                            <?php if ($e['ts'] >= strtotime("-1 day")): ?>
                                                                <button type="button" title="Edit rapport and time" aria-label="Edit rapport and time"
                                                                        onclick="openEditTime(this)"
                                                                        data-id="<?= (int) $e["id"] ?>"
                                                                        data-date="<?= htmlspecialchars(date("d M Y", $e['ts']), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-duration="<?= htmlspecialchars($e['zeit'], ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-report="<?= htmlspecialchars($e['text'], ENT_QUOTES, 'UTF-8') ?>"
                                                                        class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-surface-200 transition-colors hover:bg-surface-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"><?= icon('pencil', 'h-4 w-4') ?></button>
                                                                <button type="button" title="Delete rapport and time" aria-label="Delete rapport and time"
                                                                        onclick="openDeleteTime(this)" data-id="<?= (int) $e["id"] ?>"
                                                                        class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg border-0 bg-surface-700 text-red-300 transition-colors hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500"><?= icon('trash', 'h-4 w-4') ?></button>
                                                            <?php else: ?>
                                                                <span class="text-xs text-surface-500">Locked</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="rounded-lg border border-dashed border-surface-700 px-4 py-8 text-center text-surface-400">
                                    <i class="fas fa-clock mb-2 block text-2xl text-surface-600"></i>
                                    No time reported for this task yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- No search results -->
            <div id="nothingFound" class="flex flex-col items-center gap-4 py-12 text-center" style="display:none;">
                <img src="images/sad_smiley.png" alt="" class="h-20 w-20 opacity-80">
                <h2 class="text-xl font-semibold text-surface-200">Nothing found</h2>
            </div>
        <?php else: ?>
            <div class="noData">
                <h1 class="text-2xl font-bold text-red-400">No tasks therefore no records</h1>
                <p class="text-surface-300">Add a task, work on it by creating a record and then you'll find it here</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Edit time-record modal (in-place, no navigation) -->
    <div id="editTimeModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-black/60 p-4 sm:p-8"
         role="dialog" aria-modal="true" aria-labelledby="editTimeTitle">
        <div class="mx-auto mt-10 w-full max-w-lg rounded-xl border border-solid border-surface-700 bg-surface-800 shadow-2xl">
            <div class="flex items-center justify-between border-0 border-b border-solid border-surface-700 px-5 py-3">
                <h2 id="editTimeTitle" class="text-lg font-semibold text-white">Edit time record</h2>
                <button type="button" data-time-close aria-label="Close dialog"
                        class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-surface-300 transition-colors hover:bg-surface-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?= icon('x', 'h-5 w-5') ?>
                </button>
            </div>
            <form id="editTimeForm" class="space-y-4 p-5">
                <div>
                    <span class="label">Date</span>
                    <p id="editTime_date" class="text-sm text-surface-300">—</p>
                </div>
                <div>
                    <label for="editTime_report" class="label">Report</label>
                    <input type="text" id="editTime_report" name="rapport" class="input" autocomplete="off">
                </div>
                <div>
                    <label for="editTime_duration" class="label">Duration (hh:mm:ss)</label>
                    <input type="time" step="1" id="editTime_duration" name="time" class="input">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" data-time-close class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete time-record confirmation modal -->
    <div id="deleteTimeModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-black/60 p-4 sm:p-8"
         role="dialog" aria-modal="true" aria-labelledby="deleteTimeTitle">
        <div class="mx-auto mt-24 w-full max-w-md rounded-xl border border-solid border-surface-700 bg-surface-800 p-6 text-center shadow-2xl">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-500/15 text-red-400"><?= icon('trash', 'h-6 w-6') ?></div>
            <h2 id="deleteTimeTitle" class="text-lg font-semibold text-white">Delete time record?</h2>
            <p class="mt-2 text-sm text-surface-400">This permanently removes this report and its time. This action cannot be undone.</p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" data-time-close class="btn-secondary">Cancel</button>
                <button type="button" id="confirmDeleteTime"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <?= icon('trash', 'h-4 w-4') ?> Delete
                </button>
            </div>
        </div>
    </div>

    <?php include "app/Views/footer.view.php"; ?>
</body>

</html>
