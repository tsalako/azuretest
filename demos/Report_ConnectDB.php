<?php
  // 1. Create a database connection
  $dbhost = "eu-cdbr-azure-west-b.cloudapp.net";
  $dbuser = "b8833e255abd25";
  $dbpass = "f25e32ab";
  $dbname = "llamadb";
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  // Test if connection occurred.
  if(mysqli_connect_errno()) {
    die("Database connection failed: " . 
         mysqli_connect_error() . 
         " (" . mysqli_connect_errno() . ")"
    );
  }

?>

<?php
	// 2. Perform database query
	
	//Simple SELECT
	
	////$selectQuery  = "SELECT * ";
	////$selectQuery .= "FROM report";
	////$selectResult = mysqli_query($connection, $selectQuery);
	// Test if there was a query error
	////if (!$selectResult) {
	////	die("Database query failed.");
	////}

	//Simple INSERT

	$groupNo = 51;
	$title = "Mariem runs tests on Report INSERT";
	$body = "Well well...let's see how it goes";
	$reference = "www.somekind of URL.com smith,jackson. "

	echo "did we make it to parameters?";

	$insertQuery = "INSERT INTO report (groupNo,title,body,reference) ";
	$insertQuery .= "VALUES ({$groupNo},{$title},{$body},{$reference})";
	$insertResult = mysqli_query($connection, $insertQuery);

	if ($insertResult) {
		echo "Yay!";
	} else {
		// Failure
		die("Database query failed. " . mysql_error($connection));
	}	
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>Test Report</title>
	</head>
	<body>

<!-- 		<form action="form_processing.php" method="post">
		  Title: <input type="text" name="Title" value="" /><br />
		  Body: <input type="text" name="Body" value="" /><br />
		  References: <input type="text" name="References" value="" /><br />
			<br />
		  <input type="submit" name="submit" value="Submit" />
		</form> -->

		<!-- Communication with DB -->
<!-- 		<?php
    	// 3. Use returned data (if any)
    // 		while($row = mysqli_fetch_assoc($selectResult)) {
    //     		// output data from each row
        		
    // 			echo $row["groupNo"] . "<br />" . "<br />";
    // 			echo $row["title"] . "<br />" . "<br />";
    // 			echo $row["body"] . "<br />" . "<br />";
    // 			echo $row["reference"] . "<br />" . "<br />";

    //     		//DEFAULT
    //     		//var_dump($row);
        		
				// echo "<hr />";
    // 		}
    	?> -->
    	<?php
    	// 4. Release returned data
		  	mysqli_free_result($insertResult);
    	?>

	</body>
</html>

<?php
  // 5. Close database connection
  mysqli_close($connection);
?>