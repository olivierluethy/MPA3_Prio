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
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");
    ?>

    <div class="hero-image">
        <div class="hero-text">
            <h1 style="font-size:50px">Welcome To Prio</h1>
            <h2>Set your priorities <strong>quickly</strong> and <strong>correctly</strong> to meet all your goals.</h2>
            <p>That is our motto.</p>
        </div>
    </div>
    <h1 style="text-align:center;">What is this project about?</h1>
    <h3 style="margin-left:2rem; color: gray;">
        This project is concretely about accomplishing tasks that you set for yourself.
        Usually, people set too many new goals for the new year, or for the new age. The problem is, most of them are
        not described in a meaningful way so that they are realistic and achievable.
        This app is designed to help fix that problem as best it can.

        In this app you can add tasks or goals that you would like to have done or achieved. You define a title and
        describe what you would like to achieve and the motivation behind it.
        Why do you want to achieve it and where are the benefits?
        What will it bring me in the future, or am I just doing it for fun?

        These are the questions you should ask yourself before you set a new goal.</h3>
    <script src="public/js/routes.js"></script>
    <script src="public/js/validation.js"></script>
    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>