<?php
	if ($adminuserlevel == 'ADMINISTRATOR')  { 

	if (!$string) {
		
		Echo "<div class='section'>
		<h1>Series List |</h1>
		<font size=2>Search for the year you want below</font></div>";		
	}
	else {
		
//This checks to see if there is a page number. If not, it will set it to page 1 
if (!(isset($pagenum))) 
  { 
	$pagenum = 1; 
  } 

//Here we count the number of results 
//Edit $data to be your query 

	$query = "SELECT * FROM tvseries WHERE FirstAired LIKE '%$string%'";
	$result = mysql_query($query) or die('Query failed: Counter ' . mysql_error());
	$rows = mysql_num_rows($result); 
   if ($rows != 0 ) {
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
   }
?>


<div class="section">
<h1>Series List | <?=$string?></h1>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Series ID</td>
			<td class="head">Series Name</td>
			<td class="head">Genre</td>
			<td class="head">Status</td>
		</tr>

<?php
   if ($rows == 0 ) {
	print "<tr><td>&nbsp;</td><td>No Users With That Name</td><td colspan=3>&nbsp;</td></tr>\n";
   }
   else {

	$query = "SELECT * FROM tvseries WHERE FirstAired LIKE '%$string%' $max ";
	$result = mysql_query($query) or die('Query failed: List ' . mysql_error());

			while ($serieslist = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\">$serieslist->id</td><td class=\"$class\"><a href=\"/index.php?tab=series&amp;id=$serieslist->id\">$serieslist->SeriesName</a></td><td class=\"$class\">$serieslist->Genre</a></td><td class=\"$class\">$serieslist->Status</td></tr>\n";
				$currentlang = "";
			}
   }
?>
		</table>
</div>
<?php
	}
echo "<div class=\"section\">";
echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">';
echo '<tr><td width="33%">';
// This shows the user what page they are on, and the total number of pages
echo " --Page $pagenum of $last-- </td>";  ?>
	<td style="background-image: url(/images/search.png); background-repeat:no-repeat;">
	<form method="get" id="searchbox" target="_top">
		<input type="hidden" name="tab" value="yearlist">
		<input type="text" name="string" id="yearsearch" value="Year Search" onFocus="this.value=''" style="margin-left:25px;">
	</form>
	</td>
<?php echo "<td align='right'>";

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
