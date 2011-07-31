<?php	## Handle searches differently
	if ($_SESSION['userid'] && !$alllang){
		$languagelimit = "AND languageid = (SELECT languageid FROM users WHERE id = ".$_SESSION['userid'].")";
		$query = "SELECT languages.name FROM users INNER JOIN languages ON users.languageid = languages.id WHERE users.id=".$_SESSION['userid'];
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
?>
	
	<!-- Start Browse By Platform -->
	<div style="width: 550px; margin: auto; margin-bottom: 12px;">
		<div id="platformsPanel" style="border: 1px solid #000; background-color: #555555; padding: 15px; color: #FFFFFF;">
			<div style="width:450px; margin: auto;">
			<h1 class="arcade" style="text-align: center;"><span style="color: #000;">Browse</span> <span style="color: #EF5F00;">Platforms</span></h1>
				<form id="platformBrowseForm" action="<?= $baseurl ?>/index.php" onsubmit="if($('#platformMenu').val() != 'select') { return true; } else { alert('Please Select a Platform...'); return false; }">
					<select name="stringPlatform" id="platformMenu" onchange="showValue(this.value); alert(this.value); if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); }" style="color: #333;">
						<option value="select" title="images/common/icons/question-block_48.png">Please Select Platform...</option>
						<?php
									$platformQuery = mysql_query(" SELECT * FROM platforms ORDER BY name ASC");
									while($platformResult = mysql_fetch_assoc($platformQuery))
									{
										?>
											<option value="<?php echo $platformResult['id']; ?>" title="images/common/consoles/png48/<?php echo $platformResult['icon'];?>"<?php if($stringPlatform == $platformResult['id']) {echo " selected";}?>><?php echo $platformResult['name']; ?></option>
										<?php
									}
								?>
					</select>
					<input type="hidden" name="tab" value="listplatform" />
					<input type="hidden" name="function" value="Browse By Platform" />
					<!-- <a class="arcade" href="javascript: void();" onclick="if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); } else { alert('Please select a console...'); }" style=" font-size: 38px; color: #00FF00; float: left; padding-left: 17px; padding-top: 10px;">Go!</a> -->
					<button type="submit" style="cursor: pointer; height: 46px; padding: 5px; margin-left: 10px; margin-top: 5px; float: left; background: url(images/common/bg_button-black.png) center center repeat-x; border-radius: 10px;"><span class="arcade" style=" font-size: 30px; color: #00FF00;">Go</span></button>
				</form>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<!-- End Browse By Platform -->
	
	<?php
		$gamecount = 0;
		$string = mysql_real_escape_string($string);
		$letter = mysql_real_escape_string($letter);			
		
		if ($function == 'Browse By Platform')  {
			$query = "SELECT * FROM games  as g WHERE Platform = '$stringPlatform'";
			if(!empty($sortBy))
			{
				$query .= " ORDER BY $sortBy, GameTitle ASC";
			}
			else
			{
				$query .= " ORDER BY GameTitle";
			}
		}
	?>
	
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
				$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$prev\">&laquo; prev</a>";
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
						$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$counter\">$counter</a>";					
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
							$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$counter\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$lastpage\">$lastpage</a>";		
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=1\">1</a>";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$counter\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$lastpage\">$lastpage</a>";		
				}
				//close to end; only hide early pages
				else
				{
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=1\">1</a>";
					$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$counter\">$counter</a>";					
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= "<a href=\"?tab=listplatform&stringPlatform=$stringPlatform&sortBy=$sortBy&function=$function&limit=$limit&page=$next\">next &raquo;</a>";
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

		<?php	## Run the games query
			
			if ($function == 'Browse By Platform')  {
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());

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
						<td class="<?php echo $class; ?>"><?php if(!empty($game->Genre)) { $mainGenre = explode("|", $game->Genre); if(strlen($mainGenre[1]) > 15) { $mainGenre[1] = substr($mainGenre[1], 0, 15) . "..."; }echo $mainGenre[1]; } ?></td>
						<td class="<?php echo $class; ?>"><?php echo $game->Rating; ?></td>
						<td align="center" class="<?php echo $class; ?>"><?php if($boxartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
						<td align="center" class="<?php echo $class; ?>"><?php if($fanartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
						<td align="center" class="<?php echo $class; ?>"><?php if($bannerResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
					</tr>
					<?php
					$gamecount++;
				}
			}

			## No matches found?
			if ($gamecount == 0)  {
				print "<tr><td class=\"odd\" colspan=\"8\" align=\"center\" style=\"font-weight: bold;\">This platform does not have any games yet... Why not <a href=\"?tab=addgame&passPlatform=$stringPlatform\">add one</a>?";
				//if (!$alllang){print "Retry <a href=\"$baseurl/index.php?".$_SERVER["QUERY_STRING"]."&alllang=1\">search</a> in all languages?";}
				print "</td></tr>\n";
				
			}
			else
			{
				?>
					<tr>
						<td class="total" colspan="8">Platform Total: <?=$total_pages?> Games</td>
					</tr>
				<?php
			}
		?>
		</table>

		<!-- Start Show Pagination -->
		<?=$pagination?>
		<!-- End Show Pagination -->

		<script language="javascript">
			$(document).ready(function(e) {
				try {
					$("#platformMenu").msDropDown();
				} 
				catch(e) {
					alert(e.message);
				}
			});
		</script>