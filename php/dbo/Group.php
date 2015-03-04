<?php

class Group{
	private $data = array();

	function __construct($group){
		$this->data['groupNo'] = $group['groupNo'];
		$this->data['name'] = $group['name'];
		$this->data['assignedBy'] = $group['assignedBy'];
		$this->data['users'] = $group['users'];
	}

	public function getData(){
		$returnData = $this->data;
		$returnData['users'] = array();
        foreach ($this->data['users'] as $key => $item)
        {
            array_push($returnData['users'], $item -> getData());
        }
        return $returnData;
	}

	public static function getAllGroups($db){
		$groups = array();

		$queryGroups = "SELECT
							G.groupNo,
							G.name,
							G.assignedBy
						FROM
							groups G";
		$stmt = $db->prepare($queryGroups);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$row['users'] = User::getUsersByGroupNo($db, $row['groupNo']);
			array_push($groups, new Group($row));
		}

		return $groups;
	} 

	public static function getGroupByNo($db, $groupNo){
		$queryGroup = "SELECT
							G.groupNo,
							G.name,
							G.assignedBy
						FROM
							groups G
					WHERE
						G.groupNo = '".$groupNo."'
					";

		$stmt = $db->prepare($queryGroup);
		$stmt->execute();
		$group = $stmt->fetch();
		$row['users'] = User::getUsersByGroupNo($db, $row['groupNo']);
		return $group;
	}

	public static function modifyGroups($db, $groupList){
		foreach ($groupList as $group){
			User::setUsersGroupNos($db, $group['usernames'][0],$group['usernames'][1],$group['usernames'][2], $group['groupNo']);
		}
	}

	public static function createGroups($db, $groupList){
		//must truncate all tables with th exception of user where the admins will be preserved
		$queryTruncGroup = "TRUNCATE groups";
		$db->exec($queryTruncGroup);

		User::deleteCurrentStudents($db);
		foreach ($groupList as $group){
			$queryInsert = "INSERT INTO 
							groups (`groupNo`, `assignedBy`) 
					  	VALUES 
					  		('".$group['groupNo']."', '".$group['assignedBy']."')";
	    	$db->exec($queryInsert);

			foreach ($group['usernames'] as $username){
				User::addUserBasic($db, $username, $group['groupNo'], 'student');
			}
		}
	}
	//getAllGroups
	//getGroupByNo
	//createGroups
	//modifyGroups


}

?>