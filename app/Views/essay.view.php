<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/home.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validationBenutzerBearbeiten.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Benutzer bearbeiten</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <div class="essay">
        <h1><u><?= $getEssay[0][1] ?></u></h1>
        <td><textarea readonly class='ckeditor' id='essay'><?= $getEssay[0][2] ?></textarea></td>
        <div class='acre'>
            <button class='acbtn' onclick='accept(<?= $getEssay[0][0], $getEssay[0][4] ?>)'>Accept</button>
            <button class='rebtn' onclick='refuse(<?= $getEssay[0][0], $getEssay[0][4] ?>)'>Refuse</button>
        </div>
    </div>

    <script src="ckeditor/ckeditor.js"></script>

    <script>CKEDITOR.replace('essay');</script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>