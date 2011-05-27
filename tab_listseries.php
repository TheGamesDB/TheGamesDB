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
	<div style="width: 90%; margin: auto; margin-bottom: 12px;">
		<a href="javascript: void();" onclick="$('#advancedSearchPanel').slideToggle(); if($('#chevron').attr('src') == 'images/common/icons/expand_16.png') { $('#chevron').attr('src', 'images/common/icons/collapse_16.png'); } else { $('#chevron').attr('src', 'images/common/icons/expand_16.png'); }" style="text-decoration: none; outline: 0px; color: #0066FF;">Advanced Search <img id="chevron" src="images/common/icons/expand_16.png" alt="Expand/Collapse" style="vertical-align:middle;" /></a>
		<div id="advancedSearchPanel" style="display: none; border: 1px solid #0066FF; background-color: #E5FAFF; padding: 15px;">
			<form>
				<table cellspacing="6" width="100%">
					<tr>
						<td>Search:<input type="text" name="string" value="<?php echo $string; ?>" size="20" /></td>
						<td>Platform:
							<select name="stringPlatform">
								<option value="">Select...</option>
								<?php
									$platformQuery = mysql_query(" SELECT * FROM platforms ");
									while($platformResult = mysql_fetch_assoc($platformQuery))
									{
										?>
											<option<?php if($stringPlatform == $platformResult['name']) { echo " selected"; } ?> value="<?php echo $platformResult['name']; ?>"><?php echo $platformResult['name']; ?></option>
										<?php
									}
								?>
							</select>
						</td>
						<td>Rating:
							<select name="stringRating">
								<option value="">Select...</option>
								<option<?php if($stringRating == "eC - Early Childhood") { echo " selected"; } ?>>EC - Early Childhood</option>
                                <option<?php if($stringRating == "E - Everyone") { echo " selected"; } ?>>E - Everyone</option>
                                <option<?php if($stringRating == "E10+ - Everyone 10+") { echo " selected"; } ?>>E10+ - Everyone 10+</option>
                                <option<?php if($stringRating == "T - Teen") { echo " selected"; } ?>>T - Teen</option>
                                <option<?php if($stringRating == "M - Mature") { echo " selected"; } ?>>M - Mature</option>
                                <option<?php if($stringRating == "RP - Rating Pending") { echo " selected"; } ?>>RP - Pating Pending</option>
							</select>
						</td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td>Genre:
							<select name="stringGenres" disabled>
								<option value="">Select...</option>
								<?php
									$genresQuery = mysql_query(" SELECT * FROM genres ");
									while($genresResult = mysql_fetch_assoc($genresQuery))
									{
										?>
											<option<?php if($stringGenres == $genresResult['name']) { echo " selected"; } ?> value="<?php echo $genresResult['name']; ?>"><?php echo $genresResult['name']; ?></option>
										<?php
									}
								?>
							</select>
						</td>
						<td></td>
						<td>
							<!-- <input type="hidden" name="searchseriesid" id="searchseriesid" /> -->
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
	
	<table width="100%" border="0" cellspacing="1" cellpadding="7" id="listtable">
		<tr>
			<td class="head arcade" align="center">ID</td>
			<td class="head arcade">Game Title</td>
			<td class="head arcade">Platform</td>
			<td class="head arcade">Genre</td>
			<td class="head arcade">ESRB</td>
			<td class="head arcade">Boxart</td>
			<td class="head arcade">Fanart</td>
		</tr>

		<?php	## Run the games query
			$gamecount = 0;
			$string = mysql_real_escape_string($string);
			$letter = mysql_real_escape_string($letter);			

			if ($function == 'Search')  {
				$query = "SELECT * FROM games WHERE GameTitle LIKE '%$string%' ORDER BY GameTitle";
			}
			## Start Advanced Search Query
			elseif ($function == 'Advanced Search')  {
				$query = "SELECT * FROM games WHERE GameTitle LIKE '%$string%'";
				if($stringPlatform != "")
				{
					$query = $query .  " AND platform = '$stringPlatform' ";
				}
				if($stringRating != "")
				{
					$query = $query .  " AND Rating = '$stringRating' ";
				}
				if($stringGenres != "")
				{
					$query = $query .  " AND Genre LIKE '%$stringGenres%' ";
				}
				$query = $query .  "ORDER BY GameTitle";
			}
			## End Advanced Search Query
			elseif ($function == 'OverviewSearch')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesoverview.languageid) As language FROM translation_seriesoverview WHERE translation LIKE '%$string%' ORDER BY ID";
			}
			else if ($letter == 'OTHER')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) NOT BETWEEN 'A' AND 'Z' $languagelimit ORDER BY translation";
			}
			else  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) = '$letter' $languagelimit ORDER BY translation";
			}
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Display each game
			while ($game = mysql_fetch_object($result)) {
				$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
				$boxartResult = mysql_num_rows($boxartQuery);
				
				$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
				$fanartResult = mysql_num_rows($fanartQuery);
				
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				?>
				<tr>
					<td align="center" class="<?php echo $class; ?>"><?php echo $game->id; ?></td>
					<td class="<?php echo $class; ?>"><a href="<?php echo $baseurl; ?>/?tab=game&id=<?php echo $game->id; ?>&lid=1"><?php echo $game->GameTitle; ?></a></td>
					<td class="<?php echo $class; ?>"><?php echo $game->Platform; ?></td>
					<td class="<?php echo $class; ?>"><?php echo $game->Genre; ?></td>
					<td class="<?php echo $class; ?>"><?php echo $game->Rating; ?></td>
					<td align="center" class="<?php echo $class; ?>"><?php if($boxartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
					<td align="center" class="<?php echo $class; ?>"><?php if($fanartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
				</tr>
				<?php
				$gamecount++;
			}

			## No matches found?
			if ($gamecount == 0)  {
				print "<tr><td class=\"odd\" colspan=\"7\" align=\"center\" style=\"font-weight: bold;\">No Games Found. Have you tried widening your search parameters?";
				//if (!$alllang){print "Retry <a href=\"$baseurl/index.php?".$_SERVER["QUERY_STRING"]."&alllang=1\">search</a> in all languages?";}
				print "</td></tr>\n";
				
			}
		?>
		</table>


