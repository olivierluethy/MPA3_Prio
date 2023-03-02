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
			
			/* Get all open tasks */
			$getAllTasksOpen = $Task -> getAllTasksOpen();
	
			// Done Tasks
			/* Get all tasks */
			$getAllTasksDone = $Task -> getAllTasksDone();
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
            $titel = e(post('title'));
			$beschreibung = e(post('description'));
			$motivation = e(post('motivation'));
			$deadline = e(post('deadline'));
			$prioritaet = e(post('priority'));

            $Task->add_task($titel, $beschreibung, $motivation, $deadline, $prioritaet);
 
            header('Location: home');
        }
		require 'app/Views/addTask.view.php';
	}

	public function edit_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
			exit;
		}

		$id = $_GET['id'];

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = e(post('title'));
			$beschreibung = e(post('description'));
			$motivation = e(post('motivation'));
			$deadline = e(post('deadline'));
			$prioritaet = e(post('priority'));
        
			$Task->edit_task($titel, $beschreibung, $motivation, $deadline, $prioritaet, $id);

            header('Location: home');	
        }else{
			/* Get Data to edit */
			$getTask = $Task -> getTask($id);
        }
		require 'app/Views/editTask.view.php';
	}

	public function delete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
			exit;
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
			exit;
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
			exit;
		}

		$Task = new Task();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$id = $_GET['id'];

		$Task->lowerPrio($id);
        
        header('Location: home');
	}

	public function complete_task()
	{
	// Start the session
	session_start();
	// Check if user is logged in
	if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header('Location: login');
		exit;
	}

	// Connect to database
	$pdo = connectDatabase();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Get task details
	$task = new Task();
	$getDeadtime = $task->getDeadtime($_GET['id'])->fetchAll();
	$isPastDeadline = new DateTime() > new DateTime($getDeadtime[0][0]);

	// Mark task as completed
	if ($isPastDeadline) {
		$task->complete_task_past($_GET['id']);
	} else {
		$task->complete_task($_GET['id']);
	}

	// Check deficiency points
	$getDeficiencyPoints = $task->getDeficiencyPoints()->fetchAll();
	$deficiencyPoints = $getDeficiencyPoints[0][0];

	if ($deficiencyPoints == 10) {
		$task->lowerRole();
		header('Location: logout');
		exit;
	} elseif ($deficiencyPoints < 10) {
		header('Location: home');
		exit;
	}
	}
}