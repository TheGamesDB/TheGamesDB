<div class="section">
<h1>Missing Series Banners (Top 25)</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Requests</td>
			<td class="head">Show Title</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT * FROM games WHERE bannerrequest>0 ORDER BY bannerrequest DESC LIMIT 25";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\">$db->bannerrequest</td><td class=\"$class\"><a href=\"/?tab=series&amp;id=$db->id\">$db->SeriesName</a></td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=2>No Series Banner Requests Found</td></tr>\n";
			}
		?>
	</table>
</div>


<div class="section">
<h1>Missing Season Banners (Top 25)</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Requests</td>
			<td class="head">Show Title</td>
			<td class="head">Season Number</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT *, (SELECT SeriesName FROM games WHERE id=tvseasons.seriesid) AS SeriesName FROM tvseasons WHERE bannerrequest>0 AND season!=0 ORDER BY bannerrequest DESC LIMIT 25";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\">$db->bannerrequest</td><td class=\"$class\"><a href=\"/?tab=series&amp;id=$db->seriesid\">$db->SeriesName</a></td><td class=\"$class\">$db->season</td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=3>No Season Banner Requests Found</td></tr>\n";
			}
		?>
	</table>
</div>

