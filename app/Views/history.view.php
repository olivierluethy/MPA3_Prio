<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">

    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <title>History</title>
</head>

<body>

<!-- Navigation Bar -->
<?php
$actual_link = basename(__FILE__);
include ("header.php");
?>

<main class="mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-2xl font-bold text-white">The history of task: <?= $getHistorys[0][0];?></h1>
    <?php echo "<h2 class='mt-2 text-surface-300'><strong>Total time spent: <span class='text-red-400'>" . e($totaltime) . "</span></strong></h2>";?>

    <div class="card mt-6 overflow-x-auto p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Rapport</th>
                    <th>Time</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($getHistorys as $getHistory){?>
                <tr>
                    <td><?= $getHistory['rapport']; ?></td>
                    <td>Duration: <?= $getHistory['zeit']; ?></td>
                    <?php $date = date('dS M Y', strtotime($getHistory['created_at'])); ?>
                    <td>Created on the <?= $date ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>
<?php include ('app/Views/footer.view.php'); ?>
</body>

</html>
