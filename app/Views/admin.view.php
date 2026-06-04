<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/validation.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Admin</title>
</head>

<body>

    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <main class="mx-auto max-w-3xl px-4 py-8">
        <h1 class="mb-6 text-2xl font-bold text-white">Submitted essays</h1>

        <!-- Essays Display -->
        <?php if (count($getEssays) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($getEssays as $getEssays2): ?>
                    <div class='essay card flex items-center justify-between gap-4'>
                        <h3 class="text-lg font-semibold text-white"><?= htmlspecialchars($getEssays2['titel'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <button class="btn-primary" onclick='showEssay(<?= intval($getEssays2['essayId']) ?>)'>
                            Open &nbsp;<i class='fa fa-external-link'></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class='noData'>
                <h1 class="text-2xl font-bold text-red-400">There are no essays yet!</h1>
                <p class="text-surface-300">As soon as a user has written an essay, it'll appear here!</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
