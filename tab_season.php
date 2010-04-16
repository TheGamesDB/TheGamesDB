<?php
	## Get this series' information
	$seriesid = mysql_real_escape_string($seriesid);
	$query	= "SELECT * FROM games WHERE id=$seriesid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$series = mysql_fetch_object($result);

	## Get this season's information
	$seasonid = mysql_real_escape_string($seasonid);
	$query	= "SELECT * FROM tvseasons WHERE id=$seasonid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$season = mysql_fetch_object($result);

	##Get Name of Admin if locked
	if ($series->lockedby ) {
		$query3	= "SELECT * FROM users WHERE id=$series->lockedby limit 1";
		$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
		$lockadmin	= mysql_fetch_object($result3);
	}
	if ($season->lockedby ) {
		$query3	= "SELECT * FROM users WHERE id=$season->lockedby limit 1";
		$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
		$lockadmin	= mysql_fetch_object($result3);
	}

	## Get the number of seasons
	$query	= "SELECT MIN(season) AS minimum, MAX(season) AS maximum FROM tvseasons WHERE seriesid=$seriesid";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$seasons= mysql_fetch_object($result);

	## Get the id of the minimum season
	$query	= "SELECT id FROM tvseasons WHERE seriesid=$seriesid AND season<$season->season ORDER BY season DESC LIMIT 1";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$minseason= mysql_fetch_object($result);

	## Get the id of the maximum season
	$query	= "SELECT id FROM tvseasons WHERE seriesid=$seriesid AND season>$season->season ORDER BY season LIMIT 1";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$maxseason= mysql_fetch_object($result);

	## Build the Specials Array
	$query	= "SELECT * FROM tvseasons WHERE seriesid=$seriesid AND season='0' ";
	$result = mysql_query($query) or die('Query failed First: ' . mysql_error());
	$season0 = mysql_fetch_object($result);
	$query  = "SELECT * FROM tvepisodes WHERE seasonid=$season0->id AND airsbefore_season=$season->season ORDER BY EpisodeNumber";
	$result = mysql_query($query) or die('Query failed Second: ' . mysql_error());
	while($special = mysql_fetch_object($result))  {
		if (!$special->airsafter_season) {
			$query2 = "SELECT * FROM translation_episodename WHERE episodeid=$special->id AND languageid=$lid ORDER BY translation";
			$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($db2 = mysql_fetch_object($result2)) {
				$episodename = stripslashes($db2->translation);
			}
			if (!$episodename) {		
				$episodename = stripslashes($special->EpisodeName);
				//$episodename = "<B>".$episodename."</b> - (Default language: Please translate)";
			}	
		    $specials[$special->EpisodeNumber][$special->airsbefore_episode] = "$special->id'..'$episodename'..'$special->seasonid'..'$special->airsbefore_episode'..'$special->FirstAired'..'$special->filename";
			$episodename = "";
		}
	}
	$query  = "SELECT * FROM tvepisodes WHERE seasonid=$season0->id AND airsafter_season=$season->season";
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
			$afterspecials[$special->EpisodeNumber][$special->airsafter_season] = "$special->id'..'$episodename'..'$special->seasonid'..'$special->FirstAired'..'$special->filename'..'$seasonid";
			$episodename = "";
	}

if ($lid) {
   $urllang = "&amp;lid=$lid";	
}

	if ($user->lastupdatedby_admin)  { 
		$query	= "SELECT * FROM users WHERE id=$user->lastupdatedby_admin";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$adminuser	= mysql_fetch_object($result);
	}
?>

<div id="bannerrotator">
<?=bannerdisplay($seriesid)?>
</div>

<table cellspacing="5" cellpadding="0" border="0" width="100%">
<tr>
	<td valign="top" width="100%">


<div class="titlesection">
	<h1><a href="/?tab=series&id=<?=$series->id?><?php echo $urllang; ?>"><?=stripslashes($series->SeriesName);?></a></h1>
