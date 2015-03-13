<?php

class Thread{
	private $data = array();

	function __construct($thread){
		$this->data['threadNo'] = $thread['threadNo'];
		$this->data['groupNo'] = $thread['groupNo'];
		$this->data['creator'] = $thread['creator'];
		$this->data['title'] = $thread['title'];
		$this->data['description'] = $thread['description'];
		$this->data['lastEditor'] = $thread['lastEditor'];
		$this->data['lastUpdate'] = date_create($thread['lastUpdate'],timezone_open(" Europe/London"));
		$this->data['createdOn'] = date_create($thread['createdOn'],timezone_open(" Europe/London"));
		$this->data['postCount'] = $thread['postCount'];
	}

	public function getData(){
		return $this->data;
	}

	public static function getThreadListByGroupNo($db, $groupNo){
		$threads = array();

		return $threads;
	}

	public static function getSearchThreadList($db, $groupNo, $searchString){
		$threads = array();

		return $threads;
	}

	public static function startThread($db, $groupNo, $creator, $lastEditor, $title, $description){
		return 1;
	}
}

?>