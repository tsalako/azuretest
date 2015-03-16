<?php
include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
include 'dbo/Group.php';
header("content-type:application/json");


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['function'])){
	$db = new DB();

	switch($_POST['function']){


		case 'addReport':
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
	echo die("Bad parameters");
	exit();
}

?>