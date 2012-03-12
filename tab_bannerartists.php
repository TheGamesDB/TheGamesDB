<div id="gameWrapper">
	<div id="gameHead">

		<h1>Site Reports and Statistics</h1>	
		
		<div class="links" style="text-align: center;">
			<p>&nbsp;</p>
			<h2 style="color: #FF4F00;">Top 50 Art Contributors</h2>
			<p>&nbsp;</p>

			<table align="center" border="1" cellspacing="0" cellpadding="7">
				<tr>
					<th style="background-color: #333; color: #FFF;">User Name</th>
					<th style="background-color: #333; color: #FFF;">Total Art Submitted</th>
					<th style="background-color: #333; color: #FFF;">Ratings</th>
					<th style="background-color: #333; color: #FFF;">Average Rating</th>
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

	</div>
</div>