<?php
	if ($season->season == 0) {
		echo "<h2>Specials</h2>";
	}
	else {
		echo "<h2>Season $season->season</h2>";
	}
?>
</div>

<div class="section">
	<div id="red"><?=$errormessage?></div>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
		<?php
			$episodecount = 0;
		if ($order == 'dvd') {
			print '<td class="head">DVD Season #</td><td class="head">DVD Episode #</td>';
		}
		else {
			print '<td class="head">Episode Number</td>';
		}

		?>
			<td class="head">Episode Name</td>
			<td class="head">Originally Aired</td>
			<td class="head">Image</td>
		</tr>

		<?php	## Display series
			$episodecount = 0;
		if ($order == 'dvd') {
			$query = "SELECT * FROM tvepisodes WHERE seasonid=$seasonid ORDER BY DVD_season, DVD_episodenumber";
		}
		else {
			$query = "SELECT * FROM tvepisodes WHERE seasonid=$seasonid ORDER BY EpisodeNumber";
		}
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($db = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
			$query2 = "SELECT * FROM translation_episodename WHERE episodeid=$db->id AND languageid=$lid ORDER BY translation";
			$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
			checkspecial($db->EpisodeNumber, $specials, $seriesid);
			while ($db2 = mysql_fetch_object($result2)) {
				$episodename = stripslashes($db2->translation);
			}
			if (!$episodename) {		
				$episodename = stripslashes($db->EpisodeName);
				//$episodename = "<B>".$episodename."</b> - (Default language: Please translate)";
			}	
			if ($order == 'dvd') {
				print "<tr><td class=\"$class\">$db->DVD_season</td><td class=\"$class\">$db->DVD_episodenumber</td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$db->id$urllang\">$episodename</a></td><td class=\"$class\">$db->FirstAired</td><td class=\"$class\">";
			}
			else {
				print "<tr><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$db->id$urllang\">$db->EpisodeNumber</a></td><td class=\"$class\"><a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$db->id$urllang\">$episodename</a></td><td class=\"$class\">$db->FirstAired</td><td class=\"$class\">";
			}
				if ($db->filename) {
					print "<img src=\"/images/checkmark.png\" width=10 height=10>";
				}
				else {echo "&nbsp;";}
				print "</td></tr>\n";
				$seriescount++;
				$episodename = "";
				$episodenumber = $db->EpisodeNumber + 1;
			}
			checkspecialafter($season->season, $afterspecials, $seriesid);

			## No matches found?
			if ($seriescount == 0)  {
				print "<tr><td class=odd colspan=3>No Episodes Found</td></tr>\n";
				$episodenumber = 1;
			}
		
		##SuperAdmin Lock Override
		if ($_SESSION['userlevel'] == 'ADMINISTRATOR')  {
			if  ($series->locked == 'yes')  {
				print "<div id=formnote style='color: red;'>This season is locked at the series level.<br> You are a SuperAdmin so you can change it.<br> It was locked by $lockadmin->username</div>";
			}
			elseif ($season->locked == 'yes')
			{
				print "<div id=formnote style='color: red;'>This season is locked at the season level.<br> You are a SuperAdmin so you can change it.<br> It was locked by $lockadmin->username</div>";
			}
			?>
				<form action="<?=$fullurl?>" method="POST">
				<? if ($order != 'dvd') { ?>
				<tr>
					<td><input type="text" name="EpisodeNumber" size="4" value="<?=$episodenumber?>"></td>
					<td><input type="text" name="EpisodeName"></td>
					<td><input type="submit" name="function" value="Add Episode"></td>
				</tr>
				<? } ?>
				<tr>
					<td style="text-align: left" colspan="3" valign="top">
					  <input type="submit" name="function" value="Delete Season" class="submit_red" onClick="return confirmSubmit()" style="margin-top: 15px;">
					  <?php
					  if ($season->locked == 'yes')  {
						echo '<input type="submit" value="Lock Season" name="function" class="submit" disabled>';
						echo '<input type="submit" value="UnLock Season" name="function" class="submit">';
					  }
					  elseif ($series->locked != 'yes') {
						echo '<input type="submit" value="Lock Season" name="function" class="submit">';
						echo '<input type="submit" value="UnLock Season" name="function" class="submit" disabled>';
					  }
					  ?>
					</td>
				</tr>

<?php
		}
		#Regular Admin and User Section
		else {
			if ($series->locked == 'yes' or $season->locked == 'yes')  { 
				if  ($series->locked == 'yes')  {
					print "<div id=formnote style='color: red;'>This season is locked at the series level and cannot be changed.<br> It was locked by $lockadmin->username</div>";
					}
				else {
					print "<div id=formnote style='color: red;'>This season is locked at the season level and cannot be changed.<br> It was locked by $lockadmin->username</div>";
				if ($loggedin == 1)  {  ?>
				<form action="<?=$fullurl?>" method="POST">
				<tr>
					<td style="text-align: left" colspan="3" valign="top">
			<?php if ($adminuserlevel == 'ADMINISTRATOR')  {    
				echo '<input type="submit" value="Lock Season" name="function" class="submit" disabled>';
			        echo '<input type="submit" value="UnLock Season" name="function" class="submit">';
			    }
			?>
					</td>
				</tr>
				<? }	}
			 } 
			else { 
				if ($loggedin == 1)  {  ?>
				<form action="<?=$fullurl?>" method="POST">
				<? if ($order != 'dvd') { ?>
				<tr>
					<td><input type="text" name="EpisodeNumber" size="4" value="<?=$episodenumber?>"></td>
					<td><input type="text" name="EpisodeName"></td>
					<td><input type="submit" name="function" value="Add Episode"></td>
				</tr>
				<? } ?>
				<tr>
					<td style="text-align: left" colspan="3" valign="top">
					<?php
					if ($adminuserlevel == 'ADMINISTRATOR')  {
						echo '<input type="submit" name="function" value="Delete Season" class="submit_red" onClick="return confirmSubmit()" style="margin-top: 15px;">';
						echo '<input type="submit" value="Lock Season" name="function" class="submit">';
					  echo '<input type="submit" value="UnLock Season" name="function" class="submit" disabled>';
					  echo '</td></tr><tr><td style="text-align: left" colspan="3" valign="top"><select name="renum1"><option selected value="+">+</option><option value="-">-</option></select><input type="text" name="renum2" size="3" maxlength="3"> <input type="submit" value="Renumber Episodes" name="function" class="submit">';
					}
					?>
					</td>
				</tr>
		<?php	}  }	} ?>
		</form>
		</table>

