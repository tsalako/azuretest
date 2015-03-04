<?php

include 'dbo/DB.php';
include 'dbo/User.php';
include 'dbo/Group.php';
header("content-type:application/json");

/* 
 User service to handle requests that deal with users, admins, students, and groups. 
*/

if(isset($_POST['function'])){

	$db = new DB();
	
	switch($_POST['function']){
		case 'addUser':
			$params = $_POST['params'];
			User::addUser($db, $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
			echo json_encode('successfully added');
		break;
		case 'getStudentDetails':
			$params = $_POST['params'];
			$student = User::getStudentDetails($db, $params['userNo']);
			echo json_encode($student->getData());
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