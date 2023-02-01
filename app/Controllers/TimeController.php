<?php

class TimeController
{
	public function addTimeRecord(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Time = new Time();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$time = $_GET['timeRecord'];
		$id = $_GET['id'];

		$Time->addTimeRecord($time, $id);

		header('Location: home');
	}

	public function zeituebersicht(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: about');
		}

		$Time = new Time();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		/* Get all times */
		$getAllTimes = $Time -> getAllTimes();
		$getAllTimes = $getAllTimes -> fetchAll();

		/* Get times which were created within 24 hours */
		$getTimesUnderADay = $Time -> getTimesUnderADay();
		$getTimesUnderADay = $getTimesUnderADay -> fetchAll();

		/* Get times which were created after 24 hours */
		$getTimesOverADay = $Time -> getTimesOverADay();
		$getTimesOverADay = $getTimesOverADay -> fetchAll();

		$getAllTimesCounter = 0;
		$getTimesUnderADayCounter = 0;
		$getTimesOverADayCounter = 0;

		foreach ($getAllTimes as $getAllTimes2){
			$getAllTimesCounter++;
		}
		foreach ($getTimesUnderADay as $getTimesUnderADay2){
			$getTimesUnderADayCounter++;
		}
		foreach ($getTimesOverADay as $getTimesOverADay2){
			$getTimesOverADayCounter++;
		}

		require 'app/Views/zeituebersicht.view.php';
	}

	public function delete_time(){
		// Initialize the session
        session_start();

		if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			header('Location: login');
		}

		$Time = new Time();
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$id = $_GET['id'];

		$Time->delete_time($id);
        
        header('Location: zeituebersicht');
	}

	public function edit_time(){
		$Time = new Time();

        // Initialize the session
        session_start();

        $id = $_GET['id'];

        $title = '';
        $pdo = connectDatabase();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $time = $_POST['time'];

            $Time->edit_time($id, $time);

            header('Location: http://localhost/MPA2_Prio/zeituebersicht');
        }
		/* Needed data to show data that can be changed */
		else{
            $getTime = $Time->getTime($id);
        	$getTime = $getTime -> fetchAll();
        }
        require 'app/Views/editTime.view.php';
	}
}