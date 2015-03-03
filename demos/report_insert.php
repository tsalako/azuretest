<?php
  // 1. Create a database connection
  $dbhost = "eu-cdbr-azure-west-b.cloudapp.net";
  $dbuser = "b8833e255abd25";
  $dbpass = "f25e32ab";
  $dbname = "llamadb";
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  // Test if connection succeeded
  if(mysqli_connect_errno()) {
    die("Database connection failed: " . 
         mysqli_connect_error() . 
         " (" . mysqli_connect_errno() . ")"
    );
  }
?>
<?php
	// Often these are form values in $_POST
	$groupNo = 71;
	$title = "Mariem is trying a normal title with spacing";
	$body = "And here a loooot of text if possible since this is the body of the report. Oh well, I cannot think how something very interesting to say and I am quite frankly a little bu sleepy!";
	$reference = "yp, Bro. the artist. www.lynda.com. looks liek a reference to me!";
	
	// 2. Perform database query
	$query  = "INSERT INTO report (";
	$query .= "  groupNo, title, body, reference";
	$query .= ") VALUES (";
	//$query .= "  '61', 'MariemTitle', 'SomeOtherText', 'www.lynda.com'";
	$query .= "  '{$groupNo}', '{$title}', '{$body}', '{$reference}'";
	$query .= ")";

	$result = mysqli_query($connection, $query);

	if ($result) {
		// Success
		// redirect_to("somepage.php");
		echo "Success!";
	} else {
		// Failure
		// $message = "Subject creation failed";
		die("Database query failed. " . mysqli_error($connection));
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>Databases</title>
	</head>
	<body>
		
	</body>
</html>

<?php
  // 5. Close database connection
  mysqli_close($connection);
?>