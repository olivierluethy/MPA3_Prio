<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/add.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Edit Task</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include("header.php");

    // Set the encryption method
$encryption_method = "AES-256-CBC";

// Set the secret key and iv
$secret_key = 'my_secret_key';
$secret_iv = 'my_secret_iv';

// Hash the secret key and iv
$key = hash('sha256', $secret_key);
$iv = substr(hash('sha256', $secret_iv) , 0, 16);

$encrypted_titel = base64_decode($getTask[0][1]);
$decrypted_titel = openssl_decrypt($encrypted_titel, $encryption_method, $key, 0, $iv);
$escaped_titel = htmlspecialchars($decrypted_titel);

$encrypted_description = base64_decode($getTask[0][2]);
$decrypted_description = openssl_decrypt($encrypted_description, $encryption_method, $key, 0, $iv);
$escaped_description = htmlspecialchars($decrypted_description);

$encrypted_motivation = base64_decode($getTask[0][3]);
$decrypted_motivation = openssl_decrypt($encrypted_motivation, $encryption_method, $key, 0, $iv);
$escaped_motivation = htmlspecialchars($decrypted_motivation);

$encrypted_deadline = base64_decode($getTask[0][4]);
$decrypted_deadline = openssl_decrypt($encrypted_deadline, $encryption_method, $key, 0, $iv);
$escaped_deadline = htmlspecialchars($decrypted_deadline);

$encrypted_priority = base64_decode($getTask[0][5]);
$decrypted_priority = openssl_decrypt($encrypted_priority, $encryption_method, $key, 0, $iv);
$escaped_priority = htmlspecialchars($decrypted_priority);
    ?>

    <form action="edit_task?id=<?= $getTask[0][0] ?>" method="POST">
        <h2>Edit Task</h2>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" id="title" value="<?= $escaped_titel ?>"><br>
        <label for="lname">Description:</label><br>
        <textarea name="description" id="description"><?= $escaped_description ?></textarea><br>
        <label for="motivation">Motivation:</label><br>
        <textarea name="motivation" id="motivation"><?= $escaped_motivation ?></textarea><br>
        <label for="deadline">Deadline:</label><br>
        <input type="date" id="deadline" name="deadline" value="<?= $escaped_deadline ?>"><br>
        <label for="priority">Priorität:</label><br>
        <input type="number" id="priority" name="priority" id="priority" value="<?= $escaped_priority ?>"><br>
        <input type="submit" class="" name="addTask" value="Edit task"><br><br>
    </form>

    <script src="ckeditor/ckeditor.js"></script>

    <script>
    CKEDITOR.replace('description');
    CKEDITOR.replace('motivation');
    </script>

    <?php include('app/Views/footer.view.php'); ?>

    <script src="public/js/routes.js"></script>
    <script src="public/js/validationAddTask.js"></script>
</body>

</html>