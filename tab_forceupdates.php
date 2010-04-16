<div class="section">
<h1>Force updates requested or approved by admins</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<td class="head">Series ID</td>
			<td class="head">Series Name</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT * FROM tvseries WHERE forceupdate=2 ORDER BY SeriesName";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->id</a></td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->SeriesName</a></td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=2>No series found</td></tr>\n";
			}
		?>
	</table>
</div>


<div class="section">
<h1>Force updates requested by users and not yet approved by admins</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<td class="head">Series ID</td>
			<td class="head">Series Name</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT * FROM tvseries WHERE forceupdate=1 ORDER BY SeriesName";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->id</a></td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->SeriesName</a></td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=2>No series found</td></tr>\n";
			}
		?>
	</table>
</div>

