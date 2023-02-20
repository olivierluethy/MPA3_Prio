<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Admin</title>
</head>

<body>

    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");
    ?>

    <?php if ($getEssaysCounter > 0): ?>
        <?php foreach ($getEssays as $getEssays2): ?>
            <div class="essay">
                <table>
                    <tr>
                        <th><?= $getEssays2['titel'] ?></th>
                        <th></th>
                        <th><button onclick="showEssay(<?= $getEssays2['essayId'] ?>)">Open &nbsp<i class="fa fa-external-link"></i></button></th>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="noData">
            <h1>There are no essays yet!</h1>
            <p>As soon as a user has written an essay, it'll appear here!</p>
        </div>
    <?php endif; ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/time_recording.js"></script>
    <script src="public/js/validation.js"></script>
    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>