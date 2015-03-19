<?php

/**
 * Group Database Object Class
 */
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

	/**
	 * Gets all 20 groups in the database
	 * 
	 * @param $db database connection
	 * @return    list of group dbo objects
	 */
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

	/**
	 * Gets the group object from the database given a groupNo
	 * 
	 * @param $db      database connection
	 * @param $groupNo groupNo of group to retrieve
	 * @return         the group dbo object
	 */
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

	/**
	 * Modify all group numbers in database.
	 * 
	 * @param $db database connection
	 * @return    1 - the function ran with no errors.
	 */
	public static function modifyGroups($db, $groupList){
		foreach ($groupList as $group){
			User::setUsersGroupNos($db, $group['usernames'][0],$group['usernames'][1],$group['usernames'][2], $group['groupNo']);
		}
		return 1;
	}

	/**
	 * Clear the database of all things regarding old groups 
	 * (group table, assessment table, report table, thread table,
	 * post table, student users) and assign tne new groups.
	 * 
	 * @param $db database connection
	 * @return    boolean of whether if the deletes and updates took place or not
	 */
	public static function createGroups($db, $groupList, $adminNo){
		$errorBool = 1;

		//must truncate all tables with th exception of user where the admins will be preserved
		$queryTruncGroup = "TRUNCATE groups";
		$db->exec($queryTruncGroup);

		$queryTruncAssess = "TRUNCATE assessment";
		$db->exec($queryTruncAssess);

		$queryTruncPost = "TRUNCATE post";
		$db->exec($queryTruncPost);

		$queryTruncThread = "TRUNCATE thread";
		$db->exec($queryTruncThread);

		$queryTruncReport = "TRUNCATE report";
		$db->exec($queryTruncReport);

		User::deleteCurrentStudents($db);
		foreach ($groupList as $group){
			$queryInsert = "INSERT INTO 
							groups (`groupNo`, `assignedBy`) 
					  	VALUES 
					  		('".$group['groupNo']."', '".$adminNo."')";
	    	$db->exec($queryInsert);

			foreach ($group['usernames'] as $username){
				User::addUserBasic($db, $username, $group['groupNo'], 'student');
			}
		}

		return $errorBool;
	}

	/**
	 * Get the statistics for a specified group.
	 * 
	 * @param $db      database connection
	 * @param $groupNo groupNo of the group to grab the stats for.
	 * @return         the groupStats custom object
	 */
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

			return $row;
	}

	/**
	 * Gets stats of all 20 groups in the database
	 * 
	 * @param $db database connection
	 * @return    list of groupStats custom objects
	 */
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