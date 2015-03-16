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

$validSession = isset($_SESSION['user']);

if(isset($_POST['function']) && $validSession){

	$db = new DB();

	switch($_POST['function']){
		case 'getThreadListByGroupNo':
			//remove params
			$threads = Thread::getThreadListByGroupNo($db, $_SESSION['user']['groupNo']);
			$return['threads'] = array();			
			foreach ($threads as $thread){
				array_push($return['threads'], $thread->getData());
			}
			$return['user'] = $_SESSION['user'];
			echo json_encode($return);
		break;
		case 'getSearchThreadList':
			//remove groupNo
			$params = $_POST['params'];
			$threads = Thread::getSearchThreadList($db, $_SESSION['user']['groupNo'], $params['searchString']);
			$return['threads'] = array();			
			foreach ($threads as $thread){
				array_push($return['threads'], $thread->getData());
			}
			$return['user'] = $_SESSION['user'];
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
			//remove groupNo, userNo, username
			$params = $_POST['params'];
			$thread = Thread::startThread($db, 
				$_SESSION['user']['groupNo'],
				$_SESSION['user']['userNo'],
				$_SESSION['user']['username'],
				$params['title'],
				$params['description']);
			echo json_encode($thread->getData());
		break;
		case 'addPost':
			//remove creator
			$params = $_POST['params'];
			$post = Post::addPost($db,
				 $params['threadNo'],
				 $_SESSION['user']['userNo'],
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
	if(!$validSession){
		echo die("notLoggedIn");
		exit();
	} else {
		echo die("Bad parameters");
		exit();
	}
}

?>