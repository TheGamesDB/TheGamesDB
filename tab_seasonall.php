<?php
$seasonid ="";
	## Get this series' information
	$seriesid = mysql_real_escape_string($id);
	$query	= "SELECT * FROM tvseries WHERE id=$seriesid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$series = mysql_fetch_object($result);


	$query	= "SELECT * FROM tvseasons WHERE seriesid=$seriesid AND season='0' ";
	$result = mysql_query($query) or die('Query failed First: ' . mysql_error());
	$season0 = mysql_fetch_object($result);
	$query  = "SELECT * FROM tvepisodes WHERE seasonid=$season0->id ORDER BY airsbefore_season, EpisodeNumber";
	$result = mysql_query($query) or die('Query failed Second: ' . mysql_error());
	while($special = mysql_fetch_object($result))  {
			$query2 = "SELECT * FROM translation_episodename WHERE episodeid=$special->id AND languageid=$lid ORDER BY translation";
			$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($db2 = mysql_fetch_object($result2)) {
				$episodename = stripslashes($db2->translation);
			}
			if (!$episodename) {		
				$episodename = stripslashes($special->EpisodeName);
				//$episodename = "<B>".$episodename."</b> - (Default language: Please translate)";
			}	
	  if (!$special->airsafter_season) {
		$specials[$special->EpisodeNumber][$special->airsbefore_episode] = "$special->id'..'$episodename'..'$special->seasonid'..'$special->airsbefore_episode'..'$special->FirstAired'..'$special->filename'..'$special->airsbefore_season";
			$episodename = "";
	  }
	  elseif ($special->airsafter_season) { 	## Build the Specials After Season Array
		$afterspecials[$special->EpisodeNumber][$special->airsafter_season] = "$special->id'..'$episodename'..'$special->seasonid'..'$special->FirstAired'..'$special->filename'..'$special->airsafter_season";
			$episodename = "";
	  }
	}

	## Language stuff
	if ($lid) {
		$urllang = "&amp;lid=$lid";	
	}
?>


<div id="bannerrotator">
<?=bannerdisplay($seriesid)?>
</div>

<div class="titlesection">
	<h1><a href="/?tab=series&id=<?=$series->id?><?php echo $urllang; ?>"><?=stripslashes($series->SeriesName);?></a></h1>
	<h2>All Seasons</h2>
</div>


<div class="section">
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
		<?php
		if ($order == 'dvd') {
			print '<td class="head">DVD Episode #</td>';
		}
		elseif ($order == 'absolute') {
			print '<td class="head">Absolute #</td>';
		}
		else {
			print '<td class="head">Episode Number</td>';
		}

		?>

			<td class="head">Episode Name</td>
			<td class="head">Originally Aired</td>
			<td class="head">Image</td>
		</tr>
<?php
	$episodecount = 0;
		if ($order == 'dvd') {
			$query = "SELECT tvepisodes.*, (SELECT season FROM tvseasons WHERE id=tvepisodes.seasonid) AS SeasonNumber FROM tvepisodes WHERE seriesid=$seriesid ORDER BY DVD_season, DVD_episodenumber";
		}
		elseif ($order == 'absolute') {
			$query = "SELECT tvepisodes.*, (SELECT season FROM tvseasons WHERE id=tvepisodes.seasonid) AS SeasonNumber FROM tvepisodes WHERE seriesid=$seriesid ORDER BY absolute_number";
		}
		else {
			$query = "SELECT tvepisodes.*, (SELECT season FROM tvseasons WHERE id=tvepisodes.seasonid) AS SeasonNumber FROM tvepisodes WHERE seriesid=$seriesid ORDER BY SeasonNumber, EpisodeNumber";
		}
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($db = mysql_fetch_object($result)) {
		## Alternate the rows
		if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }


		## Translate the episodename
		$query2 = "SELECT * FROM translation_episodename WHERE episodeid=$db->id AND languageid=$lid ORDER BY translation";
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		$db2 = mysql_fetch_object($result2);
		if ($db2->translation)  {
			$episodename = stripslashes($db2->translation);
		}
		else  {		
			$episodename = stripslashes($db->EpisodeName);
		}

		## If we have a thumbnail, display an indicator
		$thumbnail = '';
		if ($db->filename) {
			$thumbnail = "<img src=\"/images/checkmark.png\" width=10 height=10>";
		}

		if ($order != 'dvd' or $order != 'absolute') {
			checkspecialall($db->EpisodeNumber, $specials, $seriesid,$db->SeasonNumber);
		}
		if ($order == 'dvd') {
			print "<tr><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$db->DVD_season - $db->DVD_episodenumber</a></td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$episodename</a></td><td class=\"$class\">$db->FirstAired</td><td class=\"$class\">$thumbnail &nbsp;</td></tr>\n";
		}
		elseif ($order == 'absolute') {
			print "<tr><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$db->absolute_number</a></td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$episodename</a></td><td class=\"$class\">$db->FirstAired</td><td class=\"$class\">$thumbnail &nbsp;</td></tr>\n";
		}
		else {
			if ($db->SeasonNumber != 0) {
				print "<tr><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$db->SeasonNumber - $db->EpisodeNumber</a></td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$db->seasonid&id=$db->id$urllang\">$episodename</a></td><td class=\"$class\">$db->FirstAired</td><td class=\"$class\">$thumbnail &nbsp;</td></tr>\n";
			}
		}
		$episodecount++;
		if ($order != 'dvd' or $order != 'absolute') {
			checkspecialallafter($db->EpisodeNumber, $afterspecials, $seriesid,$db->SeasonNumber, $db->seasonid);
		}
	}
	## No matches found?
	if ($episodecount == 0)  {
		print "<tr><td class=odd colspan=3>No Episodes Found</td></tr>\n";
	}
?>
	</table>
</div>
	<div class="section">
		<a href="<?=$fullurl?>&order=aired">Aired Order</a>
		 | 
		<a href="<?=$fullurl?>&order=dvd">DVD Order</a>
		 | 
		<a href="<?=$fullurl?>&order=absolute">Absolute Order</a>
	</div>