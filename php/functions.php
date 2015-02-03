<?php

include 'dbo/DB.php';
include 'dbo/Person.php';
header("content-type:application/json");

if(isset($_POST['function'])){

	$db = new DB();
	
	switch($_POST['function']){
		case 'getPeople':
			$people = Person::getAllPeople($db);
			$return = array();			
			foreach ($people as $person){
				array_push($return, $person->getData());
			}
			echo json_encode($return);
		break;
		case 'getPersonByID':
			$params = $_POST['params'];
			$person = Person::getPersonFromID($db, $params['ID']);
			echo json_encode($person->getData());
		break;
		case 'setPeople':
			echo json_encode('we were set! I promise.');
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