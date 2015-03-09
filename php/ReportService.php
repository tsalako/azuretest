<?php
include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
include 'dbo/Group.php';
header("content-type:application/json");

if(isset($_POST['function'])){
	$db = new DB();
	
	switch($_POST['function']){


		case 'addReport':
			$params = $_POST['params'];
			Report::addReport($db, $params['title'], $params['body'], $params['reference']);
			echo json_encode('Successfully added');
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
			Report::editReport($db, $params['id'], $params['title'], $params['body'], $params['reference']);
			echo json_encode('successfully editted');
		break;


		default:
			echo "Error - No function called '".$_POST['function']."'";
			exit();
		break;
	}
	exit();
}else{
	echo "Bad parameters";
	exit();
}

?>