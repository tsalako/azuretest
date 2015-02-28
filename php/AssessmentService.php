<?php

include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
header("content-type:application/json");

/*
Assessment service to access Assessment object and functions.
Report needed since assessments have an attached report, see
Assessement.php constructor.
*/

if(isset($_POST['function'])){
	$db = new DB();
	
	switch($_POST['function']){
		case 'getUndoneAssessmentsByGroupNo':
			$params = $_POST['params'];
			$assessments = Assessment::getUndoneAssessmentsByGroupNo($db, $params['groupNo']);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'getDoneAssessmentsByGroupNo':
			$params = $_POST['params'];
			$assessments = Assessment::getDoneAssessmentsByGroupNo($db, $params['groupNo']);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'getDoneAssessmentsByReportNo':
			$params = $_POST['params'];
			$assessments = Assessment::getDoneAssessmentsByReportNo($db, $params['reportNo']);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'getAllAssessments':
			$assessments = Assessment::getAllAssessments($db);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'setAssessment':
			$params = $_POST['params'];
			Assessment::setAssessment($db, $params['reportNo'], $params['groupNo'], 
				$params['structureGrade'], $params['strengthGrade'], $params['formatGrade'], $params['qualityGrade'], 
				$params['averageGrade'], $params['comment']);

			echo json_encode('successfully editted');
			//$reportNo, $groupNo, $structureGrade, $strengthGrade, $formatGrade, $qualityGrade,$averageGrade, $comment
		break;
		case 'assignAssessment':
			$params = $_POST['params'];
			Assessment::assignAssessment($db, $params['reportNo'], $params['groupNo']);
			echo json_encode('successfully assigned');
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