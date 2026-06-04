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

    <script defer src="public/js/searchTask.js"></script>
    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/footer.js"></script>

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

<!-- Time Tracking Overview -->
<?php if (count($getTitleOfTask) > 0): ?>
    <div class="mx-auto max-w-6xl px-4 pt-6">
        <h2 class="text-2xl font-bold text-white">Time overview</h2>
        <input class="search input mt-4 max-w-md" id="myInput" onkeyup="searchFor()" placeholder="Search for tasks and rapports" type="text">
    </div>

    <div class="flex-container mx-auto grid max-w-6xl gap-6 px-4 py-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($getTitleOfTask as $task):
            $totaltime = 0;
            $sum = strtotime("00:00:00");
            $rapportCounter = 0;
            $ivTitle = base64_decode($task['iv']);
            $taskTitle = decrypt($task["titel"], $encryption_key, $ivTitle);
        ?>

            <?php foreach ($getRapports as $rapport):
                $ivRapp = base64_decode($rapport['iv']);

                if ($rapport["fk_aufgabeId"] == $task["aufgabeId"]):
                    $rapportCounter++;
                    if ($rapportCounter == 1): ?>
                        <div>
                            <table class='data w-full overflow-hidden rounded-xl border border-solid border-surface-700 bg-surface-800 text-sm text-surface-200 shadow-lg' id="<?= str_replace(" ", "", $taskTitle) ?>">
                                <tr class="bg-surface-700/40">
                                    <th class="px-3 py-3 text-left text-base font-semibold italic text-white">
                                        <p><?= $taskTitle ?></p>
                                    </th>
                                    <th class="px-3 py-3 text-left font-semibold text-surface-200">When</th>
                                    <th class="px-3 py-3 text-left font-semibold text-surface-200">Duration</th>
                                    <th class="px-3 py-3 text-left font-semibold text-surface-200">Edit / Delete</th>
                                </tr>

                                <tr class="border-t border-solid border-surface-700">
                                    <td class="px-3 py-2"><?= decrypt($rapport["rapport"], $encryption_key, $ivRapp) ?></td>
                                    <td class="px-3 py-2 text-surface-300"><i class="fas fa-calendar-days"></i> <?= date("dS M Y", strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp))) ?></td>
                                    <td class="px-3 py-2 text-surface-300"><i class="fas fa-clock"></i> <?= decrypt($rapport["zeit"], $encryption_key, $ivRapp) ?></td>
                                    <?php
                                        $timeinsec = strtotime(decrypt($rapport["zeit"], $encryption_key, $ivRapp)) - $sum;
                                        $totaltime += $timeinsec;
                                        $created_at = strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp));
                                    ?>
                                    <td class='editDelete px-3 py-2'>
                                        <?php if ($created_at >= strtotime("-1 day")): ?>
                                            <img class="inline h-5 w-5 cursor-pointer transition hover:scale-110" onclick='editTime(<?= $rapport["rapportId"] ?>)' title='Edit rapport and time' src='images/edit.png' alt=''>
                                            / <img class="inline h-5 w-5 cursor-pointer transition hover:scale-110" title='Delete rapport and time' onclick='deleteTime(<?= $rapport["rapportId"] ?>)' src='images/delete.png' alt=''>
                                        <?php else: ?>
                                            <td></td>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php else:
                                if ($rapportCounter == 5): ?>
                                    <tr class="border-t border-solid border-surface-700">
                                        <td class="px-3 py-2"><button class="btn-secondary" title='Look into the history' onclick='showHistory(<?= $task["aufgabeId"] ?>)'><i class='fa fa-archive'></i>&nbsp History</button></td>
                                    </tr>
                                    <?php break; ?>
                                <?php else: ?>
                                    <tr class="border-t border-solid border-surface-700">
                                        <td class="px-3 py-2"><?= decrypt($rapport["rapport"], $encryption_key, $ivRapp) ?></td>
                                        <td class="px-3 py-2 text-surface-300"><i class="fas fa-calendar-days"></i> <?= date("dS M Y", strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp))) ?></td>
                                        <td class="px-3 py-2 text-surface-300"><i class="fas fa-clock"></i> <?= decrypt($rapport["zeit"], $encryption_key, $ivRapp) ?></td>
                                        <td class='editDelete px-3 py-2'>
                                            <?php if ($created_at >= strtotime("-1 day")): ?>
                                                <img class="inline h-5 w-5 cursor-pointer transition hover:scale-110" onclick='editTime(<?= $rapport["rapportId"] ?>)' title='Edit rapport and time' src='images/edit.png' alt=''>
                                                / <img class="inline h-5 w-5 cursor-pointer transition hover:scale-110" title='Delete rapport and time' onclick='deleteTime(<?= $rapport["rapportId"] ?>)' src='images/delete.png' alt=''>
                                            <?php else: ?>
                                                <td></td>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($rapportCounter == 0): ?>
                        <div>
                            <table class='data w-full overflow-hidden rounded-xl border border-solid border-surface-700 bg-surface-800 text-sm text-surface-200 shadow-lg' id="<?= $taskTitle ?>">
                                <tr class="bg-surface-700/40"><th class="px-3 py-3 text-left text-base font-semibold italic text-white"><p><?= $taskTitle ?></p></th></tr>
                                <tr class="border-t border-solid border-surface-700">
                                    <td class="px-3 py-2 text-center font-semibold text-red-400">No rapports found</td>
                                </tr>
                            </table>
                        </div>
                    <?php elseif ($rapportCounter < 5):
                        $h = intval($totaltime / 3600);
                        $totaltime -= $h * 3600;
                        $m = intval($totaltime / 60);
                        $s = $totaltime - $m * 60;
                    ?>
                        <tr class="border-t border-solid border-surface-700">
                            <td class="px-3 py-2"><strong>Total time spent: <span class="text-red-400">
                                <?php
                                    $Time = new TimeController();
                                    $Time->formatTimeOutput($h, $m, $s);
                                ?>
                            </span></strong></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="noData">
                <h1 class="text-2xl font-bold text-red-400">No tasks therefore no records</h1>
                <p class="text-surface-300">Add a task, work on it by creating a record and then you'll find it here</p>
            </div>
        <?php endif; ?>

    <div id="nothingFound" class="flex flex-col items-center gap-4 py-10 text-center" style="display:none;">
        <h1 class="text-2xl font-bold text-surface-200">Nothing found</h1>
        <img src="images/sad_smiley.png" alt="">
    </div>

<?php include "app/Views/footer.view.php"; ?>

</body>

</html>
