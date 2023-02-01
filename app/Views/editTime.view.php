<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/add.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Benutzer bearbeiten</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");
    ?>

    <form action="edit_Time?id=<?= $getRapport[0][0] ?>" method="POST">
        <h2>Edit time record</h2>
        <label for="rapport">Rapport:</label><br>
        <input id="rapport" type="text" name="rapport" value="<?= $getRapport[0][1] ?>" /><br>
        <label for="passwort">Time:</label><br>
        <input id="appt-time" type="time" name="time" step="2" value="<?= $getRapport[0][2] ?>" /><br>
        <input type="submit" value="Edit time"><br>
    </form>

    <?php include('app/Views/footer.view.php'); ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/validationEditRapport.js"></script>
</body>

</html>