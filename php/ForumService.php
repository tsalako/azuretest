<?php

include 'dbo/DB.php';
include 'dbo/User.php';
include 'dbo/Group.php';
include 'dbo/Thread.php';
include 'dbo/Post.php';
header("content-type:application/json");

/* 
 Forum service to handle requests that deal with thread and posts. 
*/

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if(isset($_POST['function'])){

	$db = new DB();

	switch($_POST['function']){
		case 'getThreadListByGroupNo':
			$params = $_POST['params'];
			$threads = Thread::getThreadListByGroupNo($db, $params['groupNo']);
			$return = array();			
			foreach ($threads as $thread){
				array_push($return, $thread->getData());
			}
			echo json_encode($return);
		break;
		case 'getSearchThreadList':
			$params = $_POST['params'];
			$threads = Thread::getSearchThreadList($db, $params['groupNo'], $params['searchString']);
			$return = array();			
			foreach ($threads as $thread){
				array_push($return, $thread->getData());
			}
			echo json_encode($return);
		break;
		case 'getPostListByThreadNo':
			$params = $_POST['params'];
			$return['thread'] = Thread::getThreadByThreadNo($db, $params['threadNo'])->getData();

			$posts = Post::getPostListByThreadNo($db, $params['threadNo']);
			$return['posts'] = array();
			foreach ($posts as $post){
				array_push($return['posts'], $post->getData());
			}
			echo json_encode($return);
		break;
		case 'startThread':
			$params = $_POST['params'];
			$thread = Thread::startThread($db, 
				$params['groupNo'],
				$params['creator'],
				$params['creatorName'],
				$params['title'],
				$params['description']);
			echo json_encode($thread->getData());
		break;
		case 'addPost':
			$params = $_POST['params'];
			//update thread fields as well
			$post = Post::addPost($db,
				 $params['threadNo'],
				 $params['creator'],
				 $params['comment']);
			echo json_encode($post->getData());
		break;
		default:
			echo die("Error - No function called '".$_POST['function']."'");
			exit();
		break;
	}
	exit();
}else{
	echo die("Bad parameters");
	exit();
}

?>