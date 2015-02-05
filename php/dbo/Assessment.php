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
	}

	public function getData(){
		return $this->data;
	}


}

?>