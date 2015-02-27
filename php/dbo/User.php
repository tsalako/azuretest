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
							user (`userNo`, `password`, `type`, `userName`,fName,lName) 
					  	VALUES 
					  		(NULL, '".$password."', '".$type."', '".$username."','".$first."','".$last."')
					  	";
		$db->exec($queryInsert);
	}

	public static function editUser($db, $id, $username, $password, $type, $first, $last){
		if($password != ''){
			$password = sha1($password);
			$queryUpdate = "UPDATE 
							user
						SET
							password = '".$password."',
							type = '".$type."',
							username = '".$username."',
							fName = '".$first."',
							lName = '".$last."'
						WHERE
							userNo = '".$id."'
						";
		} else {
			$queryUpdate = "UPDATE 
							user
						SET
							type = '".$type."',
							username = '".$username."',
							fName = '".$first."',
							lName = '".$last."'
						WHERE
							userNo = '".$id."'
						";
		}
		$db->exec($queryUpdate);
	}

	public static function getAllUsers($db){
		$users = array();

		$queryUsers = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName 
					FROM 
						user U
					";

		$stmt = $db->prepare($queryUsers);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$row['password'] = '';
			array_push($users, new User($row));
		}

		return $users;
	}

}

?>