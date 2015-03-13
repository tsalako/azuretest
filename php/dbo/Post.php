<?php

class Post{
	private $data = array();

	function __construct($post){
		$this->data['postNo'] = $post['postNo'];
		$this->data['threadNo'] = $post['threadNo'];
		$this->data['creator'] = $post['creator'];
		$this->data['comment'] = $post['comment'];
		$this->data['postedOn'] = date_create($post['postedOn'],timezone_open(" Europe/London"));
	}

	public function getData(){
		return $this->data;
	}

	public static function getPostListByThreadNo($db, $threadNo){
		$posts = array();

		return $posts;
	}

	public static function addPost($db, $groupNo, $threadNo, $creator, $comment){
		return 1;
	}
}

?>