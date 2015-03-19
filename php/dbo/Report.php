<?php

/**
 * Report Database Object Class
 */
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
	
	/**
	 * Adds a report to the database given the necessary fields.
	 * 
	 * @param $db         database connection
	 * @param $groupNo    number of the group adding the report
	 * @param $title      title of the report
	 * @param $body       the report text itself
	 * @param $reference  the references the group used to write the report
	 * @return            whethere the report was added or not
	 */
	public static function addReport($db, $groupNo, $title, $body, $reference){
		$queryInsert = "INSERT INTO 
							report (`groupNo`,`title`, `body`, `reference`) 
					  	VALUES 
					  		('".$groupNo."', '".$title."', '".$body."', '".$reference."')
					  	";
		return $db->exec($queryInsert);
	}

	/**
	 * Gets Report dbo object by its groupNo in the database.
	 * 
	 * @param $db      database connection
	 * @param $groupNo number of the group
	 * @return         report dbo object
	 */
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

		if($row){
			return new Report($row);
		} else {
			return null;
		}
		
	}

	/**
	 * Gets all reports from the database.
	 * 
	 * @param $db       database connection
	 * @return          array of dbo report objects 
	 */
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