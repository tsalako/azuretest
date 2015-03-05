<?php

class Report{
	private $data = array();

	function __construct($report){
		$this->data['groupNo'] = $report['groupNo'];
		$this->data['title'] = $report['title'];
		$this->data['body'] = $report['body'];
		$this->data['reference'] = $report['reference'];
		$this->data['uploadedOn'] = $report['uploadedOn'] == null ? null : date_create($report['uploadedOn'],timezone_open(" Europe/London"));
	}

	public function getData(){
		return $this->data;
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

}

?>