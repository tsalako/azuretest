<?php

class User{
	private $data = array();

	function __construct($user){
		$this->data['userNo'] = $user['userNo'];
		$this->data['username'] = $user['userName'];
		$this->data['password'] = $user['password']; /*encode or something*/
		$this->data['fName'] = $user['fName'];
		$this->data['lName'] = $user['lName'];
		$this->data['isAdmin'] = $user['type'] == 'admin';
		$this->data['isStudent'] = $user['type'] == 'student';
	}

	public function getData(){
		return $this->data;
	}

	public static function addUser($db, $username, $password, $type, $first, $last){
		$password = sha1($password);
		$queryInsert = "INSERT INTO 
							user (`userNo`, `password`, `type`, `userName`) 
					  	VALUES 
					  		(NULL, '".$password."', '".$type."', '".$username."')
					  	";
		$db->exec($queryInsert);

		$queryNumber = "SELECT 
							U.userNo 
						  FROM 
						  	user U
						  WHERE
						  	U.password = '".$password."'
						  AND
						  	U.type = '".$type."'
						  AND
						  	U.userName = '".$username."'
						  ";
		$stmt = $db->prepare($queryNumber);
		$stmt->execute();
		$number = $stmt->fetch()['userNo'];

		if($type == 'admin'){
			$query = "INSERT INTO
							admin (`adminNo`, `fName`, `lName`)
						VALUES
							('".$number."','".$first."','".$last."')
					";
		} else {
			$query = "INSERT INTO
							student (`studentNo`, `fName`, `lName`, `groupNo`)
						VALUES
							('".$number."','".$first."','".$last."', NULL)
					";
		}
		$db->exec($query);
	}

	public static function editUser($db, $id, $username, $password, $type, $first, $last){
		if($password != ''){
			$password = sha1($password);
			$queryUpdate = "UPDATE 
							user
						SET
							password = '".$password."',
							type = '".$type."',
							username = '".$username."'
						WHERE
							userNo = '".$id."'
						";
		} else {
			$queryUpdate = "UPDATE 
							user
						SET
							type = '".$type."',
							username = '".$username."'
						WHERE
							userNo = '".$id."'
						";
		}
		$db->exec($queryUpdate);

		if($type == 'admin'){
			$query = "UPDATE
							admin
						SET
							fName = '".$first."',
							lName = '".$last."'
						WHERE
							adminNo = '".$id."'
						";
		} else {
			$query = "UPDATE
							student
						SET
							fName = '".$first."',
							lName = '".$last."'
						WHERE
							studentNo = '".$id."'
						";
		}
		$db->exec($query);	
	}

	public static function getAllUsers($db){
		$users = array();

		$queryAdmin = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						A.fName, 
						A.lName 
					FROM 
						user U, 
						admin A 
					WHERE 
						U.userNo = A.adminNo
					";

		$stmt = $db->prepare($queryAdmin);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$row['password'] = '';
			array_push($users, new User($row));
		}

		$queryStudent = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						S.fName, 
						S.lName 
					FROM 
						user U, 
						student S
					WHERE 
						U.userNo = S.studentNo
					";


		$stmt = $db->prepare($queryStudent);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$row['password'] = '';
			array_push($users, new User($row));
		}

		return $users;
	}

}

?>