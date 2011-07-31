<?php	## Handle searches differently
	if ($_SESSION['userid'] && !$alllang){
		$languagelimit = "AND languageid = (SELECT languageid FROM users WHERE id = ".$_SESSION['userid'].")";
		$query = "SELECT languages.name FROM users INNER JOIN languages ON users.languageid = languages.id WHERE users.id=".$_SESSION['userid'];
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	
	if ($function == 'Search')  {
		$title = 'Search : ' . $string;
	}
	if ($function == 'Advanced Search')  {
		$title = 'Advanced Search_';
	}
	elseif ($function == 'OverviewSearch')  {
		$title = 'Overview Search : ' . $string;
	}
	else  {
		$title = $letter;
	}
?>

<h1 class="arcade">Games|<?=$title?></h1>
	
	<!-- Start Advanced Search -->
	<div style="width: 80%; margin: auto; margin-bottom: 12px;">
		<?php
			if($function == "Advanced Search")
			{
			?>
		<a href="javascript: void();" onclick="$('#advancedSearchPanel').slideToggle(); if($('#chevron').attr('src') == 'images/common/icons/expand_16.png') { $('#chevron').attr('src', 'images/common/icons/collapse_16.png'); } else { $('#chevron').attr('src', 'images/common/icons/expand_16.png'); }" style="text-decoration: none; outline: 0px; color: #EF5F00; font-weight: bold;">Advanced Search <img id="chevron" src="images/common/icons/collapse_16.png" alt="Expand/Collapse" style="vertical-align:middle;" /></a>
		<div id="advancedSearchPanel" style="border: 1px solid #666; background-color: #999; padding: 15px; border-radius: 10px; color: #FFF; font-weight: bold;">
			<?php
			}
			else
			{
			?>
		<a href="javascript: void();" onclick="$('#advancedSearchPanel').slideToggle(); if($('#chevron').attr('src') == 'images/common/icons/expand_16.png') { $('#chevron').attr('src', 'images/common/icons/collapse_16.png'); } else { $('#chevron').attr('src', 'images/common/icons/expand_16.png'); }" style="text-decoration: none; outline: 0px; color: #EF5F00; font-weight: bold;">Advanced Search <img id="chevron" src="images/common/icons/expand_16.png" alt="Expand/Collapse" style="vertical-align:middle;" /></a>
		<div id="advancedSearchPanel" style="display: none; border: 1px solid #666; background-color: #999; padding: 15px; border-radius: 10px; color: #FFF; font-weight: bold;">
			<?php
			}
		?>
			<form>
				<table cellspacing="6" width="100%">
					<tr>
						<td>Search: <input type="text" name="string" value="<?php echo $string; ?>" size="40" /></td>
						<td>Platform:
							<select name="stringPlatform">
								<option value="">Any</option>
								<?php
									$platformQuery = mysql_query(" SELECT * FROM platforms ORDER BY name ASC");
									while($platformResult = mysql_fetch_assoc($platformQuery))
									{
										?>
											<option<?php if($stringPlatform == $platformResult['id']) { echo " selected"; } ?> value="<?php echo $platformResult['id']; ?>"><?php echo $platformResult['name']; ?></option>
										<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Rating:
							<select name="stringRating">
								<option value="">Any</option>
								<option<?php if($stringRating == "eC - Early Childhood") { echo " selected"; } ?>>EC - Early Childhood</option>
                                <option<?php if($stringRating == "E - Everyone") { echo " selected"; } ?>>E - Everyone</option>
                                <option<?php if($stringRating == "E10+ - Everyone 10+") { echo " selected"; } ?>>E10+ - Everyone 10+</option>
                                <option<?php if($stringRating == "T - Teen") { echo " selected"; } ?>>T - Teen</option>
                                <option<?php if($stringRating == "M - Mature") { echo " selected"; } ?>>M - Mature</option>
                                <option<?php if($stringRating == "RP - Rating Pending") { echo " selected"; } ?>>RP - Pating Pending</option>
							</select>
						</td>
						<td>Genre:
							<select name="stringGenres">
								<option value="">Any</option>
								<?php
									$genresQuery = mysql_query(" SELECT * FROM genres ");
									while($genresResult = mysql_fetch_assoc($genresQuery))
									{
										?>
											<option<?php if($stringGenres == $genresResult['genre']) { echo " selected"; } ?> value="<?php echo $genresResult['genre']; ?>"><?php echo $genresResult['genre']; ?></option>
										<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Co-op:
                            <select name="stringCoop">
								<option value="">Any</option>
								<option<?php if($stringCoop == "Yes") { echo " selected"; } ?>>Yes</option>
								<option<?php if($stringCoop == "No") { echo " selected"; } ?>>No</option>
                                
							</select>
						</td>
						<td align="right">
                            <input type="hidden" name="tab" value="listseries" />
                            <input type="hidden" name="function" value="Advanced Search" />
							<input type="submit" value="Search..."/>	
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<!-- End Advanced Search -->

		<?php	## Run the games query
			$gamecount = 0;
			$string = mysql_real_escape_string($string);
			$letter = mysql_real_escape_string($letter);			

			if ($function == 'Search')  {
				$query = "SELECT g.*, p.name FROM games as g, platforms as p WHERE (SOUNDEX(g.GameTitle) LIKE CONCAT('%', SOUNDEX('$string'), '%') OR g.GameTitle LIKE '%$string%') AND g.Platform = p.id";
				if(!empty($sortBy))
				{
					$query .= " ORDER BY $sortBy, GameTitle ASC";
				}
				else
				{
					$query .= " ORDER BY GameTitle";
				}
			}
			## Start Advanced Search Query
			elseif ($function == 'Advanced Search')  {
				$query = "SELECT g.*, p.name FROM games as g, platforms as p WHERE (SOUNDEX(g.GameTitle) LIKE CONCAT('%', SOUNDEX('$string'), '%') OR g.GameTitle LIKE '%$string%')";
				if($stringPlatform != "")
				{
					$query = $query .  " AND g.Platform = '$stringPlatform' ";
				}
				if($stringRating != "")
				{
					$query = $query .  " AND g.Rating = '$stringRating' ";
				}
				if($stringGenres != "")
				{
					$query = $query .  " AND g.Genre LIKE '%$stringGenres%' ";
				}
				if($stringCoop != "")
				{
					$query = $query .  " AND g.coop = '$stringCoop' ";
				}
				$query = $query .  "AND g.Platform = p.id ";
				if(!empty($sortBy))
				{
					$query .= " ORDER BY $sortBy, GameTitle ASC";
				}
				else
				{
					$query .= " ORDER BY GameTitle";
				}
			}
			## End Advanced Search Query
			/*elseif ($function == 'OverviewSearch')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesoverview.languageid) As language FROM translation_seriesoverview WHERE translation LIKE '%$string%' ORDER BY ID";
			}
			else if ($letter == 'OTHER')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) NOT BETWEEN 'A' AND 'Z' $languagelimit ORDER BY translation";
			}
			else  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) = '$letter' $languagelimit ORDER BY translation";
			}*/
			
	?>
	
	<!-- Start Pagination -->
	<?php

		$adjacents = 3;
		
		/* 
		   First get total number of rows in data table. 
		   If you have a WHERE clause in your query, make sure you mirror it here.
		*/
		$total_pages = mysql_num_rows(mysql_query($query));
		
		/* Setup vars for query. */
		if(!isset($limit))
		{
			$limit = 20; 								//how many items to show per page
		}
		if($page) 
			$start = ($page - 1) * $limit; 			//first item to display on this page
		else
			$start = 0;								//if no page var is given, set start to 0
		
		/* Get data. */
		$query = $query . " LIMIT $start, $limit";
		
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
				$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$prev\">&laquo; prev</a>";
			else
				$pagination.= "<span class=\"disabled\">&laquo; prev</span>";	
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
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
							$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$lastpage\">$lastpage</a>";		
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=1\">1</a>";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$lastpage\">$lastpage</a>";		
				}
				//close to end; only hide early pages
				else
				{
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=1\">1</a>";
					$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= "<a href=\"?tab=listseries&string=$string&function=$function&sortBy=$sortBy&limit=$limit&page=$next\">next &raquo;</a>";
			else
				$pagination.= "<span class=\"disabled\">next &raquo;</span>";
			$pagination.= "</div>";		
		}
	?>
	<!-- End Pagination -->

	<!-- Start Sort By -->
	<form style="text-align: right;">
		<input type="hidden" name="tab" value="<?=$tab?>" />
        <input type="hidden" name="function" value="<?=$function?>" />
		<input name="string" type="hidden" value="<?=$string?>" />
		<input name="stringPlatform" type="hidden" value="<?=$stringPlatform?>" />
		<input name="stringRating" type="hidden" value="<?=$stringRating?>" />
		<input name="stringGenres" type="hidden" value="<?=$stringGenres?>" />
		<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
			<option <?php if($sortBy == "g.GameTitle"){ echo "selected"; } ?> value="g.GameTitle">Name</option>
			<option <?php if($sortBy == "p.name"){ echo "selected"; } ?> value="p.name">Platform</option>
			<option <?php if($sortBy == "g.Genre"){ echo "selected"; } ?> value="g.Genre">Genre</option>
			<option <?php if($sortBy == "g.Rating"){ echo "selected"; } ?> value="g.Rating">Rating</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;Show: <select name="limit" onchange="this.form.submit();">
			<option <?php if($limit == 10){ echo "selected"; } ?> value="10">10 Rows</option>
			<option <?php if($limit == 20){ echo "selected"; } ?> value="20">20 Rows</option>
			<option <?php if($limit == 40){ echo "selected"; } ?> value="40">40 Rows</option>
			<option <?php if($limit == 80){ echo "selected"; } ?> value="80">80 Rows</option>
			<option <?php if($limit == 100){ echo "selected"; } ?> value="100">100 Rows</option>
		</select></p>
	</form>
	<!-- End Sort By -->
	
	<div style="clear: both;"></div>
	
	<!-- Start Show Pagination -->
	<?=$pagination?>
	<!-- End Show Pagination -->
	
	<table width="100%" border="0" cellspacing="1" cellpadding="7" id="listtable">
		<tr>
			<td class="head arcade" align="center">ID</td>
			<td class="head arcade">Game Title</td>
			<td class="head arcade">Platform</td>
			<td class="head arcade">Genre</td>
			<td class="head arcade">ESRB</td>
			<td class="head arcade">Boxart</td>
			<td class="head arcade">Fanart</td>
			<td class="head arcade">Banner</td>
		</tr>

	<?php			
			##  START RUN SEARCH QUERY!!!!
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			##  END RUN SEARCH QUERY!!!!

			## Display each game
			while ($game = mysql_fetch_object($result)) {
				$platformIdQuery = mysql_query("SELECT * FROM platforms WHERE id = '$game->Platform' LIMIT 1");
				$platformIdResult = mysql_fetch_object($platformIdQuery);
				
				$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
				$boxartResult = mysql_num_rows($boxartQuery);
				
				$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
				$fanartResult = mysql_num_rows($fanartQuery);

				$bannerQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'series' LIMIT 1");
				$bannerResult = mysql_num_rows($bannerQuery);
				
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				?>
				<tr>
					<td align="center" class="<?php echo $class; ?>"><?php echo $game->id; ?></td>
					<td class="<?php echo $class; ?>"><a href="<?php echo $baseurl; ?>/?tab=game&id=<?php echo $game->id; ?>&lid=1"><?php echo $game->GameTitle; ?></a></td>
					<td class="<?php echo $class; ?>"><img src="images/common/consoles/png16/<?php echo $platformIdResult->icon; ?>" alt="<?php echo $platformIdResult->name; ?>" style="vertical-align: middle;" /> <?php echo $platformIdResult->name; ?></td>
					<td class="<?php echo $class; ?>">
						<?php if(!empty($game->Genre))
						{
							$mainGenre = explode("|", $game->Genre);
							if(!empty($stringGenres))
							{
								for($i = 0; $i <= count($mainGenre); $i++)
								{
									if($mainGenre[$i] == $stringGenres)
									{
										if(strlen($mainGenre[$i]) > 15)
										{
											$mainGenre[$i] = substr($mainGenre[$i], 0, 15) . "...";
										}
										echo $mainGenre[$i];
									}
								}
							}
							else
							{
								if(strlen($mainGenre[1]) > 15)
								{
									$mainGenre[1] = substr($mainGenre[1], 0, 15) . "...";
								}
								echo $mainGenre[1];
							}
						}
						?>
					</td>
					<td class="<?php echo $class; ?>"><?php echo $game->Rating; ?></td>
					<td align="center" class="<?php echo $class; ?>"><?php if($boxartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
					<td align="center" class="<?php echo $class; ?>"><?php if($fanartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
					<td align="center" class="<?php echo $class; ?>"><?php if($bannerResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
				</tr>
				</tr>
				<?php
				$gamecount++;
			}

			## No matches found?
			if ($total_pages == 0)  {
				print "<tr><td class=\"odd\" colspan=\"8\" align=\"center\" style=\"font-weight: bold;\">The game you searched for has not been added yet, would you like to <a href=\"?tab=addgame&passTitle=$string\">create it?</a>";
				//if (!$alllang){print "Retry <a href=\"$baseurl/index.php?".$_SERVER["QUERY_STRING"]."&alllang=1\">search</a> in all languages?";}
				print "</td></tr>\n";
				
			}
			else
			{
				?>
					<tr>
						<td class="total" colspan="8">Total Matching Search: <?=$total_pages?> Games</td>
					</tr>
				<?
			}
		?>
		</table>

		<!-- Start Show Pagination -->
		<?=$pagination?>
		<!-- End Show Pagination -->