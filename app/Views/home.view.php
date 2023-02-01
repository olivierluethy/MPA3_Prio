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
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>
</head>

<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="part1" onclick="home()">
            <img src="images/logo.png" alt="">
            <h1>Prio</h1>
        </div>
        <div class="part2" id="nav">
            <?php
            /* When the user is not logged in */
            if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                echo "<button class='loginBtn' onclick='goToLogin()'>Login  <i class='fas fa-sign-in-alt'></i></button>";
            }else{
                /* Admin User */
                if($_SESSION['role'] == 1){
                    echo "<a title='Go to the admin area' href='admin'>Admin Area</a>";
                    echo "<button title='Log you out from the system' onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Blocked User */
                if($_SESSION['role'] == 2){
                    echo "<button title='Go to tasks' class='active' onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button title='Log you out from the system' onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
                /* Normal user */
                if($_SESSION['role'] == 0) {
                    echo "<button title='Go to tasks' class='active' onclick='aufgaben()'>Tasks <i class='fas fa-tasksfas fa-tasks'></i></button>";
                    echo "<button title='See all time records' onclick='zeiterfassung()'>Time recording <i class='fas fa-clock'></i></button>";
                    echo "<button title='Log you out from the system' onclick='zuLogout()'>Logout  <i class='fas fa-sign-out-alt'></i></button>";
                }
            }
            ?>
            <a href="javascript:void(0);" class="icon" onclick="responsive()">
                <i class="fa fa-bars"></i>
            </a>
        </div>
    </nav>

    <?php
    /* If the user is blocked from the site */
    if($_SESSION['role'] == 2){
        echo "<div class='write_essay'>
            <h1>You don't have access anymore!</h1>
            <p>You have completed a task 10 times too late. That's why you don't have access anymore.</p>
            <button id='showEssayField' onclick='write_essay()'>Write an essay to get access again</button>
            <form id='essay' action='add_essay' method='POST'>
                <label>Title:</label><br>
                <input id='title' type='text' name='title'><br>
                <textarea name='essay' id='essay_content'></textarea><br>
                <button type='submit'>Send essay</button>
            </form>
        </div>";
    }
    /* If the user isn't blocked from the site */
    else if($_SESSION['role'] == 0){
        if($getAllTasksCounter > 0){
            echo "<table class='switch'>
                    <tr>
                        <th><button id='openButton' onclick='navSwitch(1)'>Open ";
                        if($getAllTasksOpenCounter == 0){
                            echo "(empty) </button></th>";
                        }else{
                            echo "(". $getAllTasksOpenCounter.")</th>" ;
                        }
                        echo "<th></th>
                        <th><button id='doneButton' onclick='navSwitch(2)'>Completed ";
                        if($getAllTasksDoneCounter == 0){
                            echo "(empty) </button></th>";
                        }else{
                            echo "(". $getAllTasksDoneCounter.")" ;
                        }
                    echo "</tr>
                </table>";
            /* For open tasks */
            echo "<div id='open'>";
                if($getAllTasksOpenCounter > 0){
                    echo "
                        <table class='order'>
                            <tr>
                                <td>
                                    <h2>All open tasks sorted by:</h2>
                                </td>
                                <td>
                                    <!-- Choose sorting option -->
                                    <form action='' method='GET'>
                                        <select name='sort' id=''>
                                            <option value='priority'";?><?php if(isset($_GET['sort_alphabet']) && $_GET['sort_alphabet'] == "priority"){echo "selected";}?><?php echo ">Priority</option>
                                            <option value='alphabet'";?><?php if(isset($_GET['sort_alphabet']) && $_GET['sort_alphabet'] == "alphabet"){echo "selected";}?><?php echo ">Alphabet</option>
                                            <option value='deadline'";?><?php if(isset($_GET['sort_alphabet']) && $_GET['sort_alphabet'] == "deadline"){echo "selected";}?><?php echo ">Deadline</option>
                                        </select>
                                        <button title='Sort all tasks' type='submit'>Sort <i class='fa fa-sort'></i></button>
                                    </form>
                                </td>
                                <td><button title='Click on this button to add a task' class='plus' onclick='addTask()'><i class='fa fa-plus'></i></button></td>
                            </tr>
                        </table>
                    <main>";
                    
                        $con = mysqli_connect("localhost", "root", "", "prio");
                        $sort_option = "prioritaet";
                        if(isset($_GET['sort']))
                        {
                            if($_GET['sort'] == "alphabet"){
                                $sort_option = "titel, beschreibung, motivation";
                            }else if($_GET['sort'] == "priority"){
                                $sort_option = "prioritaet DESC";
                            }else if($_GET['sort'] == "deadline"){
                                $sort_option = "deadline ASC";
                            }
                        }
                        $query = "SELECT * FROM aufgabe WHERE fk_benutzerId = ". $_SESSION['id'] ." AND status = 0 ORDER BY $sort_option";
                        $query_run = mysqli_query($con, $query);

                        if(mysqli_num_rows($query_run) > 0){
                            foreach($query_run as $row){?>
    <div class='task'>
        <table>
            <tr>
                <th>
                    <h1><?= $row['titel'];?></h1>
                </th>
                <th><textarea readonly class="ckeditor" name="description"
                        id="description_open"><?php echo $row['beschreibung'] ?></textarea></th>
                <th><textarea readonly class="ckeditor" name=""
                        id="motivation_open"><?php echo $row['motivation']?></textarea></th>
                <!-- Format date -->
                <?php $date = date('dS M Y', strtotime($row['deadline']));?>
                <th><?php echo $date ?></th><?php
                                            /* Check if task as been created under 24 hours */
                                            if(strtotime($row['created_at']) >= strtotime('-1 day')) {
                                                /* Task is younger than 24 hours */
                                                echo "
                                                <th>
                                                    <img title='Edit task' onclick='editTask(" . $row['aufgabeId'] . ")' src='images/edit.png' alt=''>
                                                    <img title='Delete task' onclick='deleteTask(" . $row['aufgabeId'] . ")' src='images/delete.png' alt=''>
                                                </th>";
                                            }
                                            /* When task is or older than 24 hours */
                                            else{
                                                echo "<th>This task can't be edited or deleted anymore!</th>";
                                            }?>
                <th>
                    <h1 id='active_time<?= $row['aufgabeId'];?>'>00:00:00</h1>
                </th>
                <th>
                    <img title='Start recording' id='start<?= $row['aufgabeId'];?>'
                        onclick="start_recording(<?= $row['aufgabeId'];?>)" src='images/clock off.png' alt=''>
                </th>
                <th><button title='Complete task' id='completeBtn'
                        onclick="completeTask(<?= $row['aufgabeId']; ?>)">Complete task <i
                            class='fa fa-check'></i></button></th>
                <th>
                    <div class='updown'>
                        <img title='Increase priority' onclick="higherPrio(<?= $row['aufgabeId'];?>)"
                            src='images/up.png' alt=''><br>
                        <img title='Decrease priority' onclick="lowerPrio(<?= $row['aufgabeId'];?>)"
                            src='images/down.png' alt=''>
                    </div>
                </th>
            </tr>
        </table>
    </div><?php
                            }
                        }
                    
                    echo "</main>";
                }else{
                    echo "<div class='noData'>
                            <h1>No tasks available yet</h1>
                            <button onclick='addTask()'>Add task &nbsp<i class='fa fa-plus'></i></button>
                        </div>";
                }
            echo "</div>";
            /* For done tasks */
            echo "<div id='done'>";
                if($getAllTasksDoneCounter > 0){
                    echo "
                        <table class='order'>
                            <tr>
                                <td><h2>All done tasks</h2></td>
                            </tr>
                        </table>
                    <main>";
                    foreach ($getAllTasksDone as $getAllTasksDone2){
                        echo "<div class='task'>
                            <table>
                                <tr>
                                    <th>
                                        <h1>". $getAllTasksDone2['titel'] . "</h1>
                                    </th>
                                    <th><textarea readonly class='ckeditor' name='description' id='description_done'>"?><?php echo $getAllTasksDone2['beschreibung'] . "</textarea></th>
                                    <th><textarea readonly class='ckeditor' name='description' id='motivation_done'>"?><?php echo $getAllTasksDone2['motivation'] . "</textarea></th>";
                                    $date = date('dS M Y', strtotime($getAllTasksDone2['deadline']))?>
    <th><?php echo $date ?></th><?php echo "
                                    <th><img title='Delete task' onclick='deleteTask(" . $getAllTasksDone2['aufgabeId'] . ")' src='images/delete.png' alt=''></th>
                                </tr>
                            </table>
                        </div>";
                    }
                    echo "</main>";
                echo "</div>";
                }else{
                    echo "<div class='noData'>
                            <h1>No task completed yet</h1>
                            <p>When you have completed an open task, it'll appear here!</p>
                          </div>";
                }
            echo "</div>";
        }else {
            echo "<div class='noData'>
                    <h1>Currently no task available</h1>
                    <button title='Click on this button to add a task' onclick='addTask()'>Add task <i class='fa fa-plus'></i></button>
                 </div>";
        }
    }
    ?>

    <script src="ckeditor/ckeditor.js"></script>

    <script>
    CKEDITOR.replace('essay_content');
    </script>

    <script src="public/js/responsive.js"></script>
    <script src="public/js/ValidEssay.js"></script>
    <script src="public/js/routes.js"></script>
    <script src="public/js/openDone.js"></script>
    <script src="public/js/time_recording.js"></script>
    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>