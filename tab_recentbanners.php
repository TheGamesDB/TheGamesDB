<?php
	if (!$bannertype)  {
		$bannertype = 'series';
	}
	$bannertype = mysql_real_escape_string($bannertype);
	$banneruser = mysql_real_escape_string($banneruser);
	$paging = mysql_real_escape_string($paging);
	if ($bannertype == 'fanart')  {
		$max = 20;
	}
	elseif ($bannertype == 'season')  {
		$max = 20;
	}
	else  {
		$max = 50;
	}
	
	##This is in here because Paul asked for it, there are no links to it and only Paul knows of it. If pagging ever gets added to these pages then I suppose it could be used for that.
	if ($paging){$max = $paging;}

	## If userid was passed in
	if ($banneruser)  {
		$piggyback = " AND userid=$banneruser ";
		$query	= "SELECT username FROM users WHERE id=$banneruser LIMIT 1";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$db = mysql_fetch_object($result);
		$title = "$db->username - ";
	}
	
	## If ratedonly was passed show only rated banners
	if ($ratedonly) {
		##Just pigging backing this onto your query to see if you'll complain
		$piggyback .= " AND (SELECT COUNT(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) > 0 ";
	}

	## Another piggyback for fan art without artistcolors
	if ($artistcolorsmissing)  {
		$piggyback .= " AND artistcolors IS NULL ";
		$extratitle = "(missing artist colors)";
	}
?>

<div id="gameWrapper">
	<div id="gameHead">

		<div class="links">
			<h1>Site Reports and Statistics</h1>	
			<p>&nbsp;</p>
			<h2 style="text-align: center; color: #FF4F00;"><?=$title?> <?=$max?> Most Recent <?php if($bannertype == "series") { echo "Banner"; } else { echo ucwords($bannertype); } ?> Images <?=$extratitle?></h2>
			<p>&nbsp;</p>

		<table cellspacing="10" cellpadding="2" border="0" align="center" width="600">
		<tr>
		<?php	## Display banners
			$count = 0;
			$bannercount = 0;
			$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS creator, (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS rating, (SELECT COUNT(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS ratingcount, (SELECT name FROM languages WHERE id=banners.languageid) AS language, (SELECT translation FROM translation_seriesname WHERE seriesid=banners.keyvalue AND (languageid=$lid OR languageid=7) ORDER BY languageid DESC LIMIT 1) AS seriesname FROM banners WHERE keytype='$bannertype' $piggyback ORDER BY dateadded DESC LIMIT $max";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($banner = mysql_fetch_object($result))  {
				print "<td valign=\"bottom\"><h2>$banner->seriesname</h2>";
				if ($banner->keytype == "fanart")  {
					$banner->subkey = $banner->resolution;
				}
				if ($banner->userid == $user->id || $adminuserlevel == 'ADMINISTRATOR')  {
					displaybannernew($banner, 1, "$baseurl/game/$banner->keyvalue/");
				}
				else  {
					displaybannernew($banner, 0, "$baseurl/game/$banner->keyvalue/");
				}
				print "<p>&nbsp;</p>	<hr /><p>&nbsp;</p></td>";
				$count++;
				if ($count % 3 == 0)  {
					print "</tr><tr>\n";
				}
			}
		?>
		</tr>
		</table>
		</div>

	</div>
</div>