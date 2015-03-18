<?php

/**
 * Thread Database Object Class
 */
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
		$this->data['creatorName'] = $thread['creatorName'];
	}

	public function getData(){
		return $this->data;
	}

	/**
	 * Gets the thread object from the database given a threadNo
	 * 
	 * @param $db       database connection
	 * @param $threadNo threadNo of thread to retrieve
	 * @return          the thread dbo object
	 */
	public static function getThreadByThreadNo($db, $threadNo){
		$query = "SELECT
					T.threadNo,
					T.groupNo,
					T.creator,
					T.title,
					T.description,
					T.lastEditor,
					T.lastUpdate,
					T.createdOn,
					COUNT(P.postNo) AS postCount,
					U.username AS creatorName
				FROM
					thread T
				LEFT JOIN
					post P 
				ON
					T.threadNo = P.threadNo
				JOIN
					user U
				ON
					T.creator = U.userNo
				WHERE
					T.threadNo = '".$threadNo."'
				AND
					T.creator = U.userNo
				";

		$stmt = $db->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch();

		return new Thread($row);
	}

	/**
	 * Gets all threads for a specified group
	 * 
	 * @param $db      database connection
	 * @param $groupNo groupNo of whose threads to be retrieved
	 * @return         array of thread dbo objects
	 */
	public static function getThreadListByGroupNo($db, $groupNo){
		$threads = array();
		$query = "SELECT
					T.threadNo,
					T.groupNo,
					T.creator,
					T.title,
					T.description,
					T.lastEditor,
					T.lastUpdate,
					T.createdOn,
					COUNT(P.postNo) AS postCount,
					U.username AS creatorName
				FROM
					thread T
				LEFT JOIN
					post P 
				ON
					T.threadNo = P.threadNo
				JOIN
					user U
				ON
					T.creator = U.userNo
				WHERE
					T.groupNo = '".$groupNo."'
				AND
					T.creator = U.userNo
				GROUP BY
					T.threadNo
				ORDER BY 
					T.createdOn 
				ASC
				";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row = $stmt->fetch()){
			if($row['threadNo']){
				array_push($threads, new Thread($row));
			}
		}

		return $threads;
	}

	/**
	 * Gets all threads for a specified group with the given string
	 * in their title or body.
	 * 
	 * @param $db           database connection
	 * @param $groupNo      groupNo of whose threads to be retrieved
	 * @param $searchString string to search for in the title and body
	 * @return              array of thread dbo objects
	 */
	public static function getSearchThreadList($db, $groupNo, $searchString){
		$threads = array();

		$query = "SELECT
					T.threadNo,
					T.groupNo,
					T.creator,
					T.title,
					T.description,
					T.lastEditor,
					T.lastUpdate,
					T.createdOn,
					COUNT(P.postNo) AS postCount,
					U.username AS creatorName
				FROM
					thread T
				LEFT JOIN
					post P 
				ON
					T.threadNo = P.threadNo
				JOIN
					user U
				ON
					T.creator = U.userNo
				WHERE
					T.groupNo = '".$groupNo."'
				AND
					CONCAT(T.title, T.description) LIKE '%".$searchString."%'
				GROUP BY
					T.threadNo
				ORDER BY 
					T.createdOn 
				ASC 
				";

		$stmt = $db->prepare($query);
		$stmt->execute();
		while($row = $stmt->fetch()){
			if($row['threadNo']){
				array_push($threads, new Thread($row));
			}
		}

		return $threads;
	}

	/**
	 * Saves the new thread to the database and send it back to the user.
	 * 
	 * @param $db          database connection
	 * @param $groupNo     groupNo of group to retrieve
	 * @param $creator     userNo of the creator of the thread
	 * @param $creatorName name of the creator of the thread
	 * @param $title       title of the thread
	 * @param $description description of the thread
	 * @return             thread that was just added
	 */
	public static function startThread($db, $groupNo, $creator, $creatorName, $title, $description){
		$queryInsert = "INSERT INTO
							thread (`threadNo`,`groupNo`,`creator`,`lastEditor`,`lastUpdate`,`title`,`description`,`createdOn`)
						VALUES
							(NULL, '".$groupNo."','".$creator."','".$creatorName."', CURRENT_TIMESTAMP, '".$title."', '".$description."', NULL)
						";

		$db->exec($queryInsert);

		$queryThreadNo = "SELECT
							T.threadNo,
							T.groupNo,
							T.creator,
							T.title,
							T.description,
							T.lastEditor,
							T.lastUpdate,
							T.createdOn,
							COUNT(P.postNo) AS postCount,
							U.username AS creatorName
						FROM
							thread T
						LEFT JOIN
							post P 
						ON
							T.threadNo = P.threadNo
						JOIN
							user U
						ON
							T.creator = U.userNo
						WHERE
							T.groupNo = '".$groupNo."'
						GROUP BY
							T.threadNo
						ORDER BY 
							T.createdOn 
						DESC 
						LIMIT 1
						";

		$stmt = $db->prepare($queryThreadNo);
		$stmt->execute();
		return new Thread($stmt->fetch());
	}
}

?>