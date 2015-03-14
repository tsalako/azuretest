<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Page</title>
    <meta name="description" content="Bootstrap Tab + Fixed Sidebar Tutorial with HTML5 / CSS3 / JavaScript">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    
    
    <link href="assets/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="assets/style.css" rel="stylesheet">

    <style type="text/css">
        body { background: url(assets/city.jpg); }
        .hero-unit { background-color: #fff; }
        .center { display: block; margin: 0 auto; }
    </style>
</head>

<body>

<div class="navbar navbar-fixed-top navbar-default">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand">Wild Llama</a>
      <div class="nav-collapse">
        <ul class="nav pull-right">
          <li><a href="index.php">About</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="text-field">
    
    
 
    <form action="login.php" method="post"> 
        
    <form>

	<h2>Log In</h2>
        <input type="text" class="text-field" name="userName" placeholder="Username" value="" /> 
        
        <input type="password" class="text-field" name="password" placeholder="Password" value="" /> 
        
        <input type="submit" class="submit" value="Login" /> 
    </form>
</div>


<?php 
    require("config.php"); 
    $submitted_userName = ''; 
    if(!empty($_POST)){ 
        $query = " 
            SELECT  
                userName, 
                password, 
                salt, 
                fName,
                lName
            FROM user
            WHERE 
                userName = :userName 
        "; 
        $query_params = array( 
            ':userName' => $_POST['userName'] 
        ); 
         
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex)
        { 
        die("Failed to run query: " . $ex->getMessage()); 
        } 
        
        $login_ok = false; 
        $row = $stmt->fetch(); 
        if($row){ 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            } 
            if($check_password === $row['password']){
                $login_ok = true;
            } 
        } 

        if($login_ok){ 
            unset($row['salt']); 
            unset($row['password']); 
            $_SESSION['user'] = $row;  
            header("Location: secret.php"); 
            die("Redirecting to: secret.php"); 
        } 
        else{ 
            print("Login Failed."); 
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8'); 
        } 
    } 
?> 
