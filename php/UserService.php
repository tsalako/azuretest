<?php

include 'dbo/DB.php';
include 'dbo/User.php';
include 'dbo/Group.php';
include 'dbo/Assessment.php';
include 'dbo/Report.php';
header("content-type:application/json");


/** 
 * User service to handle requests that deal with users (admins, students) and groups dbo objects. 
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$functionException = isset($_POST['function']) ? ($_POST['function'] == 'registerStudent' || $_POST['function'] == 'loginUser'): false;
$validSession = isset($_SESSION['user']) || $functionException;

if(isset($_POST['function']) && $validSession){

	$db = new DB();

	switch($_POST['function']){
		case 'logoutUser':
			session_destroy();
			echo json_encode('logged out');
		break;
		case 'registerStudent':
			$return = array();
			$params = $_POST['params'];
			$user = User::registerStudent($db, $params['username'], $params['password'],$params['fName'],$params['lName']);
			if($user == null){
				$return['isSuccess'] = false;
				$return['errorMessage'] = '*Username not found. Cannot register.';
			} else {
				//session_start();
				$user = $user->getData();
				$return['isSuccess'] = true;
				$return['user'] = $user;
				$_SESSION['user'] = $user;
			}
			echo json_encode($return);
		break;
		case 'loginUser':
			$return = array();
			$params = $_POST['params'];
			$user = User::loginUser($db, $params['username'], $params['password']);
			if($user == null){
				$return['isSuccess'] = false;
				$return['errorMessage'] = '*Username password combination not found.';
			} else {
				//session_start();
				$user = $user->getData();
				$return['isSuccess'] = true;
				$return['user'] = $user;
				$_SESSION['user'] = $user;
			}
			echo json_encode($return);
		break;
		case 'getAllGroupStats':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$return = Group::getAllGroupsStats($db);
			echo json_encode($return);
		break;
		case 'getStudentDashboard':
			if(!$_SESSION['user']['isStudent']){
				die('deniedAccess');
			}
			//remove params completely
			$return = array();
			
			$queryUploaded = "SELECT EXISTS (
								SELECT 
									* 
								FROM 
									report 
								WHERE 
									groupNo = '{$_SESSION['user']['groupNo']}'
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
										groupNo = '{$_SESSION['user']['groupNo']}'
									AND 
										assessedOn IS NULL
									";
			$stmt = $db->prepare($queryNewAssessments);
			$stmt->execute();
			$return['newAssessmentCount'] = $stmt->fetch()['newAssessmentCount'];
			$return['user'] = $_SESSION['user'];
			
			echo json_encode($return);
		break;
		case 'getAdminDashboard':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

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
			$return['user'] = $_SESSION['user'];

			echo json_encode($return);
		break;
		case 'addUser':
			//remove function
			$params = $_POST['params'];
			$errorBool = User::addUser($db, $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
			echo $errorBool ? json_encode('successfully added') : die("failed add");
		break;
		case 'getStudentDetails':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

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
		case 'getUserByUserNo' :
			//remove params
			$return = User::getUserByUserNo($db, $_SESSION['user']['userNo']);
			echo json_encode($return->getData());
		break;
		case 'getAllUsers':
			//remove function
			$users = User::getAllUsers($db);
			$return = array();			
			foreach ($users as $user){
				array_push($return, $user->getData());
			}
			echo json_encode($return);
		break;
		case 'getAllStudents':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$students = User::getAllStudents($db);
			$return = array();			
			foreach ($students as $student){
				array_push($return, $student->getData());
			}
			echo json_encode($return);
		break;
		case 'editUser':
			//remove function
			$params = $_POST['params'];
			$errorBool = User::editUser($db, $params['userNo'], $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
			echo $errorBool ? json_encode('successfully editted') : die("failed edit");
			
		break;
		case 'editUserField':
			//remove userNo
			$params = $_POST['params'];
			$errorBool = User::editUserField($db, $_SESSION['user']['userNo'], $params['type'], $params['data']);
			echo $errorBool ? json_encode('successfully editted') : die("failed edit");
			
		break;
		case 'updatePassword':
			//remove userNo
			$params = $_POST['params'];
			$errorBool = User::updatePassword($db, $_SESSION['user']['userNo'], $params['currPass'], $params['newPass']);
			$return['isPasswordMatch'] = $errorBool ? true : false;
			echo json_encode($return);

		break;
		case 'getAllGroups':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$groups = Group::getAllGroups($db);
			$return = array();			
			foreach ($groups as $group){
				array_push($return, $group->getData());
			}
			echo json_encode($return);
		break;
		case 'getGroupByNo':
			//remove function
			$params = $_POST['params'];
			$group = Group::getGroupByNo($db, $params['groupNo']);
			echo json_encode($group->getData());
		break;
		case 'modifyGroups':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$params = $_POST['params'];
			$errorBool = Group::modifyGroups($db, $params['groupList']);
			echo $errorBool ? json_encode('successfully modified') : die("failed modify");
		break;
		case 'createGroups':
			if(!$_SESSION['user']['isAdmin']){
				die('deniedAccess');
			}

			$params = $_POST['params'];
			$errorBool = Group::createGroups($db, $params['groupList'], $_SESSION['user']['userNo']);
			echo $errorBool ? json_encode('successfully created') : die("failed creation");
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