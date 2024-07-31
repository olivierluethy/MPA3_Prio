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

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/validation.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Admin</title>
</head>

<body>

    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <!-- Essays Display -->
    <?php if (count($getEssays) > 0): ?>
        <?php foreach ($getEssays as $getEssays2): ?>
            <div class='essay'>
                <table>
                    <tr>
                        <th><?= htmlspecialchars($getEssays2['titel'], ENT_QUOTES, 'UTF-8') ?></th>
                        <th></th>
                        <th>
                            <button onclick='showEssay(<?= intval($getEssays2['essayId']) ?>)'>
                                Open &nbsp;<i class='fa fa-external-link'></i>
                            </button>
                        </th>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class='noData'>
            <h1>There are no essays yet!</h1>
            <p>As soon as a user has written an essay, it'll appear here!</p>
        </div>
    <?php endif; ?>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>