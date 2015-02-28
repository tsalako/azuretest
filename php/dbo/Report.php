<?php

class Report{
	private $data = array();

	function __construct($report){
		$this->data['groupNo'] = $report['groupNo'];
		$this->data['title'] = $report['title'];
		$this->data['body'] = $report['body'];
		$this->data['reference'] = $report['reference'];
		$this->data['uploadedOn'] = date_create($report['uploadedOn'],timezone_open(" Europe/London"));
	}

	public function getData(){
		return $this->data;
	}


}

?>