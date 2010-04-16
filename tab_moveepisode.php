<?php
	## Get this series' information
	$seriesid = mysql_real_escape_string($seriesid);
	$query	= "SELECT * FROM tvseries WHERE id=$seriesid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$series = mysql_fetch_object($result);

	## Get this episode's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM tvepisodes WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$episode = mysql_fetch_object($result);

	## Get the season information
	global $season;
	$query	= "SELECT * FROM tvseasons WHERE seriesid=$seriesid ORDER BY season";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($db = mysql_fetch_object($result))  {
	  if ($db->id == $episode->seasonid) {
		$season = "$db->season";
	  }
	}

if ($lid) {
   $urllang = "&amp;lid=$lid";	
}
?>

<div id="bannerrotator">
<?=bannerdisplay($seriesid)?>
</div>


<table cellspacing="5" cellpadding="0" border="0" width="100%">
<tr>
	<td valign="top" width="100%">

<div class="titlesection">
	<h1><a href="/?tab=series&id=<?=$series->id?><?=$urllang?>"><?=stripslashes($series->SeriesName);?></a></h1>
<?php
	if ($season == 0) {
		echo "<h2><a href=\"/?tab=season&seriesid=$series->id&seasonid=$season->id$urllang\">Specials</a></h2>";
	}
	else {
		echo "<h2><a href=\"/?tab=season&seriesid=$series->id&seasonid=$season->id$urllang\">Season $season</a></h2>";
	}
?>

	<h3><?=$episode->EpisodeNumber?> : <?=stripslashes($episode->EpisodeName);?></h3>
</div>


<div class="section">
<form action="<?=$fullurl?>" method="POST" name="episodeform">
	<div id="red"><?=$errormessage?></div>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="datatable">
	<tr>
		<td width="100">Season Number:</td>
		<td align="left">
		  <select name="seasonid" size="1">
	<?php	## Display the seasons
		$query	= "SELECT * FROM tvseasons WHERE seriesid=$seriesid ORDER BY season";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($db = mysql_fetch_object($result))  {
		  if ($db->id == $episode->seasonid) {
			echo "<option value='$db->id' selected>Season : $db->season</option>";
		  }
		  else{
			echo "<option value='$db->id'>Season : $db->season</option>";
		  }
		}

	?>
		  </select>
		</td>
	<?php	if ($adminuserlevel == 'ADMINISTRATOR')  {  ?>
		<td style="text-align: left">
			<div class="float-right">
			<input type="submit" name="function" value="Move Episode" class="submit">
		<?php $link = "/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$id$urllang";
		    echo '<INPUT TYPE="BUTTON" VALUE="Return To Episode Info" ONCLICK="window.location.href=\''.$link.'\'"> '; ?>
			</div>
		</td>
	</tr>
	<?php	}   ?>

	</table>
</form>


</div>
	</td>
</tr>
</table>
