<?php
	if ($adminuserlevel == 'ADMINISTRATOR')  { 

//This checks to see if there is a page number. If not, it will set it to page 1 
if (!(isset($pagenum))) 
  { 
	$pagenum = 1; 
  } 

//Here we count the number of results 
//Edit $data to be your query 
			if ($type == 'episode')  {
				$query = "SELECT * FROM tvepisodes WHERE locked='yes' ORDER BY id";
			}
			else if ($type == 'season')  {
				$query = "SELECT * FROM tvseasons WHERE locked='yes' ORDER BY id";
			}
			else  {
				$query = "SELECT * FROM tvseries WHERE locked='yes' ORDER BY SeriesName";
				$type = 'series';
			}

	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$rows = mysql_num_rows($result); 

//This is the number of results displayed per page 
	$page_rows = 30; 
//This tells us the page number of our last page 
	$last = ceil($rows/$page_rows);
//this makes sure the page number isn't below one, or more than our maximum pages 
	if ($pagenum < 1) 
		{ 
		$pagenum = 1; 
		} 
	elseif ($pagenum > $last) 
		{ 
		$pagenum = $last; 
	} 
//This sets range that we will display in our query 
	$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;
?>
<div class="section">
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" style="font-size: 15px;">
		<tr>
			<td align="center" width="8%">&nbsp;</td>
			<td align="center">
				<div class="titlesection">
				<a href="/?tab=locked"><b>Series Lock Report</b></a>
				</div>
			</td>
			<td align="center" width="5%">&nbsp;</td>
			<td align="center">
				<div class="titlesection">
				<a href="/?tab=locked&type=season"><b>Season Lock Report</b></a>
				</div>
			</td>
			<td align="center" width="5%">&nbsp;</td>
			<td align="center">
				<div class="titlesection">
				<a href="/?tab=locked&type=episode"><b>Episode Lock Report</b></a>
				</div>
			</td>
			<td align="center" width="8%">&nbsp;</td>
		</tr>
	</table>
<h1>Locked Report | <?=$type?></h1>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">

<?php
			if ($type == 'episode')  {
				$query = "SELECT * FROM tvepisodes WHERE locked='yes' ORDER BY id $max";
				print'<tr>
					<td class="head">Series Name</td>
					<td class="head">Season Number</td>
					<td class="head">Episode Name</td>
					<td class="head">Last Updated</td>
				</tr>';
				$result = mysql_query($query) or $error = 'No Episodes Are Locked';
			}
			else if ($type == 'season')  {
				$query = "SELECT * FROM tvseasons WHERE locked='yes' ORDER BY id $max";
				print'<tr>
					<td class="head">Series Name</td>
					<td class="head">Season Number</td>
				</tr>';
				$result = mysql_query($query) or $error = 'No Seasons Are Locked';
			}
			else  {
				$query = "SELECT * FROM tvseries WHERE locked='yes' ORDER BY SeriesName $max";
				print'<tr>
					<td class="head">ID</td>
					<td class="head">Series Name</td>
					<td class="head">Last Updated</td>
                                        <td class="head">Disabled</td>
				</tr>';
				$result = mysql_query($query) or $error = 'No Series Are Locked';
			}

		while ($list = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
			if ($type == 'episode')  {
				$query2 = "SELECT * FROM tvseasons WHERE id=$list->seasonid"; 
				$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				while ($seasons = mysql_fetch_object($result2))  { 
  				  $seriesid =  $seasons->seriesid;
				  $seasonnumber = $seasons->season;
					$query3 = "SELECT * FROM tvseries WHERE id=$seriesid"; 
					$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					while ($series = mysql_fetch_object($result3))  { 
						$Seriesname =  $series->SeriesName;
					}	
				}	
				$lastupdated = date("r", $list->lastupdated);
				print "<tr><td class=\"$class\"><a href=\"/?tab=series&amp;id=$seriesid\">$Seriesname</a></td><td class=\"$class\"><a href=\"/?tab=season&seriesid=$seriesid&seasonid=$list->seasonid\">$seasonnumber</a></td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$list->seasonid&id=$list->id\">$list->EpisodeName</a></td><td class=\"$class\">$lastupdated</td></tr>\n";
			}
			else if ($type == 'season')  {
				$query2 = "SELECT * FROM tvseries WHERE id=$list->seriesid"; 
				$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				while ($series = mysql_fetch_object($result2))  { 
					$Seriesname =  $series->SeriesName;
				}	
				print "<tr><td class=\"$class\"><a href=\"/?tab=series&amp;id=$list->seriesid\">$Seriesname</a></td><td class=\"$class\"><a href=\"/?tab=season&seriesid=$list->seriesid&seasonid=$list->id\">$list->season</a></td></tr>\n";
			}
			else  {
				$lastupdated = date("r", $list->lastupdated);
				print "<tr><td class=\"$class\">$list->id</td><td width=\"400\" class=\"$class\"><a href=\"/?tab=series&amp;id=$list->id\">$list->SeriesName</a></td><td class=\"$class\">$lastupdated</td><td class=\"$class\">";
                                if ($list->disabled) {
					print "<img src=\"/images/checkmark.png\" width=10 height=10>";
				}
                                print "</td></tr>\n";
			}
		}
	if ($error) { 
		print "<tr><td>$error</td></tr>";
	} 
?>
		</table>
</div>
<?php
echo "<div class=\"section\">";
echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">';
echo '<tr><td>';
// This shows the user what page they are on, and the total number of pages
echo " --Page $pagenum of $last-- </td>";  

echo "<td align='right'>";

// First we check if we are on page one. If we are then we don't need a link to the previous page or the first page so we do nothing. If we aren't then we generate links to the first page, and to the previous page.
if ($pagenum == 1) 
	{} 
else {
	echo " <a href='$fullurl&pagenum=1'> <<-First</a> ";
	echo " ";
	$previous = $pagenum-1;
	echo " <a href='$fullurl&pagenum=$previous'> <-Previous</a> ";
	} 

	//just a spacer
	echo " ---- ";
//This does the same as above, only checking if we are on the last page, and then generating the Next and Last links
if ($pagenum == $last) 
	{} 
else {
	$next = $pagenum+1;
	echo " <a href='$fullurl&pagenum=$next'>Next -></a> ";
	echo " ";
	echo " <a href='$fullurl&pagenum=$last'>Last ->></a> ";
	} 

echo "</td></tr></table>";
	} //
	else  {
?>
		<div class="section">
		<h1>Administrators Only</h1>
		</div>
<?php
	}
?>
