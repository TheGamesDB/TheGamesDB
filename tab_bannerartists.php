<div style="text-align: center;">
	<h1 class="arcade">Site Reports &amp; Statistics:</h1>	
	<h2 class="arcade" style="color: #FF4F00;">Top 50 Art Contributors</h2>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">User Name</td>
			<td class="head">Total Art Submitted</td>
			<td class="head">Ratings</td>
			<td class="head">Average Rating</td>
		</tr>


		<?php	## Display series
			$count = 0;
			$query = "SELECT id, username, (SELECT COUNT(*) FROM banners WHERE userid=users.id) AS bannercount, (SELECT AVG(ratings.rating) FROM ratings,banners WHERE ratings.itemid=banners.id AND ratings.itemtype='banner' AND banners.userid=users.id) AS rating, (SELECT count(ratings.rating) FROM ratings,banners WHERE ratings.itemid=banners.id AND ratings.itemtype='banner' AND banners.userid=users.id) AS ratingcount FROM users ORDER BY bannercount DESC LIMIT 50";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\"><a href=\"/?tab=artistbanners&amp;id=$db->id\">$db->username</a></td><td class=\"$class\">$db->bannercount</td><td class=\"$class\">$db->ratingcount</td><td class=\"$class\">$db->rating</td></tr>\n";
				$count++;
			}

			## No matches found?
			if ($count == 0)  {
				print "<tr><td class=odd colspan=2>No banner artists found</td></tr>\n";
			}
		?>
	</table>
</div>
