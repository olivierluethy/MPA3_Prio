<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="widtd=device-widtd, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/history.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">

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
<h1>The history of task: <?= $getHistorys[0][0];?></h1>
<?php echo "<h2><strong>Total time spent: <font color='red'>" . e($totaltime) . "</font></strong></h2>";?>
    <table>
        <tr>
            <th>Rapport</th>
            <th>Time</th>
            <th>Created at</th>
        </tr>
        <?php
    foreach($getHistorys as $getHistory){?>
        <tr>
            <td><?= $getHistory['rapport']; ?></td>
            <td>Duration: <?= $getHistory['zeit']; ?></td>
            
            <?php $date = date('dS M Y', strtotime($getHistory['created_at'])); ?>
            <td>Created on the <?= $date ?></td>
        </tr>
        <?php } ?>

    </table>
    <?php include ('app/Views/footer.view.php'); ?>
</body>

</html>