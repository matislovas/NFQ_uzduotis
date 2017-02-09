<!DOCTYPE html>
<html>
	<head>
		<title>Knygos</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
	</head>
	<body>
		<ul class="w3-ul w3-border" style="max-width: 500px">
			<li><h2><?php echo $_GET['query']; ?></h2></li>
			<?php
				

				require __DIR__."/conf.php";

				// Create connection
				$conn = mysqli_connect($servername, $username, $password, $dbname);
				// Check connection
				if (!$conn) {
				     die("Connection failed: " . mysqli_connect_error());
				}

				mysqli_set_charset($conn,"utf8");

				$sql = "SELECT * FROM knygos WHERE `Pavadinimas` LIKE" . "'". $_GET['query']."'";

				$result = mysqli_query($conn, $sql);

				if(!$result){
				echo "Error creating query for book: " . mysqli_error($conn) . "\n";
				}

				if (mysqli_num_rows($result) > 0) {
			     // output data of each row
			     while($row = mysqli_fetch_assoc($result)) {
			         echo "<li>" ."Žanras: " . $row["Zanras"] . "</li>" .
			         "<li>" ."Leidimo metai: " . $row["Leidimo metai"] . "</li>" .
			         "<li>" ."Autorius: " . $row["Autorius"] . "</li>";
					}
				}	
			?>
		</ul>
	</body>
</html>
