<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validationEditRapport.js"></script>
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Benutzer bearbeiten</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <main class="mx-auto max-w-2xl px-4 py-10">
        <form action="edit_Time?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" method="POST" class="card space-y-4">
            <h2 class="text-2xl font-bold text-white">Edit time record</h2>
            <div>
                <label for="rapport" class="label">Rapport:</label>
                <input id="rapport" type="text" name="rapport" value="<?= $rapport ?>" class="input" />
            </div>
            <div>
                <label for="appt-time" class="label">Time:</label>
                <input id="appt-time" type="time" name="time" step="2" value="<?= $zeit ?>" class="input" />
            </div>
            <input type="submit" value="Edit time" class="btn-primary w-full">
        </form>
    </main>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
