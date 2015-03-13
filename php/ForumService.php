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
			$posts = Post::getPostListByThreadNo($db, $params['threadNo']);
			$return = array();			
			foreach ($posts as $post){
				array_push($return, $post->getData());
			}
			echo json_encode($return);
		break;
		case 'startThread':
			$params = $_POST['params'];
			$errorBool = Thread::startThread($db, 
				$params['groupNo'],
				$params['creator'],
				$params['lastEditor'],
				$params['title'],
				$params['description']);
			echo $errorBool ? json_encode('thread started') : die("thread start fail");
		break;
		case 'addPost':
			$params = $_POST['params'];
			//update thread fields as well
			$errorBool = Post::addPost($db,
				 $params['groupNo'],
				 $params['threadNo'],
				 $params['creator'],
				 $params['comment']);
			echo $errorBool ? json_encode('successfully posted') : die("failed post");
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