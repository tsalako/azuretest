<?php

include 'dbo/DB.php';
include 'dbo/Person.php';
include_once 'psl-config.php';
header("content-type:application/json");


function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
}

/* function login()
{
	if(isset($_POST['username']))
	{
		
		$db = new mysqli('ftps://waws-prod-am2-021.ftp.azurewebsites.windows.net', 'root', 'root', 'llamadb');
		
		$username = $db->real_escape_string($_POST['username']);
		$password = $db->real_escape_string($_POST['password']);
		
		if(userExists($username))
		{
			
			if(verifyPassword($username, $password))
			{
		
				header('Location: /login.php?message=Successfully logged in');
				
			}
			else
			{
		
				header('Location: /login.php?message=Incorrect password');
				
			}
			
		}
		else
		{
		
			header('Location: /GuruCoder-Tutorials/login.php?message=No user exists');
			
		}
		
	}
}
*/


function verifyPassword($username, $password)
{
		
	$db = new mysqli('ftps://waws-prod-am2-021.ftp.azurewebsites.windows.net', 'root', 'root', 'llamadb');
	
	$result = $db->query('
	SELECT password
	FROM users
	WHERE username = "'.$username.'"
	LIMIT 1
	');
	
	while($row = $result->fetch_object())
	{		
	
		if(crypt($password, $row->password) == $row->password)
		{
			
			return true;
			
		}
		else
		{
		
			return false;
			
		}
		
	}
}

function userExists($username)
{
		
	$db = new mysqli('ftps://waws-prod-am2-021.ftp.azurewebsites.windows.net', 'root', 'root', 'llamadb');
	
	$result = $db->query('
	SELECT username
	FROM users
	WHERE username = "'.$username.'"
	LIMIT 1
	');
	
	if(!$result)
	{
		
		echo $db->error;
		
	}
	
	return (bool)$result->num_rows;
}

function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}

if(isset($_POST['function'])){

	$db = new DB();
	
	switch($_POST['function']){
		case 'getPeople':
			$people = Person::getAllPeople($db);
			$return = array();			
			foreach ($people as $person){
				array_push($return, $person->getData());
			}
			echo json_encode($return);
		break;
		case 'getPersonByID':
			$params = $_POST['params'];
			$person = Person::getPersonFromID($db, $params['ID']);
			echo json_encode($person->getData());
		break;
		case 'setPeople':
			echo json_encode('we were set! I promise.');
		break;
		default:
			echo "Error - No function called '".$_POST['function']."'";
			exit();
		break;
	}
	exit();
}else{
	echo "Bad parameters";
	exit();
}

?>