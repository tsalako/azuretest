<?php

class Student{
	private $data = array();

	function __construct($group){
		$this->data['groupNo'] = $group['groupNo'];
		$this->data['fName'] = $group['fName'];
		$this->data['lName'] = $group['lName'];
		$this->data['groupNo'] = $group['groupNo'];
	}

	public function getData(){
		return $this->data;
	}


}

?>