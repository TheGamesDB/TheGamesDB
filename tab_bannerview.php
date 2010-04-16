<?php
	#####################################################
	## USER INFO
	#####################################################
	#if ($userid)  {
	#	$userid = mysql_real_escape_string($userid);
	#	$query	= "SELECT * FROM users WHERE id=$userid";
	#	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	#	$user = mysql_fetch_object($result);
	#}


	#####################################################
	## GET THE BANNER INFO
	#####################################################
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM banners WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$banner = mysql_fetch_object($result);



	#####################################################
	## GET THE SERIES NAME
	#####################################################
	$translated_seriesname = "";
	$query	= "SELECT * FROM translation_seriesname WHERE seriesid=$banner->keyvalue && (languageid=7 || languageid=$lid)";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while($series = mysql_fetch_object($result))  {
		if ($translated_seriesname == "" || $series->languageid != 7)  {
			$translated_seriesname = $series->translation;
		}
	}
	$seriesname = $translated_seriesname;
	$translated_seriesname = urlencode($translated_seriesname);


	#####################################################
	## GET THE SEASON NUMBER - IF Viewing a Season Banner Only
	#####################################################
	IF ($bannertype == 'season' OR $bannertype == 'seasonwide') {
		$query	= "SELECT * FROM tvseasons WHERE id=$seasonid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$season = mysql_fetch_object($result);
	}


IF (!$bannertype) {$bannertype = 'series';}
?>


<script type="text/javascript">
	function placeseriesname()  {
		color = document.translatedbanner.color.options[document.translatedbanner.color.selectedIndex].value
		document.banner.src = '/translatedbanner.php?filename=<?=$banner->filename?>&text=<?=$translated_seriesname?>&color=' + color
		document.translatedbanner.bannerpath.value = 'http://www.thetvdb.com/translatedbanner.php?filename=<?=$banner->filename?>&text=<?=$translated_seriesname?>&color=' + color
		document.snipshot.snipshot_input.value = 'http://www.thetvdb.com/translatedbanner.php?filename=<?=$banner->filename?>&text=<?=$translated_seriesname?>&color=' + color
	}
	function revert()  {
		document.banner.src = '/banners/<?=$banner->filename?>'
		document.translatedbanner.bannerpath.value = './banners/<?=$banner->filename?>'
		document.snipshot.snipshot_input.value = 'http://www.thetvdb.com/banners/<?=$banner->filename?>'
	}
	function saveas(filename)  {
		window.open('/bannerdownload.php?filename=' + document.translatedbanner.bannerpath.value, "banner")
	}
</script>

<?php
if ($bannertype != 'season') { 	
echo '<img src="/banners/'.$banner->filename.'" class="banner" border="0" style="margin: 10px" name="banner"><br>';
}
?>
<div class="titlesection">
	<h1><a href="/?tab=series&id=<?=$banner->keyvalue?>"><?=$seriesname?></a></h1>
<?php
	if ($season->season == 0 AND $bannertype != 'series') {
		echo "<h2>Specials</h2>";
		echo "<h3>Banner Viewer and Tools</h3>";
	}
	elseif ($bannertype != 'series') {
		echo "<h2>Season $season->season</h2>";
		echo "<h3>Banner Viewer and Tools</h3>";
	}
	else {
		echo "<h2>Banner Viewer and Tools</h2>";
	}
?>
</div>

