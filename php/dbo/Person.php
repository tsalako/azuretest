<?php

class Person{
	private $data = array();

	function __construct($person){
		$this->data['ID'] = $person['ID'];
		$this->data['name'] = $person['name'];
		$this->data['desc'] = $person['desc'];
		$this->data['country'] = $person['country'];
	}

	public function getData(){
		return $this->data;
	}

	public static function getAllPeople($db){
		$query = "SELECT * FROM people";
		$stmt = $db->prepare($query);
		$stmt->execute();
		$people = array();			
		while($row = $stmt->fetch()){
			array_push($people, new Person($row));
		}
		return $people;
	}

	public static function getPersonFromID($db, $id){
		$query = "SELECT * FROM people WHERE ID = " .$id. "";
		$stmt = $db->prepare($query);
		$stmt->execute();
		$person = new Person($stmt->fetch());
		return $person;
	}
}

?>