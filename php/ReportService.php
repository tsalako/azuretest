<?php
include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
include 'dbo/Group.php';
header("content-type:application/json");


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$validSession = isset($_SESSION['user']);

if(isset($_POST['function']) && $validSession){
	$db = new DB();

	switch($_POST['function']){


		case 'addReport':
			//meriem make sure to change this to use the $_SESSION['user']['groupNo']
			$params = $_POST['params'];
			$errorBool = Report::addReport($db, $params['title'], $params['body'], $params['reference']);
			echo $errorBool ? json_encode('Successfully added') : die("failed add");
		break;


		case 'getAllReports':
			$reports = Report::getAllReports($db);
			$return = array();			
			foreach ($reports as $report){
				array_push($return, $report->getData());
			}
			echo json_encode($return);
		break;

		
		case 'editReport':
			//meriem make sure to change this to use the $_SESSION['user']['groupNo']
			//also we don't necessarily need to do the edit (not is report description)
			$params = $_POST['params'];
			$errorBool = Report::editReport($db, $params['id'], $params['title'], $params['body'], $params['reference']);
			echo $errorBool ? json_encode('successfully editted') : die("failed edit");
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