<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr>
<td width="50%">
	<?php if ($bannertype == 'series') { ?>
		<div class="section" style="display: none">
		<h1>Add Translated Series Name</h1>
		<p>Use this to add the series name into the banner in your preferred language.  Use your account settings to choose your preferred language.  If no translations for the series name are available, the series name will be added in English.</p>
		<form action="" method="post" name="translatedbanner" onSubmit="placeseriesname(); return false">
			<select name="color" size="1">
				<option value="white" selected>white
				<option value="light gray">light gray
				<option value="medium gray">medium gray
				<option value="dark gray">dark gray
				<option value="black">black
			</select>
			<input type="hidden" name="bannerpath" value="./banners/<?=$banner->filename?>">
			<input type="submit" name="null" value="Add Name" />
		</form>
	</div>
	<?php
	}
	if ($bannertype == 'season') { 	
		echo '<img src="/banners/'.$banner->filename.'" class="banner" border="0" style="margin: 10px" name="banner"><br>';
	}
	if ($bannertype != 'season') { 	
	?>
	<div class="section">
		<h1>Save The Banner</h1>
		<p>Easily save the banner onto your computer.</p>
		<form action="" method="post" name="revert" onSubmit="saveas(); return false">
			<input type="submit" name="null" value="Save" OnClick="" />
		</form>
	</div>
	<?php if ($bannertype == 'series') { ?>
	<div class="section">
		<h1>Revert</h1>
		<p>Undo any changes to the banner above by clicking the revert button.</p>
		<form action="" method="post" name="revert" onSubmit="revert(); return false">
			<input type="submit" name="null" value="Revert" />
		</form>
	</div>
    <?php
	}
    if ($adminuserlevel == 'ADMINISTRATOR')  {?>
	<div class="section">
		<h1>Change Banner Language</h1>
		<form action="" method="post" name="changelanguage" onSubmit="revert(); return false">
			<select name="languageid" size="1" onChange="ShowEpisodeName(this.options[this.selectedIndex].value)">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($banner->languageid == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}
					print "<option value=\"$langid\" class=\"$class\" $selected>$langname</option>\n";
				}
			?>
			</select>
			<input type="submit" name="function" value="Change Language" class="submit">
		</form>
	</div>
    <?php } }?>

</td>
<td width="50%">
	<?php
	if ($bannertype == 'season') { 	
	?>
	<div class="section">
		<h1>Save The Banner</h1>
		<p>Easily save the banner onto your computer.</p>
		<form action="" method="post" name="revert" onSubmit="saveas(); return false">
			<input type="submit" name="null" value="Save" OnClick="" />
		</form>
	</div>

    <?php if ($adminuserlevel == 'ADMINISTRATOR')  {?>
	<div class="section">
		<h1>Change Banner Language</h1>
		<form action="" method="post" name="changelanguage" onSubmit="revert(); return false">
			<select name="languageid" size="1" onChange="ShowEpisodeName(this.options[this.selectedIndex].value)">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($banner->languageid == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}
					print "<option value=\"$langid\" class=\"$class\" $selected>$langname</option>\n";
				}
			?>
			</select>
			<input type="submit" name="function" value="Change Language" class="submit">
		</form>
	</div>
    <?php } }?>

	<div class="section">
		<h1>Mark As Preferred</h1>
		<p>By marking a banner as preferred, it will be the only banner returned for this series when your HTPC software contacts our site.  Please note that this feature is only available if you use a plugin that supports the Account Identifier features.</p><p>Even if you don't use supported software, this banner will be displayed in the favorite series listings and on the series header.  Please also note that any translated text/resizing/filters/etc will not be displayed in these locations... only the base banner.</p>
		<p style="color: red">Not functional yet. Sorry.</p>
		<form action="index.php" method="post" name="preferred">
			<input type="submit" name="null" value="Make Preferred" />
		</form>
	</div>
	<div class="section">
		<h1>Snipshot</h1>
		<p>Edit the picture with <a href="http://www.snipshot.com" target="_blank">Snipshot</a>.  Snipshot is a web-based interface that allows you to crop, resize, and do much more to pictures.  Use this to get the banner in the exact size you need.  You can also add the translated series name before doing this and it will transfer too!</p>
		<form action="http://services.snipshot.com/" accept-charset="utf-8" method="post" enctype="multipart/form-data" target="_blank" name="snipshot">
			<input type="hidden" name="snipshot_input" value="http://thetvdb.com/banners/<?=$banner->filename?>" />
			<input type="submit" name="null" value="Edit Now!"/>
		</form>
	</div>

</td>
</tr>
</table>

