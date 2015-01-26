<?php
	include 'includes/connection.php';

	$query = "SELECT * FROM people";
	$stmt = $db->prepare($query);
	$stmt->execute();
	while($row = $stmt->fetch()){
		echo "<h3>" . $row['name'] . "<h3>";
	}

	$query = "UPDATE people SET name = 'Mariem' WHERE ID = 2";
	$db->exec($query);

	$query = "SELECT * FROM people";
	$stmt = $db->prepare($query);
	$stmt->execute();
	while($row = $stmt->fetch()){
		echo "<h3>" . $row['name'] . "<h3>";
	}

	$query = "UPDATE people SET name = 'Meriem' WHERE ID = 2";
	$db->exec($query);
?>