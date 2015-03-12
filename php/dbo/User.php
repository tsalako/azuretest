<?php

class User{
	private $data = array();

	function __construct($user){
		$this->data['userNo'] = $user['userNo'];
		$this->data['username'] = $user['userName'];
		$this->data['password'] = isset($user['password']) ? $user['password'] : null;
		$this->data['fName'] = $user['fName'];
		$this->data['lName'] = $user['lName'];
		$this->data['groupNo'] = isset($user['groupNo']) ? $user['groupNo'] : null;
		$this->data['groupName'] = isset($user['groupName']) ? $user['groupName'] : null;
		$this->data['isAdmin'] = $user['type'] == 'admin';
		$this->data['isStudent'] = $user['type'] == 'student';		
	}

	public function getData(){
		return $this->data;
	}

	public static function addUserBasic($db, $username, $groupNo, $type){
		$queryInsert = "INSERT INTO 
							user (`userNo`, `type`, `userName`, `groupNo`) 
					  	VALUES 
					  		(NULL, '".$type."', '".$username."', '".$groupNo."')
					  	";
		return $db->exec($queryInsert);
	}

	public static function addUser($db, $username, $password, $type, $first, $last){
		$password = sha1($password);
		$queryInsert = "INSERT INTO 
							user (`userNo`, `password`, `type`, `userName`,`fName`,`lName`) 
					  	VALUES 
					  		(NULL, '".$password."', '".$type."', '".$username."','".$first."','".$last."')
					  	";
		return $db->exec($queryInsert);
	}

	public static function editUser($db, $userNo, $username, $password, $type, $first, $last){
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
							userNo = '".$userNo."'
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
							userNo = '".$userNo."'
						";
		}
		return $db->exec($queryUpdate);
	}

	public static function editUserField($db, $userNo, $field, $data){
		if($field == 'password') {
			$currPass = sha1($data['currPass']);
			$newPass = sha1($data['newPass']);
			$queryUpdate = "UPDATE 
							user
						SET
							password = '".$newPass."'
						WHERE
							userNo = '".$userNo."'
						AND
							password = '".$currPass."'
						";
		} else if($field == 'name') {
			$queryUpdate = "UPDATE 
							user
						SET
							fName = '".$data['fName']."',
							lName = '".$data['lName']."'
						WHERE
							userNo = '".$userNo."'
						";
		} else if($field == 'groupName') {
			$queryUpdate = "UPDATE 
							user U, groups G
						SET
							G.name = '".$data."'
						WHERE
							U.userNo = '".$userNo."'
						AND
							U.groupNo = G.groupNo
						";
		} else {
			$queryUpdate = "UPDATE 
							user
						SET
							".$field." = '".$data."'
						WHERE
							userNo = '".$userNo."'";
		}


		return $db->exec($queryUpdate);
	}

	public static function setUserGroupNo($db, $username, $groupNo){
		$queryUpdate = "UPDATE 
							user
						SET
							groupNo = '".$groupNo."'
						WHERE
							username = '".$username."'
						";
		return $db->exec($queryUpdate);
	}

	public static function setUsersGroupNos($db, $username1, $username2, $username3, $groupNo){
		$queryUpdate = "UPDATE 
							user
						SET
							groupNo = '".$groupNo."'
						WHERE
							username IN ('".$username1."','".$username2."','".$username3."')
						";
		return $db->exec($queryUpdate);
	}

	public static function getUserByUserNo($db, $userNo){
		$queryUser = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo,
						G.name as groupName
					FROM 
						user U
					LEFT JOIN 
						groups G 
					ON 
						U.groupNo = G.groupNo
					WHERE
						U.userNo = '".$userNo."'
					";

		$stmt = $db->prepare($queryUser);
		$stmt->execute();
		return new User($stmt->fetch());
	}

	public static function getUsersByGroupNo($db, $groupNo){
		$users = array();

		$queryUsers = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo 
					FROM 
						user U
					WHERE
						U.groupNo = '".$groupNo."'
					";

		$stmt = $db->prepare($queryUsers);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			array_push($users, new User($row));
		}

		return $users;	
	}

	public static function getAllStudents($db){
		$students = array();

		$queryStudents = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo 
					FROM 
						user U
					WHERE
						U.type = 'student'
					";

		$stmt = $db->prepare($queryStudents);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			array_push($students, new User($row));
		}

		return $students;
	}

	public static function getAllUsers($db){
		$users = array();

		$queryUsers = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo 
					FROM 
						user U
					";

		$stmt = $db->prepare($queryUsers);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			array_push($users, new User($row));
		}

		return $users;
	}

	public static function deleteCurrentStudents($db) {
		$query = "DELETE FROM 
					user 
				  WHERE type = 'student'
						";
		return $db->exec($query);
	}

	public static function getSessionUser($db) {
		
	}

}

?>