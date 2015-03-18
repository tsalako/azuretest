<?php

/**
 * User Database Object Class
 */
class User{
	private $data = array();

	function __construct($user){
		$this->data['userNo'] = $user['userNo'];
		$this->data['username'] = $user['userName'];
		//password and salt are omitted because they will never be returned
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

	/**
	 * Checks if the password and username match one in the database. Ensuring security.
	 * 
	 * @param $db       database connection
	 * @param $username username of the user wanting to login
	 * @param $password password of the user wanting to login
	 * @return          user dbo object if login was successful or null if it was not.
	 */
	public static function loginUser($db, $username, $password) {
		$login_ok = false; 

		$queryUser = "SELECT 
						U.userNo, 
						U.userName, 
						U.type, 
						U.fName, 
						U.lName,
						U.groupNo,
						U.password,
						U.salt,
						G.name as groupName
					FROM 
						user U
					LEFT JOIN 
						groups G 
					ON 
						U.groupNo = G.groupNo
					WHERE
						U.username = '".$username."'
				";

		$stmt = $db->prepare($queryUser); 
        $stmt->execute(); 		
        $row = $stmt->fetch(); 
        if($row){ 
            $check_password = hash('sha256', $password . $row['salt']); 
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            } 
            if($check_password === $row['password']){
                $login_ok = true;
            } 
        } 

        if($login_ok){ 
            unset($row['salt']); 
            unset($row['password']); 
            return new User($row);
        } 
        else{ 
            return null;
        } 
	}

	/**
	 * Reigsters a user in the database. Ensuring security. The username is already in the
	 * database, it was added by the admin and the user uses that name to register.
	 * 
	 * @param $db       database connection
	 * @param $username username of the user wanting to register
	 * @param $password password of the user wanting to register
	 * @param $fName    first name of the user wanting to register
	 * @param $lName    last name of the user wanting to register
	 * @return          user dbo object if registration was successful or null if it was not.
	 */
	public static function registerStudent($db, $username, $password, $fName, $lName){
		$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
        $password = hash('sha256', $password . $salt); 
        for($round = 0; $round < 65536; $round++){ 
        		$password = hash('sha256', $password . $salt); 
        } 
        $queryUpdate = "UPDATE
		        			user
		        		  SET
		        		  	password ='".$password."',
		        		  	salt = '".$salt."',
		        		  	fName = '".$fName."',
		        		  	lName = '".$lName."'
		        		  WHERE
		        		  	username = '".$username."'
		        		  AND
		        		  	password IS NULL
		        		  ";
       $isEditted = $db->exec($queryUpdate);

       if($isEditted == 0) {
       		return null;
       } else {
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
						U.username = '".$username."'
					AND
						U.password = '".$password."'
       					";

       		$stmt = $db->prepare($queryUser);
			$stmt->execute();
			return new User($stmt->fetch());
       }

	}

	/**
	 * Add user to the database by their username, groupNo, and type
	 * so they can register when they come to the site for the first time.
	 * 
	 * @param $db       database connection
	 * @param $username username of the user
	 * @param $groupNo  groupNo of the group the user is in
	 * @param $type     type of user
	 * @return          whether user was added or not
	 */
	public static function addUserBasic($db, $username, $groupNo, $type){
		$queryInsert = "INSERT INTO 
							user (`userNo`, `type`, `userName`, `groupNo`) 
					  	VALUES 
					  		(NULL, '".$type."', '".$username."', '".$groupNo."')
					  	";
		return $db->exec($queryInsert);
	}

	/**
	 * REMOVE FUNCTION
	 */
	public static function addUser($db, $username, $password, $type, $first, $last){
		$password = sha1($password);
		$queryInsert = "INSERT INTO 
							user (`userNo`, `password`, `type`, `userName`,`fName`,`lName`) 
					  	VALUES 
					  		(NULL, '".$password."', '".$type."', '".$username."','".$first."','".$last."')
					  	";
		return $db->exec($queryInsert);
	}

	/**
	 * REMOVE FUNCTION
	 */
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

	/**
	 * Update the password of the specified user.
	 * 
	 * @param $db       database connection
	 * @param $userNo   userNo of the user
	 * @param $currPass current password to change
	 * @param $newPass  new password to set
	 * @return          whether the password was updated or not
	 */
	public static function updatePassword($db, $userNo, $currPass, $newPass) {
		$pass_check = false; 

		$queryUser = "SELECT 
						U.userNo, 
						U.userName, 
						U.password,
						U.salt
					FROM 
						user U
					WHERE
						U.userNo = '".$userNo."'
				";

		$stmt = $db->prepare($queryUser); 
        $stmt->execute(); 		
        $row = $stmt->fetch(); 
        if($row){ 
            $check_password = hash('sha256', $currPass . $row['salt']); 
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            } 
            if($check_password === $row['password']){
                $pass_check = true;
            } 
        } 

        if($pass_check){ 
            unset($row['salt']); 
            unset($row['password']); 
            
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
	        $newPass = hash('sha256', $newPass . $salt); 
	        for($round = 0; $round < 65536; $round++){ 
	        		$newPass = hash('sha256', $newPass . $salt); 
	        } 
	        $queryUpdate = "UPDATE
			        			user
			        		  SET
			        		  	password ='".$newPass."',
			        		  	salt = '".$salt."'
			        		  WHERE
			        		  	userNo = '".$userNo."'
			        		  ";
	       $isEditted = $db->exec($queryUpdate);
	       return 1;
        } 
        else{ 
           return 0;
        } 
	}

	/**
	 * Edit a specified user field for a specified user.
	 * 
	 * @param $db     database connection
	 * @param $userNo userNo of the user
	 * @param $field  field to update
	 * @param $data   data to set the field to
	 * @return        1 - the field was updated
	 */
	public static function editUserField($db, $userNo, $field, $data){
		if($field == 'name') {
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
		$db->exec($queryUpdate);
		//return $db->exec($queryUpdate);
		return 1;
	}

	/**
	 * Set the groupNo of an entire group.
	 * 
	 * @param $db        database connection
	 * @param $username1 username of the first group member
	 * @param $username2 username of the second group member
	 * @param $username3 username of the third group member
	 * @param $groupNo   groupNo to set the users to
	 * @return           1 - the field was updated
	 */
	public static function setUsersGroupNos($db, $username1, $username2, $username3, $groupNo){
		$queryUpdate = "UPDATE 
							user
						SET
							groupNo = '".$groupNo."'
						WHERE
							username IN ('".$username1."','".$username2."','".$username3."')
						";

		$db->exec($queryUpdate);
		return 1;
	}

	/**
	 * Get a user from the database by their user number
	 * 
	 * @param $db     database connection
	 * @param $userNo userNo of the user to retrieve
	 * @return        the user db object
	 */
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

	/**
	 * Get all the users from the database in a group.
	 * 
	 * @param $db      database connection
	 * @param $groupNo groupNo of the users to retrieve
	 * @return         array of user objects
	 */
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

	/**
	 * Get all the student users from the database in a group.
	 * 
	 * @param $db database connection
	 * @return    array of user objects
	 */
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

	/**
	 * REMOVE FUNCTION
	 */
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

	/**
	 * Deletes all the user with type student in the database
	 * 
	 * @param $db database connection
	 * @return    whether the students were deleted or not
	 */
	public static function deleteCurrentStudents($db) {
		$query = "DELETE FROM 
					user 
				  WHERE type = 'student'
						";
		return $db->exec($query);
	}

}

?>