<?php	## Display only the season links we need
	if ($seasons->minimum != $seasons->maximum)  {
		print "<div class=\"subsection\">";

		if ($seasons->minimum < $season->season)  {
			print "<a href=\"/?tab=season&seriesid=$seriesid&seasonid=$minseason->id$urllang\">Previous Season</a>";
		}

		if (($seasons->minimum < $season->season) && ($seasons->maximum > $season->season))  {
			print " | ";
		}

		if ($seasons->maximum > $season->season)  {
			print "<a href=\"/?tab=season&seriesid=$seriesid&seasonid=$maxseason->id$urllang\">Next Season</a>";
		}
		print "</div>";
	}
?>
</div>
	<div class="section">
		<a href="<?=$fullurl?>&order=aired">Aired Order</a>
		 | 
		<a href="<?=$fullurl?>&order=dvd">DVD Order</a>
	</div>

	</td>

	<td>
<?php	## Display only the season links we need
	print '<div class="section">';
	if ($seasons->minimum != $seasons->maximum)  {
		print "<div class=\"subsection\" style=\"margin-right: 40px\">";

		if ($seasons->minimum < $season->season)  {
			print "<a href=\"/?tab=season&seriesid=$seriesid&seasonid=$minseason->id$urllang\">Previous Season</a>";
		}

		if (($seasons->minimum < $season->season) && ($seasons->maximum > $season->season))  {
			print " | ";
		}

		if ($seasons->maximum > $season->season)  {
			print "<a href=\"/?tab=season&seriesid=$seriesid&seasonid=$maxseason->id$urllang\">Next Season</a>";
		}
		print "</div>";
	}
	print "</div>";
