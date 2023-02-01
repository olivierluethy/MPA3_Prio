<?php

class TaskController
{
	public function home(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: about');
		}
		/* Admin */
		if($_SESSION['role'] == 1){
			header("location: admin");
		}else {
			$Task = new Task();
			$pdo = connectDatabase();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			/* Get all tasks */
			$getAllTasks = $Task -> getAllTasks();
			$getAllTasks = $getAllTasks -> fetchAll();
			
			/* Get all open tasks */
			$getAllTasksOpen = $Task -> getAllTasksOpen();
			$getAllTasksOpen = $getAllTasksOpen -> fetchAll();
	
			$getAllTasksCounter = 0;
			$getAllTasksOpenCounter = 0;

			foreach ($getAllTasks as $getAllTasks2){
				$getAllTasksCounter++;
			}
			foreach ($getAllTasksOpen as $getAllTasksOpen2){
				$getAllTasksOpenCounter++;
			}

			// Done Tasks
			/* Get all tasks */
			$getAllTasksDone = $Task -> getAllTasksDone();
			$getAllTasksDone = $getAllTasksDone -> fetchAll();
	
			$getAllTasksDoneCounter = 0;
	
			foreach ($getAllTasksDone as $getAllTasksDone2){
				$getAllTasksDoneCounter++;
			}
		}
		require 'app/Views/home.view.php';
	}

	public function about(){
		// Initialize the session
        session_start();

		require 'app/Views/about.view.php';
	}

	public function add_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
			$motivation = $_POST['motivation'];

			$deadline = $_POST['deadline'];
			$priority = $_POST['priority'];

            $Task->add_task($title, $description, $motivation, $deadline, $priority);

            header('Location: home');
        }
		require 'app/Views/addTask.view.php';
	}

	public function edit_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$id = $_GET['id'];

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = $_POST['title'];
            $beschreibung = $_POST['description'];
			$motivation = $_POST['motivation'];
			$deadline = $_POST['deadline'];
			$prioritaet = $_POST['priority'];
        
			$Task->edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id);

            header('Location: home');	
        }else{
			/* Get Data to edit */
			$getTask = $Task -> getTask($id);
        	$getTask = $getTask -> fetchAll();
        }
		require 'app/Views/editTask.view.php';
	}

	public function delete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$id = $_GET['id'];

		$Task->deleteTask($id);
        
        header('Location: home');	
	}

	public function higherPrio(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$id = $_GET['id'];

		$Task->higherPrio($id);
        
        header('Location: home');
	}

	public function lowerPrio(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$id = $_GET['id'];

		$Task->lowerPrio($id);
        
        header('Location: home');
	}

	public function complete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		/* Get Data to edit */
		$getDeadtime = $Task -> getDeadtime($_GET['id']);
		$getDeadtime = $getDeadtime -> fetchAll();

		if(new DateTime() > new DateTime($getDeadtime[0][0])){
			/* Date is in the past */
			$Task->complete_task_past($_GET['id']);
		}
		else {
			/* Date is NOT in the past */
			$Task->complete_task($_GET['id']);
		}

		/* Get amount of deficiency points */
		$getDeficiencyPoints = $Task -> getDeficiencyPoints();
		$getDeficiencyPoints = $getDeficiencyPoints -> fetchAll();

		if($getDeficiencyPoints[0][0] == 10){
			$Task->lowerRole();
			header('Location: logout');
		}else if($getDeficiencyPoints[0][0] < 10){
			header('Location: home');
		}
	}
}