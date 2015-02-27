<?php

class Assessment{
	private $data = array();

	function __construct($assessment){
		$this->data['reportNo'] = $assessment['reportNo'];
		$this->data['groupNo'] = $assessment['groupNo'];
		$this->data['structureGrade'] = $assessment['structureGrade'];
		$this->data['strengthGrade'] = $assessment['strengthGrade'];
		$this->data['formatGrade'] = $assessment['formatGrade'];
		$this->data['qualityGrade'] = $assessment['qualityGrade'];
		$this->data['averageGrade'] = $assessment['averageGrade'];
		$this->data['comment'] = $assessment['comment'];
		$this->data['assessedOn'] = date_create($assessment['assessedOn'],timezone_open(" Europe/London"));
		$this->data['report'] = $assessment['report'];
	}

	public function getData(){
		return $this->data;
	}

	public static function getUndoneAssessments($db, $groupNo) {
		//get all groupNo where report exists so where reportNo matches with a report in the report table
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.comment,
					A.assessedOn,

					R.groupNo AS assessedReportNo,
					R.title,
					R.body,
					R.reference,
					R.uploadedOn
				 FROM 
				 	assessment A,
				 	report R
				 WHERE
				 	A.reportNo = R.groupNo
				 AND
				 	A.assessedOn IS NULL
				 AND
				 	A.groupNo = '".$groupNo."'
				 ";
		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$report = array();
			$report['reportNo'] = $row['reportNo'];
			$report['groupNo'] = $row['assessedReportNo'];
			$report['title'] = $row['title'];
			$report['body'] = $row['body'];
			$report['reference'] = $row['reference'];
			$report['uploadedOn'] = $row['uploadedOn'];

			$row['report'] = new Report($report);
			array_push($assessments, new Assessment($row));
		}

		return $assessments;
	}

	public static function getDoneAssessments($db, $groupNo) {
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.comment,
					A.assessedOn,


					R.groupNo AS assessedReportNo,
					R.title,
					R.body,
					R.reference,
					R.uploadedOn
				 FROM 
				 	assessment A,
				 	report R
				 WHERE
				 	A.reportNo = R.groupNo
				 AND
				 	A.assessedOn IS NOT NULL
				 AND
				 	A.groupNo = '".$groupNo"'
				 ";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$report = array();
			$report['reportNo'] = $row['reportNo'];
			$report['groupNo'] = $row['assessedReportNo'];
			$report['title'] = $row['title'];
			$report['body'] = $row['body'];
			$report['reference'] = $row['reference'];
			$report['uploadedOn'] = $row['uploadedOn'];

			$row['report'] = new Report($report);
			array_push($assessments, new Assessment($row));
	}

	public static function setAssessment($db, $reportNo, $groupNo, 
		$structureGrade, $strengthGrade, $formatGrade, $qualityGrade,
		$averageGrade, $comment) {

		$query = "UPDATE 
						assessment
					SET
						structureGrade = '".$structureGrade."',
						strengthGrade = '".$strengthGrade."',
						formatGrade = '".$formatGrade."',
						qualityGrade = '".$qualityGrade."',
						averageGrade = '".$averageGrade."',
						comment = '".$comment."',
						assessedOn = CURRENT_TIMESTAMP
					WHERE
						groupNo = '".$groupNo."'
					AND
						reportNo = '".$reportNo."'
						";
		$db->exec($query);


	}

	public static function getAllAssessments($db) {
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.structureGrade,
					A.comment,
					A.assessedOn,
					R.reportNo AS assessedReportNo
					R.title,
					R.body,
					R.reference,
					R.uploadedOn
				 FROM 
				 	assessment A,
				 	report R
				 WHERE
				 	A.reportNo = R.reportNo
				 AND
				 	A.assessedOn != NULL
				 ";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			$report = array();
			$report['reportNo'] = $row['reportNo'];
			$report['groupNo'] = $row['assessedReportNo'];
			$report['title'] = $row['title'];
			$report['body'] = $row['body'];
			$report['reference'] = $row['reference'];
			$report['uploadedOn'] = $row['uploadedOn'];

			$row['report'] = new Report($report);
			array_push($assessments, new Assessment($row));
		}

		return $assessments;
	}

	public static function assignAssessment($db, $reportNo, $groupNo) {
		$query = "INSERT INTO
					assessment (`reportNo`,`groupNo`)
				  VALUES
				  	('".reportNo."','".groupNo."')
				  ";
	    $db->exec($query);
	}

}

?>