?>

<div class="section">
<h1>Season Banners</h1>
	<?php	## Display all banners for this season
		$bannercount = 0;
		$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS creator, (SELECT name FROM languages WHERE id=banners.languageid LIMIT 1) AS language, (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS rating, (SELECT COUNT(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS ratingcount FROM banners WHERE keytype='season' AND keyvalue=$seriesid AND subkey='$season->season' ORDER BY rating DESC,RAND()";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			if ($banner->userid == $user->id || $adminuserlevel == 'ADMINISTRATOR')  {
				displaybannernew($banner, 1, "");
			}
			else  {
				displaybannernew($banner, 0, "");
			}
			$bannercount++;
		}
		if ($bannercount == 0)  {
			print "There are no banners for this season\n";
		}
	?>
</div>

<div class="section">
<h1>Wide Season Banners</h1>
	<?php	## Display all banners for this season
		$bannercount = 0;
		$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS creator, (SELECT name FROM languages WHERE id=banners.languageid LIMIT 1) AS language, (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS rating, (SELECT COUNT(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS ratingcount FROM banners WHERE keytype='seasonwide' AND keyvalue=$seriesid AND subkey='$season->season' ORDER BY rating DESC,RAND()";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			if ($banner->userid == $user->id || $adminuserlevel == 'ADMINISTRATOR')  {
				displaybannernew($banner, 1, "");
			}
			else  {
				displaybannernew($banner, 0, "");
			}
			$bannercount++;
		}
		if ($bannercount == 0)  {
			print "There are no wide banners for this season\n";
		}
	?>
</div>

<?php	if ($loggedin == 1)  {  ?>
<div class="section">
<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
<h1>Season Banner Upload</h1>
	<?php  	## check for agreement to terms
		if ($user->banneragreement != 1) {
			print "<div style='width:300px;'>";
			print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
			print "</div>";
		} ## Check for disabled banner upload 
		elseif ($user->bannerlimit == 0) {
			print "<div style='width:300px;'>";
			print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
			print "</div>";
		} ## Check banner limit 
		elseif ($series->disabled == 'Yes')  { 
			print "<div style='width:300px;'>";
			print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
			print "</div>";
		}		
		elseif ($userbanners < $user->bannerlimit || $adminuserlevel == 'ADMINISTRATOR')  {  
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
	<tr>
		<td>File:</td>
		<td>
			<input type="file" name="bannerfile">
		</td>
	</tr>
	<tr>
		<td>Banner Type:</td>
		<td>
			<select name="keytype" size="1">
				<option value="season">DVD Cover</option>
				<option value="seasonwide">Wide Banner</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Banner Language:</td>
		<td>
			<select name="languageid" size="1">
			<?php

				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($lid == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}

					## If a translation is found
					print "<option value=\"$langid\" $selected>$langname</option>\n";
				}
			?>
			</select>
			<div id="formnote">Season banners must be 400x578 for DVD Covers or 758x140 for Wide Banners.</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: right">
			<input type="hidden" name="season" value="<?=$season->season?>">
			<input type="hidden" name="function" value="Upload Season Banner">
			<input type="submit" name="button" value="Upload" class="submit">
		</td>
	</tr>
	</table>
	<?php	## Print banner limit message
		}
		else  {
			print "You have already uploaded $userbanners banners for this season, which is your banner limit.  To get your banner limit increased, please post a request on the forums.";
		}
	?>
</form>
</div>
<?php	}  ?>


	</td>
</tr>
</table>
