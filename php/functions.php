<?php

include 'includes/connection.php';
header("content-type:application/json");

if(isset($_POST['function'])){
	
	switch($_POST['function']){
		case 'getPeople':
			$query = "SELECT * FROM people";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$return = array();			
			while($row = $stmt->fetch()){
				array_push($return, $row/*array(
						'ID' => $row['ID'],
						'name' => $row['name'],
						'desc' => $row['desc']
					)*/);
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