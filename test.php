<!DOCTYPE html>
<html>
	<head>
		<title>Knygos</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
		<style type="text/css">
			
			div.pagination {
				padding: 3px;
				margin: 3px;
				position: relative;
			}

			div.pagination a {
				padding: 2px 5px 2px 5px;
				margin: 2px;
				border: 1px solid #AAAADD;
				text-decoration: none; /* no underline */
				color: #000099;
			}

			div.pagination a:hover, div.pagination a:active {
				border: 1px solid #000099;
				color: #000;
			}

			div.pagination span.current {
				padding: 2px 5px 2px 5px;
				margin: 2px;
				border: 1px solid #000099;
				font-weight: bold;
				background-color: #ccc;
				color: #000;
			}

			div.pagination span.disabled {
				padding: 2px 5px 2px 5px;
				margin: 2px;
				border: 1px solid #EEE;
				color: #DDD;
			}

		</style>
	</head>
	<body>
	<?php $_GET['listsize'] = 10; ?>
		<div class="w3-container" style="max-width: 715px;">
		  <ul class="w3-navbar w3-card-2 w3-light-grey">
		    <li class="w3-dropdown-hover">
		      <a href="#">Grupuoti pagal ...<i class="fa fa-caret-down"></i></a>
		      <div class="w3-dropdown-content w3-white w3-card-4">
		        <a href="<?=$_SERVER['REQUEST_URI']?>?&order=Knygos_pavadinimas">Knygos pavadinimas</a>
		        <a href="<?=$_SERVER['REQUEST_URI']?>?&order=Leidimo_metai">Leidimo metai</a>
		      </div>
		    </li>
		    <li class="w3-dropdown-hover">
		      <a href="#">Įrašų puslapyje<i class="fa fa-caret-down"></i></a>
		      <div class="w3-dropdown-content w3-white w3-card-4">
		        <a href="<?=$_SERVER['PHP_SELF']?>?<?php echo http_build_query(($_GET['listsize']=10)); ?>">10</a>
		        <a href="<?=$_SERVER['PHP_SELF']?>?<?php echo http_build_query(($_GET['listsize']=20)); ?>">20</a>
		        <a href="<?=$_SERVER['REQUEST_URI']?>?&listsize=40">40</a>
		      </div>
		    </li>
		  </ul>
		</div>
		<table class="w3-table-all w3-hoverable" style="max-width: 700px;"">
		<tr>
		  <th>Knygos pavadinimas</th>
		  <!-- <th>Leidimo metai</th>
		  <th>Autorius</th>
		  <th>Zanras</th> -->
		  <th></th>
		</tr>


<?php

	//echo $_SERVER['REQUEST_URI'];
	/*
		Place code to connect to your DB here.
	*/
	require __DIR__."/conf.php";	// include your code to connect to DB.

	// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		// Check connection
		if (!$conn) {
		     die("Connection failed: " . mysqli_connect_error());
		}

		mysqli_set_charset($conn,"utf8");

	$tbl_name="knygos";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;
	
	/* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	
	$total_pages = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM knygos"));
	
	/* Setup vars for query. */
	$targetpage = "test.php"; 	//your file name  (the name of this file)

	if(empty($_GET['listsize'])){$limit=10;} else {$limit = $_GET['listsize'];}
	
	//$limit = 10; 								//how many items to show per page
	//$page = $_GET['page'];
	if(empty($_GET['page'])){$page=1;} else {$page = $_GET['page'];}
	
	if($page) 
		$start = ($page - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0
	
	/* Get data. */
	if(empty($_GET['order'])){$ordering="id";} else {$ordering = $_GET['order'];}

	$sql = "SELECT Knygos_pavadinimas FROM $tbl_name ORDER BY $ordering LIMIT $start, $limit";
	$result = mysqli_query($conn, $sql);
	
	/* Setup page vars for display. */
	if ($page == 0) $page = 1;					//if no page var is given, default to 1.
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?page=$prev\">&laquo; previous</a>";
		else
			$pagination.= "<span class=\"disabled\">&laquo; previous</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"$targetpage?page=$next\">next &raquo;</a>";
		else
			$pagination.= "<span class=\"disabled\">next &raquo;</span>";
		$pagination.= "</div>\n";		
	}
?>

	<?php
		while($row = mysqli_fetch_array($result))
		{
	
		echo "<tr><td>" . $row["Knygos_pavadinimas"] . 
		         /*"</td><td>" . $row["Leidimo metai"] . 
		         "</td><td>" . $row["Autorius"] .
		         "</td><td>" . $row["Zanras"] .*/
		         "</td><td><a href='book.php?query=" . $row["Knygos_pavadinimas"] . "' target='_blank'>Plačiau</a></td></tr>";
	
		}
	?>



		</table>
		<?=$pagination?>

		<!-- <div class="w3-container" style="max-width: 715px;">
		  <ul class="w3-navbar w3-card-2 w3-light-grey">
		    <li class="w3-dropdown-hover">
		      <a href="#">Įrašų puslapyje<i class="fa fa-caret-down"></i></a>
		      <div class="w3-dropdown-content w3-white w3-card-4">
		        <a href="test.php?listsize=10">10</a>
		        <a href="test.php?listsize=20">20</a>
		        <a href="test.php?listsize=40">40</a>
		      </div>
		    </li>
		  </ul>
		</div> -->
	</body>
</html>
	