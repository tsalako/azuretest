<?php 
    require("config.php");
    if(!empty($_POST)) 
    { 
        // checks to see if forms empty
        if(empty($_POST['userName'])) 
        { 
        die("Please enter a username."); 
        } 
        
        if(empty($_POST['password'])) 
        { 
        die("Please enter a password.");
        } 
        
        if(empty($_POST['fName'])) 
        { 
        die("Please enter a first name.");
        } 
        
        if(empty($_POST['lName'])) 
        { 
        die("Please enter a lastname.");
        } 
         
        // Check if username is already taken
        $query = " 
            SELECT 
                1 
            FROM user 
            WHERE 
                userName = :userName 
        "; 
        $query_params = array( ':userName' => $_POST['userName'] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        
        catch(PDOException $ex){ 
        die("Failed to run query: " . $ex->getMessage()); 
        } 
        
        $row = $stmt->fetch(); 
        
        if($row)
        { 
        die("This username is already in use"); 
        } 
         
        // Add row to database 
        $query = " 
            INSERT INTO user ( 
                userName, 
                password, 
                salt, 
                fName,
                lName 
            ) VALUES ( 
                :userName, 
                :password, 
                :salt, 
                :fName,
                :lName 
            ) 
        "; 
         
        // Security
        // Random Salting
       $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
        $password = hash('sha256', $_POST['password'] . $salt); 
        for($round = 0; $round < 65536; $round++){ $password = hash('sha256', $password . $salt); } 
        $query_params = array( 
            ':userName' => $_POST['userName'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':fName' => $_POST['fName'], 
            ':lName' => $_POST['lName']
        ); 
        try {  
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex)
        { 
        die("Failed to run query: " . $ex->getMessage()); 
        } 
        header("Location: index.php"); 
        die("Redirecting to index.php"); 
    } 
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Page</title>
    <meta name="description" content="Bootstrap Tab + Fixed Sidebar Tutorial with HTML5 / CSS3 / JavaScript">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    <link href="assets/bootstrap.min.css" rel="stylesheet" media="screen">
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
      <a class="brand">Register - or some other cool title</a>
      <div class="nav-collapse">
        <ul class="nav pull-right">
          <li><a href="index.php">Return Home</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="container hero-unit">
    <h1>Register</h1> <br /><br />
    <form action="register.php" method="post"> 
        <label>Username:</label> 
        <input type="text" name="userName" value="" /> 
        <label>Password:</label> 
        <input type="password" name="password" value="" /> 
        <label>First Name:</label> 
        <input type="text" name="fName" value="" />
        <label>Last Name:</label> 
        <input type="text" name="lName" value="" />
        
        <br /><br />
        <input type="submit" class="btn btn-info" value="Register" /> 
    </form>
</div>

</body>
</html>