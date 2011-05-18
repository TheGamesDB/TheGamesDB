<?php
	## Get this user's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM users WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$db = mysql_fetch_object($result);
?>

<div class="section">
	<h1><?=$db->username?> Art Contributions</h1>
  <a href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>">50 Most Recent Game Banners</a> - <a href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a><br>
	<a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>">20 Most Recent Fan Art</a> - <a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a> - <a href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>&artistcolorsmissing=Moo">Missing Artist Colors</a><br>
	<a href="/index.php?tab=recentbanners&bannertype=boxart&banneruser=<?=$id?>">20 Most Recent Boxart</a> - <a href="/index.php?tab=recentbanners&bannertype=boxart&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a><br>
</div>
