<?php

class Group{
	private $data = array();

	function __construct($group){
		$this->data['groupNo'] = $group['groupNo'];
		$this->data['name'] = $group['name'];
		$this->data['assignedBy'] = $group['assignedBy'];
	}

	public function getData(){
		return $this->data;
	}


}

?>