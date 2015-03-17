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
		$errorBool = 1;
		foreach ($groupList as $group){
			$errorBool = $errorBool && User::setUsersGroupNos($db, $group['usernames'][0],$group['usernames'][1],$group['usernames'][2], $group['groupNo']);
		}
		return $errorBool;
	}

	public static function createGroups($db, $groupList){
		$errorBool = 1;

		//must truncate all tables with th exception of user where the admins will be preserved
		$queryTruncGroup = "TRUNCATE groups";
		$db->exec($queryTruncGroup);

		$errorBool = $errorBool && User::deleteCurrentStudents($db);
		foreach ($groupList as $group){
			$queryInsert = "INSERT INTO 
							groups (`groupNo`, `assignedBy`) 
					  	VALUES 
					  		('".$group['groupNo']."', '".$group['assignedBy']."')";
	    	$errorBool = $errorBool && $db->exec($queryInsert);

			foreach ($group['usernames'] as $username){
				$errorBool = $errorBool && User::addUserBasic($db, $username, $group['groupNo'], 'student');
			}
		}

		return $errorBool;
	}

	public static function getGroupStats($db, $groupNo) {
		$row = array();
		$queryReport = "SELECT 
						z.rank, 
						z.reportNo, 
						z.avg 
					FROM (
						SELECT 
							@rowno:=@rowno+1 as rank, 
							x.reportNo, 
							x.avg 
						FROM 
							(SELECT 
								reportNo, 
								AVG((structureGrade+formatGrade+strengthGrade+qualityGrade)/4) as avg 
							FROM 
								assessment 
							GROUP BY 
								reportNo 
							ORDER BY 
								avg 
							DESC) x,
							(SELECT @rowno:=0) r
						) z 
					WHERE 
						z.reportNo = '{$groupNo}'
					";
			$stmt = $db->prepare($queryReport);
			$stmt->execute();
			$result = $stmt->fetch();
			if(!$result){
				$row['avg'] = null;
				$row['rank'] = null;
			} else {
				$row['avg'] = $result['avg'];
				$row['rank'] = $result['rank'];
			}

			$querySubmittedDate = "SELECT 
									uploadedOn
								   FROM
								   	 report
								   WHERE
								   	 groupNo = '{$groupNo}'
									";
			$stmt = $db->prepare($querySubmittedDate);
			$stmt->execute();
			$result = $stmt->fetch();
			$row['uploadedOn'] = !$result ? null : $result['uploadedOn'];

			//original query
			/*
				$queryAssessment = "SELECT (
									SELECT COUNT( * )
									FROM assessment
									WHERE groupNo = '{$groupNo}'
									) AS totalCount, (

									SELECT COUNT( * )
									FROM assessment
									WHERE assessedOn IS NOT NULL
									AND groupNo = '{$groupNo}'
									) AS writtenCount
									FROM assessment";
			*/
			$queryAssessment = "SELECT 
									COUNT(*) as total, 
									COUNT(assessedOn) as written 
								FROM 
									assessment 
								WHERE groupNo = '{$groupNo}'
								";
			$stmt = $db->prepare($queryAssessment);
			$stmt->execute();
			$result = $stmt->fetch();
			$row['written'] = $result['written'];
			$row['total'] = $result['total'];

			

			//returns object with avg, rank, and reportNo fields (see use example in UserService.php)
			return $row;
	}

	public static function getAllGroupsStats($db) {
		$groupStats = array();
		$query = "SELECT 
					groupNo,
					name 
				FROM groups";
		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row = $stmt->fetch()){
			$groupStats[$row['groupNo']] = Group::getGroupStats($db, $row['groupNo']);
			$groupStats[$row['groupNo']]['name'] = $row['name'];
			$groupStats[$row['groupNo']]['groupNo'] = $row['groupNo'];
		}
		return $groupStats;
	}

}

?>