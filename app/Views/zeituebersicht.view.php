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
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/zeituebersicht.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/searchTask.js"></script>
    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
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
    <table class='leiste'>
        <tr>
            <th><h2>Time overview</h2></th>
        </tr>
    </table>

    <input class="search" id="myInput" onkeyup="searchFor()" placeholder="Search for tasks and rapports" type="text">

    <div class="flex-container">
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
                            <table class='data' id="<?= str_replace(" ", "", $taskTitle) ?>">
                                <tr>
                                    <th style='font-style: italic; text-shadow: 4px 4px 2px rgba(0,0,0,0.6); font-size: 1.2rem;'>
                                        <p><?= $taskTitle ?></p>
                                    </th>
                                    <th>When</th>
                                    <th>Duration</th>
                                    <th>Edit / Delete</th>
                                </tr>

                                <tr>
                                    <td><?= decrypt($rapport["rapport"], $encryption_key, $ivRapp) ?></td>
                                    <td><i class="fas fa-calendar-days"></i> <?= date("dS M Y", strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp))) ?></td>
                                    <td><i class="fas fa-clock"></i> <?= decrypt($rapport["zeit"], $encryption_key, $ivRapp) ?></td>
                                    <?php
                                        $timeinsec = strtotime(decrypt($rapport["zeit"], $encryption_key, $ivRapp)) - $sum;
                                        $totaltime += $timeinsec;
                                        $created_at = strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp));
                                    ?>
                                    <td class='editDelete'>
                                        <?php if ($created_at >= strtotime("-1 day")): ?>
                                            <img onclick='editTime(<?= $rapport["rapportId"] ?>)' title='Edit rapport and time' src='images/edit.png' alt=''>
                                            / <img title='Delete rapport and time' onclick='deleteTime(<?= $rapport["rapportId"] ?>)' src='images/delete.png' alt=''>
                                        <?php else: ?>
                                            <td></td>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php else: 
                                if ($rapportCounter == 5): ?>
                                    <tr>
                                        <td><button title='Look into the history' onclick='showHistory(<?= $task["aufgabeId"] ?>)'><i class='fa fa-archive'></i>&nbsp History</button></td>
                                    </tr>
                                    <?php break; ?>
                                <?php else: ?>
                                    <tr>
                                        <td><?= decrypt($rapport["rapport"], $encryption_key, $ivRapp) ?></td>
                                        <td><i class="fas fa-calendar-days"></i> <?= date("dS M Y", strtotime(decrypt($rapport["created_at"], $encryption_key, $ivRapp))) ?></td>
                                        <td><i class="fas fa-clock"></i> <?= decrypt($rapport["zeit"], $encryption_key, $ivRapp) ?></td>
                                        <td class='editDelete'>
                                            <?php if ($created_at >= strtotime("-1 day")): ?>
                                                <img onclick='editTime(<?= $rapport["rapportId"] ?>)' title='Edit rapport and time' src='images/edit.png' alt=''>
                                                / <img title='Delete rapport and time' onclick='deleteTime(<?= $rapport["rapportId"] ?>)' src='images/delete.png' alt=''>
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
                            <table class='data' id="<?= $taskTitle ?>">
                                <tr><th><p><?= $taskTitle ?></p></th></tr>
                                <tr>
                                    <td style='color:red; text-align:center;'><strong>No rapports found</strong></td>
                                </tr>
                            </table>
                        </div>
                    <?php elseif ($rapportCounter < 5): 
                        $h = intval($totaltime / 3600);
                        $totaltime -= $h * 3600;
                        $m = intval($totaltime / 60);
                        $s = $totaltime - $m * 60;
                    ?>
                        <tr>
                            <td><strong>Total time spent: <font color='red'>
                                <?php
                                    $Time = new TimeController();
                                    $Time->formatTimeOutput($h, $m, $s);
                                ?>
                            </font></strong></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="noData">
                <h1>No tasks therefore no records</h1>
                <p>Add a task, work on it by creating a record and then you'll find it here</p>
            </div>
        <?php endif; ?>

    <div id="nothingFound">
        <h1>Nothing found</h1>
        <img src="images/sad_smiley.png" alt="">
    </div>

<?php include "app/Views/footer.view.php"; ?>

</body>

</html>