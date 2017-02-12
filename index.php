<!DOCTYPE html>
<html>
	<head>
		<title>Knygos</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
	</head>
	<body style="margin:5px;padding:5px">
		<?php
			//------------Function for mergin GET array on multiple parameters ---------------//
			$url = $_SERVER['REQUEST_URI'];

			function mergeQuerystring($url = null,$query = null,$recursive = false) {
			  // $url = 'http://www.google.com.au?q=apple&type=keyword';
			  // $query = '?q=banana';
			  // if there's a URL missing or no query string, return
			  if($url == null)
			    return false;
			  if($query == null)
			    return $url;
			  // split the url into it's components
			  $url_components = parse_url($url);
			  // if we have the query string but no query on the original url
			  // just return the URL + query string
			  if(empty($url_components['query']))
			    return $url.'?'.ltrim($query,'?');
			  // turn the url's query string into an array
			  parse_str($url_components['query'],$original_query_string);
			  // turn the query string into an array
			  parse_str(parse_url($query,PHP_URL_QUERY),$merged_query_string);
			  // merge the query string
			  if($recursive == true)
			    $merged_result = array_merge_recursive($original_query_string,$merged_query_string);
			  else
			    $merged_result = array_merge($original_query_string,$merged_query_string);
			  // find the original query string in the URL and replace it with the new one
			  return str_replace($url_components['query'],http_build_query($merged_result),$url);
			}
		?>
		<div class="w3-container" style="max-width: 750px; vertical-align: middle">
		  <ul class="w3-navbar w3-card-2 w3-light-grey">
		  	<li>
		  	<a href="index.php" style="width: 100px; font-size:80%; text-align:center; vertical-align:middle">Pradinis puslapis</a>
		  	</li>
		    <li class="w3-dropdown-hover">
		      <a href="#" style="width: 100px; font-size:80%; text-align:center; vertical-align:middle">Grupuoti pagal<i class="fa fa-caret-down"></i></a>
		      <div class="w3-dropdown-content w3-white w3-card-4" style="width: 120px; vertical-align:middle">
		        <a href="<?=mergeQuerystring($url,'?order=Knygos_pavadinimas');?>">Knygos pavadinimas</a>
		        <a href="<?=mergeQuerystring($url,'?order=Leidimo_metai');?>">Leidimo metai</a>
		      </div>
		    </li>
		    <li class="w3-dropdown-hover">
		      <a href="#" style="width: 100px; font-size:80%; text-align:center; vertical-align:middle">Įrašų puslapyje<i class="fa fa-caret-down"></i></a>
		      <div class="w3-dropdown-content w3-white w3-card-4">
		      	<a href="<?=mergeQuerystring($url,'?listsize=5');?>">5</a>
		        <a href="<?=mergeQuerystring($url,'?listsize=10');?>">10</a>
		        <a href="<?=mergeQuerystring($url,'?listsize=20');?>">20</a>
		        <a href="<?=mergeQuerystring($url,'?listsize=40');?>">40</a>
		      </div>
		    </li>
		    <div style="vertical-align: middle; padding-top: 6px">
		    <form action="#" style="vertical-align: middle">
		    <li style="vertical-align: middle;">
			    <div class="w3-third" style="width: 190px; vertical-align:middle"> 
					<input class="w3-input w3-border" type="text" name="search" placeholder="Paieška ..."/>  
	  			</div>
  			</li>
  			<li>
	  			<select class="w3-select w3-border" name="criteria" style="width: 100px; vertical-align:middle;">
				    <option value="Knygos_pavadinimas" disabled selected>Kriterijus</option>
				    <option value="Zanras">Žanras</option>
				    <option value="Autorius">Autorius</option>
				    <option value="Knygos_pavadinimas">Knygos pavadinimas</option>
				    <option value="Leidimo_metai">Leidimo metai</option>
			  	</select>
			</li>
			<li>
				<input style="width: 100px; vertical-align: middle" class="w3-btn w3-teal" type="Submit" value="Paieška" />
			</li>
			</form>
			</div>
		  </ul>
		</div>
		<br>
		<table class="w3-table-all w3-hoverable" style="max-width: 750px;">
		<tr>
		  <th>Knygos pavadinimas</th>
		  <th></th>
		</tr>
			<?php

				//------------DB connection ---------------//
				require __DIR__."/conf.php";
				$conn = mysqli_connect($servername, $username, $password, $dbname);
				if (!$conn) {
				    die("Connection failed: " . mysqli_connect_error());
				}
				mysqli_set_charset($conn,"utf8");

				//------------Adjacents pages in pagination bar---------------//
				$adjacents = 3;

				//------------GET array default values and assigning vars ---------------//
				if(empty($_GET['listsize'])){$limit=5;} else {$limit = $_GET['listsize'];}
				
				if(empty($_GET['page'])){$page=1;} else {$page = $_GET['page'];}

				if(empty($_GET['order'])){$ordering="id";} else {$ordering = $_GET['order'];}

				//------------How many items to show per page---------------//
				if($page) 
					$start = ($page - 1) * $limit;
				else
					$start = 0;

				//------------Get data from DB ---------------//
				if (!empty($_GET['search']) && !empty($_GET['criteria'])) {

					$term = mysqli_real_escape_string($conn, $_GET['search']);
					$criteria = mysqli_real_escape_string($conn, $_GET['criteria']);

					$total_pages = mysqli_num_rows(mysqli_query($conn, "SELECT Knygos_pavadinimas FROM knygos WHERE $criteria LIKE '%".$term."%'"));
					$sql = "SELECT Knygos_pavadinimas FROM knygos WHERE $criteria LIKE '%".$term."%' ORDER BY $ordering LIMIT $start, $limit";
				
				} else {

					$total_pages = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM knygos"));
					$sql = "SELECT Knygos_pavadinimas FROM knygos ORDER BY $ordering LIMIT $start, $limit";
				}

				$result = mysqli_query($conn, $sql);

				//------------Setup page vars for display ---------------//
				if ($page == 0) $page = 1;					
				$prev = $page - 1;							
				$next = $page + 1;							
				$lastpage = ceil($total_pages/$limit);		
				$lpm1 = $lastpage - 1;						
				
				//------------Pagination object implemented in variable ---------------//
				$pagination = "";
				if($lastpage > 1)
				{	
					$pagination .= "<ul class=\"w3-pagination\" style=\"padding-top: 4px\">";
					//previous button
					if ($page > 1) {
						$pagination.= "<li><a href=".mergeQuerystring($url,'?page='.$prev).">&laquo;</a></li>";
					} else {
						$pagination.= "<li><a href=\"#\">&laquo;</a></li>";	
					}
					
					//pages	
					if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
					{	
						for ($counter = 1; $counter <= $lastpage; $counter++) {

							if ($counter == $page){

								$pagination.= "<li><a class=\"w3-green\">$counter</a></li>";
							} else {

								$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $counter).">$counter</a></li>";					
							}
						}
					}

					//enough pages to hide some
					elseif($lastpage > 5 + ($adjacents * 2)) {

						//close to beginning; only hide later pages
						if($page < 1 + ($adjacents * 2)) {

							for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {

								if ($counter == $page) {
									$pagination.= "<li><a class=\"w3-green\">$counter</a></li>";
								} else {

									$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $counter).">$counter</a></li>";
								}
							}

							$pagination.= "<li><a style=\"background-color:transparent\">...</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page='.$lpm1).">$lpm1</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page='.$lastpage).">$lastpage</a></li>";		
						}

						//in middle; hide some front and some back
						elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

							$pagination.= "<li><a href=".mergeQuerystring($url,'?page=1').">1</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page=2').">2</a></li>";
							$pagination.= "<li><a style=\"background-color:transparent\">...</a></li>";
							for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {

								if ($counter == $page) {
									$pagination.= "<li><a class=\"w3-green\">$counter</a></li>";
								} else {

									$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $counter).">$counter</a></li>";
								}
							}

							$pagination.= "<li><a style=\"background-color:transparent\">...</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $lpm1).">$lpm1</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $lastpage).">$lastpage</a></li>";		
						}

						//close to end; only hide early pages
						else {

							$pagination.= "<li><a href=".mergeQuerystring($url,'?page=1').">1</a></li>";
							$pagination.= "<li><a href=".mergeQuerystring($url,'?page=2').">2</a></li>";
							$pagination.= "<li><a style=\"background-color:transparent\">...</a></li>";

							for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {

								if ($counter == $page) {
									$pagination.= "<li><a class=\"w3-green\">$counter</a></li>";
								} else {

									$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $counter).">$counter</a></li>";					
								}
							}
						}
					}
					
					//next button
					if ($page < $counter - 1) {

						$pagination.= "<li><a href=".mergeQuerystring($url,'?page='. $next)."> &raquo;</a></li>";
					} else {
						$pagination.= "<li><a href=\"#\">&raquo;</a></li>";
					}

					$pagination.= "</ul>\n";		
				}
			?>

			<?php
				while($row = mysqli_fetch_array($result)) {
			
					echo "<tr><td>" . $row["Knygos_pavadinimas"] . "</td><td><a href='book.php?query=" . $row["Knygos_pavadinimas"] . "' target='_blank'>Plačiau</a></td></tr>";
				}
			?>
		</table>
		<br>
		<div class="w3-container" style="max-width: 750px" >
		<ul class="w3-navbar w3-card-2 w3-light-grey">
		<?=$pagination?>
		</ul>
		</div>
	</body>
</html>