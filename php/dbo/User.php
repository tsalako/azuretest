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
		$this->data['isAdmin'] = $user['type'] == 'admin';
		$this->data['isStudent'] = $user['type'] == 'student';

		$this->data['report'] = isset($user['report']) ? $user['report'] : null;
		$this->data['assessments'] = isset($user['assessments']) ? $user['assessments'] : null;
		
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
		$db->exec($queryInsert);
	}

	public static function addUser($db, $username, $password, $type, $first, $last){
		$password = sha1($password);
		$queryInsert = "INSERT INTO 
							user (`userNo`, `password`, `type`, `userName`,`fName`,`lName`) 
					  	VALUES 
					  		(NULL, '".$password."', '".$type."', '".$username."','".$first."','".$last."')
					  	";
		$db->exec($queryInsert);
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
		$db->exec($queryUpdate);
	}

	public static function setUserGroupNo($db, $username, $groupNo){
		$queryUpdate = "UPDATE 
							user
						SET
							groupNo = '".$groupNo."'
						WHERE
							username = '".$username."'
						";
		$db->exec($queryUpdate);
	}

	public static function setUsersGroupNos($db, $username1, $username2, $username3, $groupNo){
		$queryUpdate = "UPDATE 
							user
						SET
							groupNo = '".$groupNo."'
						WHERE
							username IN ('".$username1."','".$username2."','".$username3."')
						";
		$db->exec($queryUpdate);
	}

	public static function getUserByUsername($db, $username){
		$queryUser = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo
					FROM 
						user U
					WHERE
						U.userName = '".$username."'
					";

		$stmt = $db->prepare($queryUser);
		$stmt->execute();
		return $stmt->fetch();
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
		$db->exec($query);
	}

	public static function getStudentDetails($db, $user) {

	}

	public static function getSessionUser($db) {
		
	}

}

?>