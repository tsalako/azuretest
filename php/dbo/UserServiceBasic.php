<?php

include 'dbo/DB.php';
include 'dbo/User.php';
include 'dbo/Group.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
header("content-type:application/json");

/* 
 User service to handle requests that deal with users, admins, students, and groups. 
*/

if(isset($_POST['function'])){

	$db = new DB();
	
	switch($_POST['function']){
		case 'getStudentDashboard':
			$return = array();
			$params = $_POST['params'];
			
			$queryUploaded = "SELECT EXISTS (
								SELECT 
									* 
								FROM 
									report 
								WHERE 
									groupNo = '{$params['groupNo']}'
								) as isUploaded
							";
			$stmt = $db->prepare($queryUploaded);
			$stmt->execute();
			$return['isUploaded'] = $stmt->fetch()['isUploaded'];

			$queryNewAssessments = "SELECT 
										COUNT(*) as newAssessmentCount
									FROM 
										assessment 
									WHERE 
										groupNo = '{$params['groupNo']}' 
									AND 
										assessedOn IS NULL
									";
			$stmt = $db->prepare($queryNewAssessments);
			$stmt->execute();
			$return['newAssessmentCount'] = $stmt->fetch()['newAssessmentCount'];
			
			echo json_encode($return);
		break;
		case 'getAdminDashboard':
			$return = array();

			$query = "SELECT 
						COUNT(*) AS doneReports 
					FROM 
						report 
					WHERE 
						uploadedOn IS NOT NULL
					";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$return['doneReports'] = $stmt->fetch()['doneReports'];
			
			$query = "SELECT 
						COUNT(*) AS doneAssessments 
					FROM 
						assessment 
					WHERE 
						assessedOn IS NOT NULL
					";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$return['doneAssessments'] = $stmt->fetch()['doneAssessments'];
			
			$query = "SELECT 
						COUNT(*) AS totalAssessments 
					FROM 
						assessment
					";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$return['totalAssessments'] = $stmt->fetch()['totalAssessments'];

			echo json_encode($return);
		break;
		case 'addUser':
			$params = $_POST['params'];
			User::addUser($db, $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
			echo json_encode('successfully added');
		break;
		case 'getStudentDetails':
			$return = array();
			

			$params = $_POST['params'];
			$student = User::getUserByUserNo($db, $params['userNo']);
			$return['student'] = $student->getData();

			$report = Report::getReportByGroupNo($db, $return['student']['groupNo']);
			$return['report'] = $report->getData();

			$authorAssessments = Assessment::getDoneAssessmentsByReportNo($db,$return['student']['groupNo']);
			$return['authorAssessments'] = array();
			foreach ($authorAssessments as $assessment){
				array_push($return['authorAssessments'], $assessment->getData());
			}
			
			$assessorAssessments = Assessment::getDoneAssessmentsByGroupNo($db,$return['student']['groupNo']);
			$return['assessorAssessments'] = array();
			foreach ($assessorAssessments as $assessment){
				array_push($return['assessorAssessments'], $assessment->getData());
			}

			$stats = Group::getGroupStats($db, $return['student']['groupNo']);
			$return['rank'] = $stats['rank'];
			$return['overallAvg'] = $stats['avg'];

			echo json_encode($return);
		break;
		case 'getAllUsers':
			$users = User::getAllUsers($db);
			$return = array();			
			foreach ($users as $user){
				array_push($return, $user->getData());
			}
			echo json_encode($return);
		break;
		case 'getAllStudents':
			$students = User::getAllStudents($db);
			$return = array();			
			foreach ($students as $student){
				array_push($return, $student->getData());
			}
			echo json_encode($return);
		break;
		case 'editUser':
			$params = $_POST['params'];
			User::editUser($db, $params['id'], $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
			echo json_encode('successfully editted');
		break;
		case 'getAllGroups':
			$groups = Group::getAllGroups($db);
			$return = array();			
			foreach ($groups as $group){
				array_push($return, $group->getData());
			}
			echo json_encode($return);
		break;
		case 'getGroupByNo':
			$params = $_POST['params'];
			$group = Group::getGroupByNo($db, $params['groupNo']);
			return json_encode($group->getData());
		break;
		case 'modifyGroups':
			$params = $_POST['params'];
			Group::modifyGroups($db, $params['groupList']);
			echo json_encode('successfully modified');
		break;
		case 'createGroups':
			$params = $_POST['params'];
			Group::createGroups($db, $params['groupList']);
			echo json_encode('successfully created');
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