<?php

include 'dbo/DB.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
include 'dbo/Group.php';
header("content-type:application/json");

/*
Assessment service to access Assessment object and functions.
Report needed since assessments have an attached report, see
Assessement.php constructor.
*/

if(isset($_POST['function'])){
	$db = new DB();
	
	switch($_POST['function']){
		case 'setNewAssignments':
			$errorBool = 1;
			$params = $_POST['params'];
			foreach ($params['newAssignmentList'] as $groupAssignedTo){
				$errorBool = $errorBool && Assessment::assignAssessment($db, $params['groupNo'], $groupAssignedTo);
			}
			echo $errorBool ? json_encode('successfully assigned') : die("failed assignment");
		break;
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
			$return['assessments'] = array();			
			foreach ($assessments as $assessment){
				array_push($return['assessments'], $assessment->getData());
			}

			$stats = Group::getGroupStats($db, $params['reportNo']);
			$return['rank'] = $stats['rank'];
			$return['overallAvg'] = $stats['avg'];
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
			$errorBool = Assessment::setAssessment($db, $params['reportNo'], $params['groupNo'], 
				$params['structureGrade'], $params['strengthGrade'], $params['formatGrade'], $params['qualityGrade'], 
				$params['averageGrade'], $params['comment']);

			echo $errorBool ? json_encode('successfully editted') : die("failed edit");
		break;
		case 'assignAssessment':
			$params = $_POST['params'];
			$errorBool = Assessment::assignAssessment($db, $params['reportNo'], $params['groupNo']);
			echo $errorBool ? json_encode('successfully assigned') : die("failed assignment");
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