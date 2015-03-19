<?php

/**
 * Post Database Object Class
 */
class Post{
	private $data = array();

	function __construct($post){
		$this->data['postNo'] = $post['postNo'];
		$this->data['threadNo'] = $post['threadNo'];
		$this->data['creator'] = $post['creator'];
		$this->data['creatorName'] = $post['creatorName'];
		$this->data['comment'] = $post['comment'];
		$this->data['postedOn'] = $post['postedOn'];
	}

	public function getData(){
		return $this->data;
	}

	/**
	 * Gets list of Post dbo objects by their threadNo from the database.
	 * 
	 * @param $db       database connection
	 * @param $threadNo number of the thread
	 * @return          array of dbo posts objects 
	 */
	public static function getPostListByThreadNo($db, $threadNo){
		$posts = array();
		$query = "SELECT
					P.postNo,
					P.threadNo,
					P.comment,
					P.postedOn,
					P.creator,
					U.username AS creatorName
				FROM
					user U,
					post P
				WHERE
					P.threadNo = '".$threadNo."'
				AND
					U.userNo = P.creator
				";
		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row = $stmt->fetch()){
			array_push($posts, new Post($row));
		}
		return $posts;
	}

	/**
	 * Adds a post to the database given the necessary fields.
	 * 
	 * @param $db       database connection
	 * @param $threadNo number of the thread
	 * @param $creator  userNo of the creator of the post
	 * @param $comment  the post comment
	 * @return          the post that was added
	 */
	public static function addPost($db, $threadNo, $creator, $comment){
		$queryInsert = "INSERT INTO
					post (`postNo`,`threadNo`,`creator`,`comment`,`postedOn`)
				VALUES
					(NULL, '".$threadNo."','".$creator."','".$comment."', NULL)
					";

		 $db->exec($queryInsert);

		 $queryUpdate = "UPDATE
		 					thread T, user U
		 				SET
		 					T.lastEditor = U.userName,
		 					T.lastUpdate = CURRENT_TIMESTAMP
		 				WHERE
		 					threadNo = '".$threadNo."'
						AND
							U.userNo = '".$creator."'
		 				";
		 $db->exec($queryUpdate);

		 $queryPost = "SELECT
						P.postNo,
						P.threadNo,
						P.comment,
						P.postedOn,
						P.creator,
						U.username AS creatorName
					FROM
						user U,
						post P
					WHERE
						P.threadNo = '".$threadNo."'
					AND
						U.userNo = P.creator
					ORDER BY 
						P.postedOn 
					DESC 
					LIMIT 1";

		$stmt = $db->prepare($queryPost);
		$stmt->execute();
		return new Post($stmt->fetch());
	}
}

?>