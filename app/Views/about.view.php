<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/about.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/validation.js"></script>
    <script defer src="public/js/footer.js"></script>
    <script defer src="public/js/parallax.js"></script>
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");
    ?>

    <!-- <div class="hero-image">
        <div class="hero-text">
            <h1>Welcome to Prio</h1>
            <h2>Prioritize <strong>efficiently</strong> and <strong>accurately</strong> to achieve your goals.</h2>
            <p>That’s our promise.</p>
        </div>
    </div> -->
    <!-- Parallax Scroll -->
    <section class='parallax-image'>
        <img src='images/logo-parallax.png' />
        <h1>Welcome to Prio</h1>
        <h2>Prioritize <strong>efficiently</strong> and <strong>accurately</strong> to achieve your goals.</h2>
        <p>That’s our promise.</p>
    </section>

    <main>
        <h1>About This Project</h1>
        <h3>
            Prio is designed to help you accomplish the tasks you set for yourself. Many people set numerous goals for the new year or a new phase of life but often fail to define them in a realistic and achievable way. Prio aims to address this issue effectively.
        </h3>
        <h3>
            With Prio, you can add tasks or goals you want to achieve. Define a title, describe your objective, and explain the motivation behind it. Ask yourself:
            <ul>
                <li>Why do I want to achieve this?</li>
                <li>What benefits will it bring?</li>
                <li>Is it for personal growth, professional development, or just for fun?</li>
            </ul>
        </h3>
        <h1>Set Meaningful Goals</h1>
        <h3>Consider your goals carefully and ensure they are significant and attainable.</h3>
        
        <h1>Track Your Progress Intuitively</h1>
        <h3>Forget about constantly checking the clock or lists. Our intuitive dashboard offers a seamless time management solution.</h3>
        
        <h1>Achieve Your Goals Quickly</h1>
        <h3>Achieving your goals, especially when learning new skills, requires dedication. Prio’s dashboard helps you track the time spent on each task and monitor your progress effectively.</h3>
    </main>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>