
<div class="section">
<h1>Last 30 series added to the site.</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head"></td>
			<td class="head">Series ID</td>
			<td class="head">First Aired</td>
			<td class="head">Status</td>
			<td class="head">Series Name</td>			
		</tr>

		<?php	## Display series
			$count = 0;
			$query = "SELECT * FROM tvseries ORDER BY id desc limit 30";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				$displaynum = $count + 1;
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\">$displaynum</td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->id</a></td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->FirstAired</a></td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->Status</a></td><td class=\"$class\"><a href=\"/?tab=series&id=$db->id\">$db->SeriesName</a></td></tr>\n";
				$count++;
			}

		?>
	</table>
</div>

