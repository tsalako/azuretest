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


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Main Page Login</title>
    <meta name="description" content="Database Project Login">
    
    <link href="assets/bootstrap.min.css" rel="stylesheet" media="screen">
    <style type="text/css">
        body { 
        background: url(assets/city.jpg); 
        }
        .hero-unit { background-color: #fff; 
        }
        .center { display: block; margin: 5 auto; 
        }
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
      <a class="brand">UCL Llama DB</a>
      <div class="nav-collapse collapse">
        <ul class="nav pull-right">
          <li><a href="register.php">Register</a></li>
          <li class="divider-vertical"></li>
          <li class="dropdown">
            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Log In <strong class="caret"></strong></a>
            
            <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                <form action="index.php" method="post"> 
                    Username:<br /> 
                    <input type="text" name="userName" value="<?php echo $submitted_userName; ?>" /> 
                    <br /><br /> 
                    Password:<br /> 
                    <input type="password" name="password" value="" /> 
                    <br /><br /> 
                    <input type="submit" class="btn btn-info" value="Login" /> 
                </form> 
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="container hero-unit">
    <h1>Super cool UCL Llamadb project title</h1>
    <br>
    <p>Lorem ipsum dolor sit amet, eam vivendo nostrum sapientem cu, nec cu habemus honestatis dissentiunt. Pri aeterno praesent adversarium ne. Eos an esse aeque fabellas, eu est tamquam mediocritatem, nam te postea labitur feugiat. Ius ea fastidii luptatum pertinacia, vel sanctus ancillae an.
	Sit impetus explicari efficiantur id, eu case lobortis reformidans per, eum ceteros deserunt reprimique ea. Mutat accusam iudicabit nec id, nec ex libris deserunt complectitur. Cu mel utamur iuvaret, in posse volumus est. Vel aliquip eripuit appareat in. Molestiae dissentias quo ei, duo ut perfecto voluptatibus, cu dictas gubergren adipiscing duo.
	Vix illum summo id. At sea graeco persecuti, his maiorum consulatu ut. Sonet vitae bonorum ea eum, commune voluptaria te eam. Ne sit accumsan periculis maluisset. Ius et latine feugait accumsan, cum iuvaret mediocrem complectitur te, duo no atqui discere.
	Populo voluptatibus mei at, at simul volutpat qui. No sit dicam volumus. Duo persequeris intellegebat deterruisset cu, illud labitur deterruisset usu ei. Mei ea quis altera. Te equidem sapientem definitiones qui, est oratio doctus meliore no. Vidisse aliquid albucius ut sed.
    <br>
    <br>
    Update 1:
    <br>
    <br>
    Update 2:
    <br>
    etc...
    
    </p>

  
</div>

<!-- include javascript, jQuery FIRST -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</body>
</html>