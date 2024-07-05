<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/home.css">
    <link rel="stylesheet" href="public/css/navigation.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/openDone.js"></script>
    <script defer src="public/js/time_recording.js"></script>
    <script defer src="public/js/footer.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__); // aktueller dateiname (wird für header.php benötigt)
    include "header.php";
    ?>

    <?php /* If the user is blocked from the site */
    if ($_SESSION["role"] == 2) { ?>
    <div class='write_essay'>
            <h1>You don't have access anymore!</h1>
            <p>You have completed a task 10 times too late. That's why you don't have access anymore.</p>
            <button id='showEssayField' onclick='write_essay()'>Write an essay to get access again</button>
            <form id='essay' action='add_essay' method='POST'>
                <label>Title:</label><br>
                <input id='title' type='text' name='title'><br>
                <textarea name='essay' id='essay_content'></textarea><br>
                <button type='submit'>Send essay</button>
            </form>
        </div>
<?php }
    /* If the user isn't blocked from the site */ elseif (
        $_SESSION["role"] == 0
    ) {
        if (count($getAllTasks) > 0) {
            echo "<table class='switch'>
                    <tr>
                        <th><button id='openButton' onclick='navSwitch(1)'>Open ";
            if (count($getAllTasksOpen) == 0) {
                echo "(empty) </button></th>";
            } else {
                echo "(" . count($getAllTasksOpen) . ")</th>";
            }
            echo "<th></th>
                        <th><button id='doneButton' onclick='navSwitch(2)'>Completed ";
            if (count($getAllTasksDone) == 0) {
                echo "(empty) </button></th>";
            } else {
                echo "(" . count($getAllTasksDone) . ")";
            }
            echo "</tr>
                </table>";
            /* For open tasks */
            echo "<div id='open'>";
            if (count($getAllTasksOpen) > 0) { ?>
    <table class="order">
        <tr>
            <td>
                <h2>All open tasks sorted by:</h2>
            </td>
            <td>
                <!-- Choose sorting option -->
                <form action="" method="GET">
                    <select name="sort">
                        <option value="priority"
                            <?php if (
                                isset($_GET["sort"]) &&
                                $_GET["sort"] == "priority"
                            ) {
                                echo "selected";
                            } ?>>
                            Priority</option>
                        <option value="alphabet"
                            <?php if (
                                isset($_GET["sort"]) &&
                                $_GET["sort"] == "alphabet"
                            ) {
                                echo "selected";
                            } ?>>
                            Alphabet</option>
                        <option value="deadline"
                            <?php if (
                                isset($_GET["sort"]) &&
                                $_GET["sort"] == "deadline"
                            ) {
                                echo "selected";
                            } ?>>
                            Deadline</option>
                    </select>
                    <button title='Sort all tasks' type='submit'>Sort <i class='fa fa-sort'></i></button>
                </form>
            </td>
            <td><button title='Click on this button to add a task' class='plus' onclick='addTask()'><i
                        class='fa fa-plus'></i></button></td>
        </tr>
    </table>
    <main>
        <?php
        // make a connector
        $sort_option = "prioritaet DESC";
        if (isset($_GET["sort"])) {
            if ($_GET["sort"] == "alphabet") {
                $sort_option = "titel, beschreibung, motivation";
            } elseif ($_GET["sort"] == "priority") {
                $sort_option = "prioritaet ASC";
            } elseif ($_GET["sort"] == "deadline") {
                $sort_option = "deadline ASC";
            }
        }

        $Task = new Task();
        $getObjects = $Task->sortTask($sort_option);
        $getObjects = $getObjects->fetchAll();

        $rowCounter = 0;
        foreach ($getObjects as $getObject) {

            $rowCounter++;
            if ($rowCounter == 1) {
                echo "<div class='task' style='background-color: #FFD700;'>";
            } elseif ($rowCounter == 2) {
                echo "<div class='task' style='background-color: #C0C0C0;'>";
            } elseif ($rowCounter == 3) {
                echo "<div class='task' style='background-color: #CD7F32;'>";
            } else {
                echo "<div class='task' style='background-color: #99CCFF;'>";
            }
            ?>

        <table>
            <tr>
                <th>
                    <h1><?= $getObject["titel"] ?></h1>
                </th>
                <th><textarea readonly class="ckeditor" name="description"
                        id="description_open"><?php echo $getObject[
                            "beschreibung"
                        ]; ?></textarea></th>
                <th><textarea readonly class="ckeditor" name=""
                        id="motivation_open"><?php echo $getObject[
                            "motivation"
                        ]; ?></textarea></th>
                <!-- Format date -->
                <?php $date = date(
                    "dS M Y",
                    strtotime($getObject["deadline"])
                ); ?>
                <th><?php echo $date; ?></th><?php /* Check if task as been created under 24 hours */
if (strtotime($getObject["created_at"]) >= strtotime("-1 day")) {
    /* Task is younger than 24 hours */
    echo "
                                                <th>
                                                    <img title='Edit task' onclick='editTask(" .
        $getObject["aufgabeId"] .
        ")' src='images/edit.png' alt=''>
                                                    <img title='Delete task' onclick='deleteTask(" .
        $getObject["aufgabeId"] .
        ")' src='images/delete.png' alt=''>
                                                </th>";
} /* When task is or older than 24 hours */ else {
    echo "<th></th>";
} ?>
                <th>
                    <h1 id='active_time<?= $getObject[
                        "aufgabeId"
                    ] ?>'>00:00:00</h1>
                </th>
                <th>
                    <img title='Start recording' id='start<?= $getObject[
                        "aufgabeId"
                    ] ?>'
                        onclick="start_recording(<?= $getObject[
                            "aufgabeId"
                        ] ?>)" src='images/clock off.png' alt=''>
                </th>
                <th><button title='Complete task' id='completeBtn'
                        onclick="completeTask(<?= $getObject[
                            "aufgabeId"
                        ] ?>)">Complete task <i
                            class='fa fa-check'></i></button></th>
                <th>
                    <img title='Increase priority' onclick="higherPrio(<?= $getObject[
                        "aufgabeId"
                    ] ?>)"
                        src='images/up.png' alt=''><br>
                    <p><?= $getObject["prioritaet"] ?></p>
                    <?php if ($getObject["prioritaet"] > 0) { ?>
                    <img title='Decrease priority' onclick="lowerPrio(<?= $getObject[
                        "aufgabeId"
                    ] ?>)"
                        src='images/down.png' alt=''>
                    <?php } ?>
                </th>
            </tr>
        </table>
        </div><?php
        }

        echo "</main>";
        } else {echo "<div class='noData'>
                            <h1>No tasks available yet</h1>
                            <button onclick='addTask()'>Add task &nbsp<i class='fa fa-plus'></i></button>
                        </div>";}
            echo "</div>";
            /* For done tasks */
            echo "<div id='done'>";
            if (count($getAllTasksDone) > 0) {
                echo "
                        <table class='order'>
                            <tr>
                                <td><h2>All done tasks</h2></td>
                            </tr>
                        </table>
                    <main>";
                foreach ($getAllTasksDone as $getAllTasksDone2) {

                    echo "<div class='task'>
                            <table>
                                <tr>
                                    <th>
                                        <h1>" .
                        $getAllTasksDone2["titel"] .
                        "</h1>
                                    </th>
                                    <th><textarea readonly class='ckeditor' name='description' id='description_done'>";
                    echo $getAllTasksDone2["beschreibung"] .
                        "</textarea></th>
                                    <th><textarea readonly class='ckeditor' name='description' id='motivation_done'>";
                    echo $getAllTasksDone2["motivation"] . "</textarea></th>";
                    $date = date(
                        "dS M Y",
                        strtotime($getAllTasksDone2["deadline"])
                    );
                    ?>
        <th><?php echo $date; ?></th><?php echo "
                                    <th><img title='Delete task' onclick='deleteTask(" .
    $getAllTasksDone2["aufgabeId"] .
    ")' src='images/delete.png' alt=''></th>
                                </tr>
                            </table>
                        </div>";
                }
                echo "</main>";
                echo "</div>";
            } else {
                echo "<div class='noData'>
                            <h1>No tasks have been completed yet</h1>
                            <p>When you complete an open task, it will appear here!</p>
                          </div>";
            }
            echo "</div>";
        } else {
            echo "<div class='noData'>
                    <h1>Currently no task available</h1>
                    <button title='Click on this button to add a task' onclick='addTask()'>Add task <i class='fa fa-plus'></i></button>
                 </div>";
        }
    } ?>

        <script src="ckeditor/ckeditor.js"></script>

        <script>
        CKEDITOR.replace('essay_content');
        </script>

        <?php include "app/Views/addRapportModal.view.php"; ?>
        <?php include "app/Views/footer.view.php"; ?>
</body>

</html>