<!DOCTYPE html>
<html>
	<head>
		<title>Knygos</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
	</head>
	<body>
		<table class="w3-table-all w3-hoverable" style="max-width: 700px;"">
		<tr>
		  <th>Knygos pavadinimas</th>
		  <!-- <th>Leidimo metai</th>
		  <th>Autorius</th>
		  <th>Zanras</th> -->
		  <th></th>
		</tr>

		<?php

		require __DIR__."/conf.php";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		// Check connection
		if (!$conn) {
		     die("Connection failed: " . mysqli_connect_error());
		}

		mysqli_set_charset($conn,"utf8");

		$sql = "SELECT `Pavadinimas`, `Leidimo metai`, `Autorius`, `Zanras` FROM knygos ORDER BY `Leidimo metai`";
		$result = mysqli_query($conn, $sql);

		if(!$result){
			echo "Error creating table query: " . mysqli_error($conn) . "\n";
		}

		if (mysqli_num_rows($result) > 0) {
		     // output data of each row
		     while($row = mysqli_fetch_assoc($result)) {
		         echo "<tr><td>" . $row["Pavadinimas"] . 
		         /*"</td><td>" . $row["Leidimo metai"] . 
		         "</td><td>" . $row["Autorius"] .
		         "</td><td>" . $row["Zanras"] .*/
		         "</td><td><a href='book.php?query=" . $row["Pavadinimas"] . "' target='_blank'>Plačiau</a></td></tr>";
		     }
		} else {
		     echo "0 results";
		}

		mysqli_close($conn);

		?>  
		</table>
	</body>
</html>