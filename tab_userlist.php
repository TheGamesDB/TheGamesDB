<?php
	if ($adminuserlevel == 'ADMINISTRATOR')  { 

	if ($function == 'Search')  {
		$title = 'Search : ' . $string;
	}
	

//This checks to see if there is a page number. If not, it will set it to page 1 
if (!(isset($pagenum))) 
  { 
	$pagenum = 1; 
  } 

//Here we count the number of results 
//Edit $data to be your query 
			if ($function == 'UserSearch')  {
				$query = "SELECT * FROM users WHERE username LIKE '%$string%' AND userlevel = 'USER' ORDER BY username";
			}
			elseif ($function == 'admin')  {
			  if ($_SESSION['userlevel'] == 'SUPERADMIN')  {
				$query = "SELECT * FROM users WHERE userlevel='ADMINISTRATOR' OR userlevel='SUPERADMIN' ORDER BY username";
				$title = 'Administrators';
			  }
			}
			else  {
				$query = "SELECT * FROM users WHERE  userlevel = 'USER'";
			}

	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
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


<div style="text-align: center;">
	<h1 class="arcade">Site Reports &amp; Statistics:</h1>	
	<h2 class="arcade" style="color: #FF4F00;">User List | <?=$title?></h2>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">User ID</td>
			<td class="head">UserName</td>
			<td class="head">Email</td>
			<td class="head">Language</td>
			<td class="head">Banner Limit</td>
		</tr>

<?php
   if ($rows == 0 ) {
	print "<tr><td>&nbsp;</td><td>No Users With That Name</td><td colspan=3>&nbsp;</td></tr>\n";
   }
   else {

			if ($function == 'UserSearch')  {
				$query = "SELECT * FROM users WHERE username LIKE '%$string%' AND userlevel = 'USER' ORDER BY username $max ";
			}
			elseif ($function == 'admin')  {
			  if ($_SESSION['userlevel'] == 'SUPERADMIN')  {
				$query = "SELECT * FROM users WHERE userlevel='ADMINISTRATOR' OR userlevel='SUPERADMIN' ORDER BY username $max ";
				$title = 'Administrators';
			  }
			}
			else  {
				$query = "SELECT * FROM users WHERE  userlevel = 'USER' $max ";
			}

	$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			while ($userlist = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
						$query2 = "SELECT * FROM languages WHERE id=$userlist->languageid"; 
						$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
						while ($languages = mysql_fetch_object($result2))  { 
						$currentlang =  $languages->name;
						}	
				print "<tr><td class=\"$class\">$userlist->id</td><td class=\"$class\"><a href=\"/?tab=userinfoadmin&amp;id=$userlist->id\">$userlist->username</a></td><td class=\"$class\"><a href='mailto:$userlist->emailaddress'>$userlist->emailaddress</a></td><td class=\"$class\">$currentlang</td><td class=\"$class\">$userlist->bannerlimit</td></tr>\n";
				$currentlang = "";
			}
   }
?>
		</table>
</div>
<?php
//echo "<div class=\"section\">";
echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">';
echo '<tr><td width="33%">';
// This shows the user what page they are on, and the total number of pages
echo " -- Page $pagenum of $last -- </td>";  ?>
	<td style="background-image: url(/images/search.png); background-repeat:no-repeat;">
	<form method="get" id="searchbox" target="_top">
		<input type="text" name="string" id="usersearch" value="Username Search" onFocus="this.value=''" style="margin-left:25px;">
		<input type="hidden" name="tab" value="userlist">
		<input type="hidden" name="function" value="UserSearch">
	</form>
	</td>
<?php echo "<td align='right'>";

// First we check if we are on page one. If we are then we don't need a link to the previous page or the first page so we do nothing. If we aren't then we generate links to the first page, and to the previous page.
if ($pagenum == 1) 
	{} 
else {
	echo " <a href='$baseurl/?tab=userlist&pagenum=1'> <<-First</a> ";
	echo " ";
	$previous = $pagenum-1;
	echo " <a href='$baseurl/?tab=userlist&pagenum=$previous'> <-Previous</a> ";
	} 

	//just a spacer
	echo " ---- ";
//This does the same as above, only checking if we are on the last page, and then generating the Next and Last links
if ($pagenum == $last) 
	{} 
else {
	$next = $pagenum+1;
	echo " <a href='$baseurl/?tab=userlist&pagenum=$next'>Next -></a> ";
	echo " ";
	echo " <a href='$baseurl/?tab=userlist&pagenum=$last'>Last ->></a> ";
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
