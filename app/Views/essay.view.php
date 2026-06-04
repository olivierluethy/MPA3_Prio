<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validationBenutzerBearbeiten.js"></script>
    <script defer src="public/js/footer.js"></script>

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

    <main class="mx-auto max-w-3xl px-4 py-8">
        <div class="essay card">
            <h1 class="mb-4 text-2xl font-bold text-white"><u><?= $getEssay[0][1] ?></u></h1>
            <textarea readonly class='ckeditor' id='essay'><?= $getEssay[0][2] ?></textarea>
            <div class='acre mt-6 flex justify-center gap-3'>
                <button class='acbtn inline-flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-green-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-green-500' onclick='accept(<?= $getEssay[0][0], $getEssay[0][4] ?>)'>Accept</button>
                <button class='rebtn inline-flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-red-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-red-500' onclick='refuse(<?= $getEssay[0][0], $getEssay[0][4] ?>)'>Refuse</button>
            </div>
        </div>
    </main>

    <script src="ckeditor/ckeditor.js"></script>

    <script>CKEDITOR.replace('essay');</script>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
