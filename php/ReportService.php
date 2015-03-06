<?php
include 'dbo/DB.php';
include 'dbo/Report.php';
header("content-type:application/json");

if(isset($_POST['function'])){
	$db = new DB();
	
	switch($_POST['function']){
		case 'getAllReports':
			$reports = Report::getAllReports($db);
			$return = array();			
			foreach ($reports as $report){
				array_push($return, $report->getData());
			}
			echo json_encode($return);
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