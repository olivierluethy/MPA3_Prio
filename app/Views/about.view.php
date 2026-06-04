<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validation.js"></script>
    <script defer src="public/js/footer.js"></script>
    <script defer src="public/js/parallax.js"></script>
    <script defer src="public/js/responsive.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <!-- Parallax Scroll -->
    <section class='parallax-image relative flex h-[60vh] flex-col items-center justify-center overflow-hidden bg-surface-950 text-center'>
        <img src='images/logo-parallax.png' class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-20" />
        <div class="relative z-10 px-4">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl">Welcome to Prio</h1>
            <h2 class="mt-4 text-lg text-surface-200">Prioritize <strong class="text-brand-400">efficiently</strong> and <strong class="text-brand-400">accurately</strong> to achieve your goals.</h2>
            <p class="mt-2 text-surface-400">That’s our promise.</p>
        </div>
    </section>

    <main class="mx-auto max-w-3xl space-y-8 px-4 py-12">
        <section class="card">
            <h1 class="text-2xl font-bold text-white">About This Project</h1>
            <p class="mt-3 text-surface-300">
                Prio is designed to help you accomplish the tasks you set for yourself. Many people set numerous goals for the new year or a new phase of life but often fail to define them in a realistic and achievable way. Prio aims to address this issue effectively.
            </p>
            <p class="mt-3 text-surface-300">
                With Prio, you can add tasks or goals you want to achieve. Define a title, describe your objective, and explain the motivation behind it. Ask yourself:
            </p>
            <ul class="mt-3 list-disc space-y-1 pl-6 text-surface-300">
                <li>Why do I want to achieve this?</li>
                <li>What benefits will it bring?</li>
                <li>Is it for personal growth, professional development, or just for fun?</li>
            </ul>
        </section>

        <section class="card">
            <h1 class="text-2xl font-bold text-white">Set Meaningful Goals</h1>
            <p class="mt-3 text-surface-300">Consider your goals carefully and ensure they are significant and attainable.<br>
            To help you do that, you need to enter a brief description of your task and your motivation. Why do you want to do it and how hard do you want to do it?</p>
        </section>

        <section class="card">
            <h1 class="text-2xl font-bold text-white">Track Your Progress Intuitively</h1>
            <p class="mt-3 text-surface-300">Forget about constantly checking the clock or lists. Our intuitive dashboard offers a seamless time management solution.<br>
            For those who take it seriously, they can see the entire history on a spreadsheet of what they've all done, how long the day took and the total amount calculated and displayed all for you.</p>
        </section>

        <section class="card">
            <h1 class="text-2xl font-bold text-white">Achieve Your Goals Quickly</h1>
            <p class="mt-3 text-surface-300">Achieving your goals, especially when learning new skills, requires dedication. Prio’s dashboard helps you track the time spent on each task and monitor your progress effectively.</p>
        </section>

        <section class="card">
            <h1 class="text-2xl font-bold text-white">Are there special things?</h1>
            <p class="mt-3 text-surface-300">Since our app focuses on prioritization and therefore on productivity, the deadline is also very important whether you meet it or not. Just notice how often you don't complete a task within your deadline. If it happens that you don't meet it at all, you're in for a surprise.</p>
        </section>

        <section class="card">
            <h1 class="text-2xl font-bold text-white">How save is this app?</h1>
            <p class="mt-3 text-surface-300">All data you store within this application is fully encrypted. For user data, we have even added a separate security layer that makes it impossible for us to retrieve user data in clear text. So even if you've added very personal or classified data about something, we can't see it.</p>
        </section>
    </main>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
