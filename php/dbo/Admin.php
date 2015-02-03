<?php

class Admin{
	private $data = array();

	function __construct($admin){
		$this->data['adminNo'] = $admin['adminNo'];
		$this->data['fName'] = $admin['fName'];
		$this->data['lName'] = $admin['lName'];
	}

	public function getData(){
		return $this->data;
	}


}

?>