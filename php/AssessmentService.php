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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$validSession = isset($_SESSION['user']);

if(isset($_POST['function']) && $validSession){
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
			//remove params
			$assessments = Assessment::getUndoneAssessmentsByGroupNo($db, $_SESSION['user']['groupNo']);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'getDoneAssessmentsByGroupNo':
			//remove params
			$assessments = Assessment::getDoneAssessmentsByGroupNo($db, $_SESSION['user']['groupNo']);
			$return = array();			
			foreach ($assessments as $assessment){
				array_push($return, $assessment->getData());
			}
			echo json_encode($return);
		break;
		case 'getDoneAssessmentsByReportNo':
			//remove params
			$assessments = Assessment::getDoneAssessmentsByReportNo($db, $_SESSION['user']['groupNo']);
			$return['assessments'] = array();			
			foreach ($assessments as $assessment){
				array_push($return['assessments'], $assessment->getData());
			}

			$stats = Group::getGroupStats($db, $_SESSION['user']['groupNo']);
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
			//remove reportNo
			$params = $_POST['params'];
			$errorBool = Assessment::setAssessment($db, $params['reportNo'], $_SESSION['user']['groupNo'], 
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
	if(!$validSession){
		echo die("notLoggedIn");
		exit();
	} else {
		echo die("Bad parameters");
		exit();
	}
}


?>