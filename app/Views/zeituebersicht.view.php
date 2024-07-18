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

    <!-- Zeiterfassungen -->
    <?php if (count($getTitleOfTask) > 0) { ?>
    <table class='leiste'>
        <tr>
            <th>
                <h2>Time overview</h2>
            </th>
            <th></th>
            <th></th>
        </tr>
    </table>

    <input class="search" id="myInput" onkeyup="myFunction()" placeholder="Search for task" type="text">

    <div class="flex-container">

        <?php foreach ($getTitleOfTask as $getTitleOfTask2) {
            $totaltime = 0;
            $sum = strtotime("00:00:00");
            $rapportCounter = 0;
            $ivTitle = base64_decode($getTitleOfTask2['iv']);
            foreach ($getRapports as $getRapports2) {
                $ivRapp = base64_decode($getRapports2['iv']);
                
                /* Check if rapport belongs to task */
                if ($getRapports2["fk_aufgabeId"] == $getTitleOfTask2["aufgabeId"]) {
                    $rapportCounter++;

                    /* Check if currently no data has been given out */
                    if ($rapportCounter == 1) {
                        echo "<div>
                            <!-- Das ID Attribut frisst keine Leerschläge aka Whitespaces -->
                            <table class='data' id=" .str_replace(" ", "", decrypt($getTitleOfTask2["titel"], $encryption_key, $ivTitle)) .">
                                <tr>
                                    <th style='font-style: italic; text-shadow: 4px 4px 2px rgba(0,0,0,0.6); font-size: 1.2rem;'><p>" .
                            decrypt($getTitleOfTask2["titel"], $encryption_key, $ivTitle) .
                            "</p></th>
                                    <th>When</th>
                                    <th>Duration</th>
                                    <th>Edit / Delete</th>
                                </tr>"; ?>

        <!-- Display all essential informations about task -->
        <tr>
            <td><?= decrypt($getRapports2["rapport"], $encryption_key, $ivRapp) ?></td>
            <?php $date = date(
                "dS M Y",
                strtotime(decrypt($getRapports2["created_at"], $encryption_key, $ivRapp))
            ); ?>
            <td><i class="fas fa-calendar-days"></i> <?= $date ?></td>
            <td><i class="fas fa-clock"></i> <?= decrypt($getRapports2["zeit"], $encryption_key, $ivRapp) ?></td>

            <?php
            // Converting the time into seconds
            $timeinsec = strtotime(decrypt($getRapports2["zeit"], $encryption_key, $ivRapp)) - $sum;

            // Sum the time with previous value
            $totaltime = $totaltime + $timeinsec;

            /* Check if task as been created under 24 hours */
            if (strtotime(decrypt($getRapports2["created_at"], $encryption_key, $ivRapp)) >= strtotime("-1 day")) {
                /* Task is younger than 24 hours */
                echo "<td class='editDelete'>
                                                <img onclick='editTime(" .
                    $getRapports2["rapportId"] .
                    ")'
                                                title='Edit rapport and time' src='images/edit.png' alt=''> / <img title='Delete rapport and time'
                                                onclick='deleteTime(" .
                    $getRapports2["rapportId"] .
                    ")' src='images/delete.png' alt=''>
                                            </td>";
            } /* When task is or older than 24 hours */ else {
                echo "<td></td>";
            }
            ?>
            <?php
                    } else {
                        /* Check if their are already 5 rows of data */
                        if ($rapportCounter == 5) {
                            echo "<tr><td><button title='Look into the history' onclick='showHistory(" .
                                $getTitleOfTask2["aufgabeId"] .
                                ")'><i class='fa fa-archive'></i>&nbsp History</button></td><td></td><td></td></tr>";
                            break;
                        } else {
                             ?>
            <!-- Display all essential informations about task -->
        <tr>
            <td><?= decrypt($getRapports2["rapport"], $encryption_key, $ivRapp) ?></td>
            <?php $date = date(
                "dS M Y",
                strtotime(decrypt($getRapports2["created_at"], $encryption_key, $ivRapp))
            ); ?>
            <td><i class="fas fa-calendar-days"></i> <?= $date ?></td>
            <td><i class="fas fa-clock"></i> <?= decrypt($getRapports2["zeit"], $encryption_key, $ivRapp) ?></td>

            <?php
            // Converting the time into seconds
            $timeinsec = strtotime(decrypt($getRapports2["zeit"], $encryption_key, $ivRapp)) - $sum;

            // Sum the time with previous value
            $totaltime = $totaltime + $timeinsec;

            /* Check if task as been created under 24 hours */
            if (strtotime(decrypt($getRapports2["created_at"], $encryption_key, $ivRapp)) >= strtotime("-1 day")) {
                /* Task is younger than 24 hours */
                echo "<td class='editDelete'>
                                            <img onclick='editTime(" .
                    $getRapports2["rapportId"] .
                    ")'
                                            title='Edit rapport and time' src='images/edit.png' alt=''> / <img title='Delete rapport and time'
                                            onclick='deleteTime(" .
                    $getRapports2["rapportId"] .
                    ")' src='images/delete.png' alt=''>
                                        </td>";
            } /* When task is or older than 24 hours */ else {
                echo "<td></td>";
            }

                        }
                    }
                }
            }
            if ($rapportCounter == 0) {
                echo "<div>
                    <table class='data' id=" .
                    decrypt($getTitleOfTask2["titel"], $encryption_key, $ivTitle) .
                    ">
                        <tr><th><p>" .
                        decrypt($getTitleOfTask2["titel"], $encryption_key, $ivTitle) .
                    "</p></th></tr>
                        <tr>
                            <td style='color:red; text-align:center;'><strong>No rapports found</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>";
            }
            if ($rapportCounter < 5 && $rapportCounter != 0) {

                $h = intval($totaltime / 3600);

                $totaltime = $totaltime - $h * 3600;

                $m = intval($totaltime / 60);

                $s = $totaltime - $m * 60;

                $timeinsec = strtotime(decrypt($getRapports2["zeit"], $encryption_key, $ivRapp)) - $sum;

                $totaltime = $totaltime + $timeinsec;

                // Printing the result
                echo "
                    <tr>
                        <td><strong>Total time spent: <font color='red'>";
                ?>
            <?php
            $Time = new TimeController();
            $Time->formatTimeOutput($h, $m, $s);
            echo "</font></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>";

            }
        } ?>
    </div>
    <?php } else { ?>
    <div class="noData">
        <h1>No tasks therefore no records</h1>
        <p>Add a task, work on it by creating a record and then you'll find it here</p>
    </div>
    <?php } ?>
    <div id="nothingFound">
        <h1>Nothing found</h1>
        <img src="images/sad_smiley.png" alt="">
    </div>
    </div>
    </table>
    </div>
    </div>
    <?php include "app/Views/footer.view.php"; ?>
</body>

</html>