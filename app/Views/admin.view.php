<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Admin</title>
</head>

<body>

    <!-- Navigation Bar -->
    <nav>
        <div class="part1" onclick="home()">
            <img src="images/logo.png" alt="">
            <h1>Prio</h1>
        </div>
        <div class="part2">
            <a class="active" href='admin'>Admin Area</a>
            <button class='logoutBtn' onclick='zuLogout()'>Logout <i class='fas fa-sign-out-alt'></i></button>
        </div>
    </nav>

    <?php
        if($getEssaysCounter > 0){
            foreach($getEssays as $getEssays2){
                echo "<div class='essay'>
                        <table>
                            <tr>
                                <th>" . $getEssays2['titel'] . "</th>
                                <th></th>
                                <th><button onclick='showEssay(" . $getEssays2['essayId'] . ")'>Open &nbsp<i class='fa fa-external-link'></i></button></th>
                            </tr>
                        </table>
                      </div>";
            }
        }else{
            echo "<div class='noData'>
                    <h1>There are no essays yet!</h1>
                    <p>As soon as an user has written an essay, it'll appear here!</p>
                  </div>";
                }
        ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/time_recording.js"></script>
    <script src="public/js/validation.js"></script>
    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>