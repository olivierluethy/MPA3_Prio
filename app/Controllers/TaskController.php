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
	
			/* Get all tasks */
			$getAllTasks = $Task -> getAllTasks()-> fetchAll();
			
			/* Get all open tasks */
			$getAllTasksOpen = $Task -> getAllTasksOpen() -> fetchAll();;
	
			// Done Tasks
			/* Get all tasks */
			$getAllTasksDone = $Task -> getAllTasksDone() -> fetchAll();;
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
		}

		$id = $_GET['id'];

		$Task = new Task();

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
			$getTask = $Task -> getTask($id) -> fetchAll();;
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

		$id = $_GET['id'];

		$Task->higherPrio($id);
	}

	public function lowerPrio(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		$id = $_GET['id'];

		$Task->lowerPrio($id);
	}

	public function complete_task(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Task = new Task();

		/* Get Data to edit */
		$getDeadtime = $Task -> getDeadtime($_GET['id']) -> fetchAll();;

		if(new DateTime() > new DateTime($getDeadtime[0][0])){
			/* Date is in the past */
			$Task->complete_task_past($_GET['id']);
		}
		else {
			/* Date is NOT in the past */
			$Task->complete_task($_GET['id']);
		}

		/* Get amount of deficiency points */
		$getDeficiencyPoints = $Task -> getDeficiencyPoints() -> fetchAll();;

		if($getDeficiencyPoints[0][0] == 10){
			$Task->lowerRole();
			header('Location: logout');
		}else if($getDeficiencyPoints[0][0] < 10){
			header('Location: home');
		}
	}
}