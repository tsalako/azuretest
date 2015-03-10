<?php

class Report{
	private $data = array();

	function __construct($report){
		$this->data['groupNo'] = $report['groupNo'];
		$this->data['title'] = $report['title'];
		$this->data['body'] = $report['body'];
		$this->data['reference'] = $report['reference'];
		$this->data['uploadedOn'] = $report['uploadedOn'] == null ? null : date_create($report['uploadedOn'],timezone_open(" Europe/London"));
		$this->data['assignedGroupsList'] = isset($report['assignedGroupsList']) ? $report['assignedGroupsList'] : null;
	}

	public function getData(){
		return $this->data;
	}
	
	public static function addReport($db, $groupNo, $title, $body, $reference){
		$queryInsert = "INSERT INTO 
							report (`groupNo`,`title`, `body`, `reference`) 
					  	VALUES 
					  		(NULL, '".$title."', '".$body."', '".$reference."')
					  	";
		return $db->exec($queryInsert);
	}
	
	public static function editReport($db, $groupNo, $title, $body, $reference){
		$queryUpdate = "UPDATE 
							report
						SET
							title = '".$title."',
							body = '".$body."',
							reference = '".$reference."'
						WHERE
							groupNo = '".$groupNo."'
						";
		
		return $db->exec($queryUpdate);
	}

	public static function getReportByGroupNo($db, $groupNo) {
		$query = "SELECT
					R.groupNo,
					R.title,
					R.body,
					R.reference,
					R.uploadedOn
				  FROM
				  	report R
				  WHERE
				  	R.groupNo = '".$groupNo."'
		";

		$stmt = $db->prepare($query);
		$stmt->execute();
		$row =  $stmt->fetch();

		return new Report($row);
	}

	public static function getAllReports($db) {
		$reports = array();

		$query = "SELECT
					R.groupNo,
					R.title,
					R.body,
					R.reference,
					R.uploadedOn
				  FROM
				  	report R
				  ORDER BY
				  	R.groupNo
				  ASC
		";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row =  $stmt->fetch()) {
			$queryList = "SELECT 
							GROUP_CONCAT(groupNo SEPARATOR ',') as list
						FROM 
							assessment 
						WHERE 
							reportNo = '".$row['groupNo']."'
						";
			$stmtList = $db->prepare($queryList);
			$stmtList->execute();

			$row['assignedGroupsList'] = explode(',', $stmtList->fetch()['list']);

			array_push($reports, new Report($row));
		}

		return $reports;
	}

}

?>