<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr>
	<!-- Left column -->
	<td valign="top" width="100%">
	<div class="section">
	<h1>Welcome to The Television Database</h1>
	<p>This website will serve as a frontend to a complete database of television shows.  The site includes series and season banners that can be incorporated into various HTPC software and plugins.</p>
	<p><b>Facts about the Online TV Show Database:</b>
	<ul>
		<li>The database is currently being used by the <a href="http://mytv.senseitweb.com/r.ashx?1" target="_blank">myTV add-in</a> for Windows Media Center, Spiff's scraper for <a href="http://www.xboxmediacenter.com/" target="_blank">XBox Media Center</a> (SVN rev 8044); the <a href="http://meedio.com/forum/about25041.html">meeTVshows</a> and <a href="http://www.meedios.com/forum/viewtopic.php?t=1380">TVNight</a> plugins for <a href="http://www.meedios.com/">MeediOS</a> and <a href="http://meedio.com/">Meedio</a>; the <a href="http://forum.team-mediaportal.com/my_tvseries-f162.html">MP-TVSeries plugin</a> for <a href="http://www.team-mediaportal.com/">MediaPortal</a>; and a handful of smaller projects.
		<li>The database schema and website are open source under the GPL, and are available at <a href="http://sourceforge.net/projects/tvdb/">Sourceforge</a>.
		<li>I'm happy to make changes to the tv database's interfaces and data in order to accommodate programmers and users.
		<li>Software developers are welcome to use our <a href="/wiki/index.php?title=Programmers_API" target="_blank">XML interface</a>.
	</ul></p>
	<p>Special thanks goes out to Josh Walter (walts81) for the original site; Richard Skarsten (t0ffluss) for the original banner site; Paul Taylor (polargeek) for considerable code contributions; Maelstrom Technology Solutions for hosting the forums and previously hosting the site; Coco for site administration help and code contributions; Crazzy Kid, Arne @ senseIT, and Zyran for generously providing a dedicated server and technical assistance for the site. Without the contributions of these people, as well as the ongoing work of the contributors and banner artists, this site wouldn't exist.
	</div>


		<?php	if ($loggedin == 1)  {  ?>
		<div class="section">
		<a href="./?tab=addgame"><h1>Add A Game</h1></a>
	<div id="red"><?=$errormessage?></div>
		</div>
		<?php	}  ?>

	</td>


	<!-- Right column -->
	<td valign="top">
		<div class="blanksection">
			<a href="/index.php?tab=donation"><img src='images/donate.png' alt='Donate' border='0' width="344" height="89"></a>
		</div>
		<?php	if ($adminuserlevel == 'ADMINISTRATOR')  { 	?>
		<div class="section">
		<h1>Administrator Options</h1>
			<a href="/index.php?tab=userlist">User Editor</a>
		<?php	if ($_SESSION['userlevel'] == 'SUPERADMIN')  { 	?>
			&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="/index.php?tab=userlist&function=admin">Admin User Editor</a>
		<?php } ?>
		&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="/index.php?tab=locked">Lock Report</a>
		<br>&nbsp;<br><a href="/index.php?tab=yearlist">Year List</a>

		</div>
		<?php 
		} 

		## If the user has favorites, display them
			if ($user->favorites)  {
		?>
			<div class="section">
			<h1>Your Favorites</h1>
				<?php	## Display banners

				## Prepare the favorites query
				$userfavorites = explode(",", $user->favorites);
				foreach ($userfavorites AS $key => $favorite)  {
					if ($favorite)  {
						$userfavorites[$key] = "tvseries.id=$favorite";
					}
					else  {
						unset($userfavorites[$key]);
					}
				}
				$favoritesquery = implode(" OR ", $userfavorites);

				$query	= "SELECT id, seriesname FROM games WHERE $favoritesquery ORDER BY SeriesName";
				$result = mysql_query($query);
				while ($db = mysql_fetch_object($result))  {
					$subquery = "SELECT *, (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS rating from banners WHERE keytype='series' AND keyvalue=$db->id ORDER BY rating DESC, RAND()";
					$subresult = mysql_query($subquery);
					$banner = mysql_fetch_object($subresult);

					if ($banner->filename && $user->favorites_displaymode=="banners") {
						displaybanner($banner->filename, $banner->user, 0, $fullurl, $banner->id, 0, "/index.php?tab=series&id=$db->id");
					}
					else {
						ECHO "<br><a href='$baseurl/index.php?tab=series&id=$db->id'>$db->seriesname</a>";
					}
				}
            }
				?>
			</div>
	</td>
</tr>
</table>
