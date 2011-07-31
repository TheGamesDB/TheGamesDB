<div style="text-align: center;">
	<h1 class="arcade">Site Reports &amp; Statistics:</h1>	

<!-- Start Pagination -->
<?php
function incPagination($query, $limit, $statstype, $sortBy, $page)
{
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
			$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$prev\">&laquo; prev</a>";
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
					$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
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
						$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$lastpage\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=1\">1</a>";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$lastpage\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=1\">1</a>";
				$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$counter\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"?tab=adminstats&statstype=$statstype&sortBy=$sortBy&limit=$limit&page=$next\">next &raquo;</a>";
		else
			$pagination.= "<span class=\"disabled\">next &raquo;</span>";
		$pagination.= "</div>";		
	}
	
	return array("pagination" => $pagination, "query" => $query, "total" => $total_pages, "limit" => $limit);
}
?>
<!-- End Pagination -->
	
<?php

if(isset($_GET['statstype'])) {
	
	$StatsType=$_GET['statstype'];
	
	// Start Admin Only Stats & Reports
	if ($adminuserlevel == 'ADMINISTRATOR') {
?>
		<?php
			switch ($StatsType)
			{
			case "locked":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Locked Games</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
						<th style="background-color: #333; color: #FFF;">Platform</th>
						<th style="background-color: #333; color: #FFF;">Locked By</th>
					</tr>
				<?php
				$lockedcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle, platforms.name, games.lockedby, users.username FROM games, platforms, users WHERE games.Platform = platforms.id AND locked = 'yes' AND games.lockedby = users.id ORDER BY games.GameTitle ASC");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
						<td><?php echo $row[name]; ?></td>
						<td><?php echo $row[username]; ?></td>
					</tr>
					<?php
					$lockedcount++;
				}
				?>
					<tr>
						<td colspan="4" style="background-color: #EEE; font-weight: bold;">Total Locked Games: <?php echo $lockedcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "missingplatform":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Platform Data</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE games.Platform IS NULL ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Platform Data: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "multipleplatform":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games With Multiple Platforms</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
						<th style="background-color: #333; color: #FFF;">Platforms</th>
					</tr>
				<?php
				$multiplecount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE games.Platform = platforms.id AND games.Platform LIKE '|%|%|' ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
						<td><?php echo $row[name]; ?></td>
					</tr>
					<?php
					$multiplecount++;
				}
				?>
					<tr>
						<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games With Multiple Platforms: <?php echo $multiplecount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			
			case "morefront":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games With 2 or More Front Boxart</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
						<th style="background-color: #333; color: #FFF;">Platform</th>
					</tr>
				<?php
				$morecount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE (SELECT COUNT(filename) FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') > 1 AND games.Platform = platforms.id ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
						<td><?php echo $row[name]; ?></td>
					</tr>
					<?php
					$morecount++;
				}
				?>
					<tr>
						<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games With 2 or More Front Boxart: <?php echo $morecount; ?></td>
					</tr>
				</table>
				<?php
				break;
			} 
		}
		// End Admin Only Stats & Reports
			switch ($StatsType)
			{
				case "missingoverview":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE games.Platform = platforms.id AND games.Overview IS NULL ";
						
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
					
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>

					<h2 class="arcade" style="color: #FF4F00;">Games Missing Overview</h2>
					
					<!-- Start Sort By -->
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Overview: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
			
				case "missinggenre":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE games.Platform = platforms.id AND games.Genre IS NULL ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
					
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Genre Data</h2>
					<!-- Start Sort By -->
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Genre Data: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
					
					case "missingfront":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
						?>
						<h2 class="arcade" style="color: #FF4F00;">Games Missing Front Boxart</h2>
						<!-- Start Sort By -->
						<form style="text-align: right;">
							<input type="hidden" name="tab" value="<?=$tab?>" />
							<input type="hidden" name="statstype" value="<?=$statstype?>" />
							<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
								<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
								<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
						<?=$pagination?>
						<table align="center" border="1" cellspacing="0" cellpadding="7">
							<tr>
								<th style="background-color: #333; color: #FFF;">Game ID</th>
								<th style="background-color: #333; color: #FFF;">Game Title</th>
								<th style="background-color: #333; color: #FFF;">Platform</th>
							</tr>
						<?php
						$missingcount = 0;
						$result = mysql_query($query);
						while($row = mysql_fetch_assoc($result)) {
							?>
							<tr>
								<td><?php echo $row[id]; ?></td>
								<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
								<td><?php echo $row[name]; ?></td>
							</tr>
							<?php
							$missingcount++;
						}
						?>
							<tr>
								<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Front Boxart: <?php echo $total; ?></td>
							</tr>
						</table>
						<?=$pagination?>
						<?php
						break;
			
				case "missingback":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%back%') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Back Boxart</h2>
					<!-- Start Sort By -->
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Back Boxart: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
				
				case "missingfanart":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'fanart') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Fanart</h2>
					<!-- Start Sort By -->
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Fanart: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
				
				case "missingbanner":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'series') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Banners</h2>
					<!-- Start Sort By -->
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Banners: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
					
					case "missingscreenshot":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'screenshot') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Screenshots</h2>
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Screenshots: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
					
					case "missingyoutube":
						$query = " SELECT games.id, games.GameTitle, platforms.name FROM games, platforms WHERE (games.Youtube IS NULL OR games.Youtube = '') AND games.Platform = platforms.id ";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, games.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY games.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Games Missing Youtube Trailers</h2>
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "games.GameTitle"){ echo "selected"; } ?> value="games.GameTitle">Name</option>
							<option <?php if($sortBy == "platforms.name"){ echo "selected"; } ?> value="platforms.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Youtube Trailers: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
					
					case "topratedgames":
						$query = "SELECT g.GameTitle, p.name, g.id, AVG(r.rating) as toprating FROM games AS g, platforms AS p, ratings AS r WHERE r.itemid = g.id AND g.platform = p.id GROUP BY g.GameTitle HAVING AVG(r.rating) > 8";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, g.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY g.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Top Rated Games</h2>
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "g.GameTitle"){ echo "selected"; } ?> value="g.GameTitle">Name</option>
							<option <?php if($sortBy == "p.name"){ echo "selected"; } ?> value="p.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
							<th style="background-color: #333; color: #FFF;">Avg Rating</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
							<td><?php echo $row[toprating]; ?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="4" style="background-color: #EEE; font-weight: bold;" >Total Top Rated Games: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
					
					case "topratedfanart":
						include('simpleimage.php');
						function imageResize($filename, $cleanFilename, $target)
						{
							if(!file_exists($cleanFilename))
							{
								$dims = getimagesize($filename);
								$width = $dims[0];
								$height = $dims[1];
								//takes the larger size of the width and height and applies the formula accordingly...this is so this script will work dynamically with any size image
								if ($width > $height)
								{
									$percentage = ($target / $width);
								}
								else
								{
									$percentage = ($target / $height);
								}
								
								//gets the new value and applies the percentage, then rounds the value
								$width = round($width * $percentage);
								$height = round($height * $percentage); 
								
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($width, $height);
								$image->save($cleanFilename);
								$image = null;
							}
							//returns the new sizes in html image tag format...this is so you can plug this function inside an image tag and just get the
							return "src=\"$cleanFilename\"";
						}
						$query = "SELECT g.GameTitle, p.name, g.id, b.filename, AVG(r.rating) as toprating FROM games AS g, banners AS b, platforms AS p, ratings AS r WHERE r.itemid = b.id AND g.id = b.keyvalue AND r.itemtype = 'banner' AND b.keytype = 'fanart' AND g.platform = p.id GROUP BY g.GameTitle, p.name, g.id, b.filename HAVING AVG(r.rating) = 10";
						if(!empty($sortBy))
						{
							$query = $query . " ORDER BY $sortBy, g.GameTitle ASC";
						}
						else
						{
							$query = $query . " ORDER BY g.GameTitle ASC";
						}
						
						$pagResult = incPagination($query, $limit, $statstype, $sortBy, $page);
						$pagination = $pagResult["pagination"];
						$query = $pagResult["query"];
						$total = $pagResult["total"];
						$limit = $pagResult["limit"];
					?>
					<h2 class="arcade" style="color: #FF4F00;">Top Rated Fanart</h2>
					<form style="text-align: right;">
						<input type="hidden" name="tab" value="<?=$tab?>" />
						<input type="hidden" name="statstype" value="<?=$statstype?>" />
						<p style="font-weight: bold;">Sort By: <select name="sortBy" onchange="this.form.submit();">
							<option <?php if($sortBy == "g.GameTitle"){ echo "selected"; } ?> value="g.GameTitle">Name</option>
							<option <?php if($sortBy == "p.name"){ echo "selected"; } ?> value="p.name">Platform</option>
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
					<?=$pagination?>
					<table align="center" border="1" cellspacing="0" cellpadding="7">
						<tr>
							<th style="background-color: #333; color: #FFF;">Game ID</th>
							<th style="background-color: #333; color: #FFF;">Game Title</th>
							<th style="background-color: #333; color: #FFF;">Platform</th>
							<th style="background-color: #333; color: #FFF;">Avg Rating</th>
							<th style="background-color: #333; color: #FFF;">Fanart</th>
						</tr>
					<?php
					$missingcount = 0;
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td><?php echo $row[id]; ?></td>
							<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
							<td><?php echo $row[name]; ?></td>
							<td><?php echo $row[toprating]; ?></td>
							<td><?php imageResize("banners/" . $row[filename], "banners/_statscache/" . $row[filename], 150); echo "<a href=\"banners/$row[filename]\" target=\"_blank\"><img src=\"banners/_statscache/$row[filename]\" alt=\"$row[GameTitle] Fanart\" /></a>";?></td>
						</tr>
						<?php
						$missingcount++;
					}
					?>
						<tr>
							<td colspan="5" style="background-color: #EEE; font-weight: bold;" >Total Top Rated Fanart: <?php echo $total; ?></td>
						</tr>
					</table>
					<?=$pagination?>
					<?php
					break;
			}
		}
	
?>
</div>
<!-- End Admin Only Stats & Reports -->