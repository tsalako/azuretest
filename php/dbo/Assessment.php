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
		$this->data['report'] = isset($assessment['report']) ? $assessment['report'] : null;
	}

	public function getData(){
		$returnData = $this->data;
		$returnData['report'] = $this->data['report'] != null ? $this->data['report']->getData() : null;
		return $returnData;
	}

	public static function getUndoneAssessmentsByGroupNo($db, $groupNo) {
		//get all groupNo where report exists so where reportNo matches with a report in the report table
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.strengthGrade,
					A.formatGrade,
					A.qualityGrade,
					(A.structureGrade+A.formatGrade+A.strengthGrade+A.qualityGrade)/4 AS averageGrade,
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
				 ORDER BY
				 	R.uploadedOn
				 DESC
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

	public static function getDoneAssessmentsByGroupNo($db, $groupNo) {
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.strengthGrade,
					A.formatGrade,
					A.qualityGrade,
					(A.structureGrade+A.formatGrade+A.strengthGrade+A.qualityGrade)/4 AS averageGrade,
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
				 	A.groupNo = '".$groupNo."'
				 ORDER BY
				 	A.assessedOn
				 DESC
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

	public static function getDoneAssessmentsByReportNo($db, $reportNo) {
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.strengthGrade,
					A.formatGrade,
					A.qualityGrade,
					(A.structureGrade+A.formatGrade+A.strengthGrade+A.qualityGrade)/4 AS averageGrade,
					A.comment,
					A.assessedOn
				 FROM 
				 	assessment A
				 WHERE
				 	A.reportNo = '".$reportNo."'
				 AND
				 	A.assessedOn IS NOT NULL
				 ORDER BY
				 	A.assessedOn
				 DESC
				 ";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row =  $stmt->fetch()){
			array_push($assessments, new Assessment($row));
		}

		return $assessments;
	}

	public static function getAllAssessments($db) {
		$assessments = array();

		$query = "SELECT
					A.reportNo,
					A.groupNo,
					A.structureGrade,
					A.strengthGrade,
					A.formatGrade,
					A.qualityGrade,
					(A.structureGrade+A.formatGrade+A.strengthGrade+A.qualityGrade)/4 AS averageGrade,
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
				 ORDER BY
				 	A.assessedOn
				 DESC
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

	public static function setAssessment($db, $reportNo, $groupNo, 
		$structureGrade, $strengthGrade, $formatGrade, $qualityGrade,
		$comment) {

		$query = "UPDATE 
						assessment
					SET
						structureGrade = '".$structureGrade."',
						strengthGrade = '".$strengthGrade."',
						formatGrade = '".$formatGrade."',
						qualityGrade = '".$qualityGrade."',
						comment = '".$comment."',
						assessedOn = CURRENT_TIMESTAMP
					WHERE
						groupNo = '".$groupNo."'
					AND
						reportNo = '".$reportNo."'
						";
		return $db->exec($query);


	}

	public static function assignAssessment($db, $reportNo, $groupNo) {
		$query = "INSERT INTO
					assessment (`reportNo`,`groupNo`)
				  VALUES
				  	('".$reportNo."','".$groupNo."')
				  ";
	    return $db->exec($query);
	}

}

?>