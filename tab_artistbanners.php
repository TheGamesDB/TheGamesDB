<?php
	## Get this user's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM users WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$db = mysql_fetch_object($result);
?>

<div class="section">
	<h1><?=$db->username?> Banners</h1>
  <a href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>">50 Most Recent Series Banners</a> - <a href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a><br>
	<a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>">20 Most Recent Fan Art</a> - <a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a> - <a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>&artistcolorsmissing=Moo">Missing Artist Colors</a><br>
	<a href="/index.php?tab=recentbanners&bannertype=season&banneruser=<?=$id?>">20 Most Recent Season Banners</a> - <a href="/index.php?tab=recentbanners&bannertype=season&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a><br>
	<a href="/index.php?tab=recentbanners&bannertype=seasonwide&banneruser=<?=$id?>">50 Most Recent Wide Season Banners</a> - <a href="/index.php?tab=recentbanners&bannertype=seasonwide&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a><br>
</div>
