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



	<div class="section">
	<h1>New Today</h1>
	<table cellspacing="0" cellpadding="1" border="0" width="100%" id="infotable">
	<tr>
		<td class="head">Series</td>
		<td class="head">Episode</td>
	</tr>
	<?php	## Display shows episodes that are airing today
		$cachedate		= date("l F d, Y");

		## Get the shows from the database
		$itemcount = 0;
		$query = "SELECT id, seriesid, seasonid, (SELECT translation FROM translation_seriesname WHERE seriesid=tvepisodes.seriesid AND languageid=7 LIMIT 1) AS SeriesName, (SELECT translation FROM translation_episodename WHERE episodeid=tvepisodes.id AND languageid=7 LIMIT 1) AS EpisodeName FROM tvepisodes WHERE FirstAired=CURDATE() ORDER BY SeriesName, EpisodeNumber, EpisodeName";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($episode = mysql_fetch_object($result))  {
			if ($episode->EpisodeName) {
				print "<tr><td><a href=\"/index.php?tab=series&id=$episode->seriesid\">$episode->SeriesName</a></td>\n";
				print "<td><a href=\"/index.php?tab=episode&seriesid=$episode->seriesid&seasonid=$episode->seasonid&id=$episode->id\">$episode->EpisodeName</a></td></tr>\n";
				$itemcount++;
			}
		}

		## If no records were found
		if ($itemcount == 0)  {
			print "<tr><td colspan=2>There are no new episodes airing today</td></tr>";
		}

		## Attach the date
	?>
		<tr>
			<td colspan=2 style="border-bottom: none; text-align: right; font-size: 8pt; font-weight: bold; padding-top: 2px"><span style="float: left; width: 14px; padding: 0px; margin: 0px"><a href="/rss/newtoday.php" target="_blank"><img src="/images/rss.gif" width=12 height=12 border=0></a></span> Listings are for <?=$cachedate?></td>
		</tr>
	</table>
	</div>


		<div class="section">
		<h1>Site Stats</h1>
		<?php	## Various quick stats about the site
			$sitestats = array();
			$query = "SHOW TABLE STATUS";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while($db = mysql_fetch_object($result))  {
				$sitestats[$db->Name] = $db->Rows;
			}

			$query = "SELECT COUNT(*) AS Rows, CONCAT('Banner', keytype) AS Name FROM banners GROUP BY keytype";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while($db = mysql_fetch_object($result))  {
				$sitestats[$db->Name] = $db->Rows;
			}
		?>
		<table cellspacing="0" cellpadding="1" border="0" width="100%" id="infotable">
		<tr><td>Series</td><td><?=$sitestats["tvseries"]?></td></tr>
		<tr><td>Seasons</td><td><?=$sitestats["tvseasons"]?></td></tr>
		<tr><td>Episodes</td><td><?=$sitestats["tvepisodes"]?></td></tr>
		<tr><td>Fan Art</td><td><?=$sitestats["Bannerfanart"]?></td></tr>
		<tr><td>Series Banners</td><td><?=$sitestats["Bannerseries"]?></td></tr>
		<tr><td>Season Banners</td><td><?=$sitestats["Bannerseason"]?></td></tr>
		<tr><td>Season Banners (wide)</td><td><?=$sitestats["Bannerseasonwide"]?></td></tr>
		<tr><td>Users</td><td><?=$sitestats["users"]?></td></tr>
		<tr><td>API Users</td><td><?=$sitestats["apiusers"]?></td></tr>
		<tr><td>Ratings</td><td><?=$sitestats["ratings"]?></td></tr>
		</table>
		</div>


		<div class="section">
		<h1>Reports</h1>
			<a href="/index.php?tab=bannerartists">Banners by User</a><br>
			<a href="/index.php?tab=recentbanners&bannertype=series">50 Most Recent Series Banners</a><br>
			<a href="/index.php?tab=recentbanners&bannertype=fanart">20 Most Recent Fan Art</a><br>
			<a href="/index.php?tab=recentbanners&bannertype=fanart&artistcolorsmissing=1">20 Most Recent Fan Art (missing artist colors)</a><br>
			<a href="/index.php?tab=recentbanners&bannertype=season">20 Most Recent Season Banners</a><br>
			<a href="/index.php?tab=recentbanners&bannertype=seasonwide">50 Most Recent Wide Season Banners</a><br>
			<a href="/index.php?tab=bannerrequests">Requested Banners</a><br>
			<a href="/index.php?tab=newshows">30 Newest Shows</a><br>
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
						ECHO "<br><a href='/index.php?tab=series&id=$db->id'>$db->seriesname</a>";
					}
				}
				?>
			</div>

		<?php	## Otherwise, display popular/recommended banners
			}
			else  {
		?>

			<div class="section">
			<h1>Popular Shows</h1>
				<a href="/index.php?tab=series&id=73739"><img src="banners/_cache/graphical/24313-g2.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=79501"><img src="banners/_cache/graphical/79501-g.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=75340"><img src="banners/_cache/graphical/31635-g3.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=76290"><img src="banners/_cache/graphical/3866-g4.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=73545"><img src="banners/_cache/graphical/23557-g.jpg" class="banner" border="0"></a>
			</div>

			<div class="section">
			<h1>Recommended By The Admins</h1>
				<a href="/index.php?tab=series&id=73067"><img src="banners/_cache/graphical/21612-g.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=73244"><img src="banners/_cache/graphical/73244-g2.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=75397"><img src="banners/_cache/graphical/31988-g2.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=75682"><img src="banners/_cache/graphical/33332-g.jpg" class="banner" border="0"></a>
				<a href="/index.php?tab=series&id=73104"><img src="banners/_cache/graphical/73104-g.jpg" class="banner" border="0"></a>

				<a href="/index.php?tab=series&id=79759"><img src="banners/_cache/graphical/79759-g2.jpg" class="banner" border="0"></a>
			</div>
		<?php } ?>
	</td>
</tr>
</table>
