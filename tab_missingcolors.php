<div class="section">
<h1>Top 50 Banner Artists</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Artist Name</td>
			<td class="head">Missing Fan Art</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT users.id, users.username, COUNT(*) AS fanart FROM users LEFT JOIN banners ON userid=users.id WHERE artistcolors IS NULL AND keytype='fanart' GROUP BY users.id,users.username ORDER BY fanart DESC";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\"><a href=\"/index.php?tab=recentbanners&bannertype=fanart&banneruser=$db->id&artistcolorsmissing=Moo\">$db->username</a></td><td class=\"$class\">$db->fanart</td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=2>No banner artists found</td></tr>\n";
			}
		?>
	</table>
</div>
