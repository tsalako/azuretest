<?php

include 'dbo/DB.php';
include 'dbo/User.php';
include 'dbo/Student.php';
include 'dbo/Admin.php';
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
		case 'getAllUsers':
			$users = User::getAllUsers($db);
			$return = array();			
			foreach ($users as $user){
				array_push($return, $user->getData());
			}
			echo json_encode($return);
		break;
		case 'editUser':
			$params = $_POST['params'];
			User::editUser($db, $params['id'], $params['username'], $params['password'], $params['type'], $params['first'], $params['last']);
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