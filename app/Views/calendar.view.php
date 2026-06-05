<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/vendor/fullcalendar/index.global.min.js"></script>
    <script defer src="public/js/taskModal.js"></script>
    <script defer src="public/js/timeRecords.js"></script>
    <script defer src="public/js/calendar.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Calendar</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include "header.php";
    ?>

    <main class="mx-auto max-w-6xl px-4 py-8">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">Calendar</h1>
                <p class="mt-1 text-sm text-surface-400">Plan tasks by deadline and review reported time. Drag an item to reschedule.</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-surface-400">
                <span class="inline-flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-sm" style="background:#4f46e5"></span> Task (deadline)</span>
                <span class="inline-flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-sm" style="background:#0d9488"></span> Time report</span>
            </div>
        </div>

        <div class="card p-3 sm:p-5">
            <div id="calendar"></div>
        </div>
    </main>

    <!-- CKEditor is needed by the reused task edit modal -->
    <script src="ckeditor/ckeditor.js"></script>

    <?php include "app/Views/taskModal.view.php"; ?>
    <?php include "app/Views/timeEditModal.view.php"; ?>
    <?php include "app/Views/footer.view.php"; ?>
</body>

</html>
