<?php
include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
include 'dbo/Group.php';
header("content-type:application/json");

/** 
 * Report service to handle requests that deal with the report dbo object.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$validSession = isset($_SESSION['user']);

if(isset($_POST['function']) && $validSession){
	$db = new DB();

	switch($_POST['function']){
		case 'getReportByGroupNo':
			if(!$_SESSION['user']['isStudent']){
				die('deniedAccess');
			}

			$report = Report::getReportByGroupNo($db, $_SESSION['user']['groupNo']);
			echo $report == null ? json_encode('noReport') : json_encode($report->getData());
		break;

		case 'addReport':
			if(!$_SESSION['user']['isStudent']){
				die('deniedAccess');
			}

			$params = $_POST['params'];
			$errorBool = Report::addReport($db, $_SESSION['user']['groupNo'], $params['title'], $params['body'], $params['reference']);
			echo $errorBool ? json_encode('Successfully added') : die("failed add");
		break;


		case 'getAllReports':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$reports = Report::getAllReports($db);
			$return = array();			
			foreach ($reports as $report){
				array_push($return, $report->getData());
			}
			echo json_encode($return);
		break;

		default:
			echo die("Error - No function called '".$_POST['function']."'");
			exit();
		break;
	}
	exit();
}else{
	if(!$validSession){
		echo die("notLoggedIn");
		exit();
	} else {
		echo die("Bad parameters");
		exit();
	}
}

?>