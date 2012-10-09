<?php
	## Get this user's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM users WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$db = mysql_fetch_object($result);
?>


<div id="gameHead">
	<h1><?=$db->username?>'s Art Contributions</h1>
	
	<p style="text-align: center;"><a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=boxart&banneruser=<?=$id?>">20 Most Recent Boxart</a> - <a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=boxart&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a></p>

	<p style="text-align: center;"><a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>">20 Most Recent Fan Art</a> - <a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=fanart&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a></p>

	<p style="text-align: center;"><a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>">50 Most Recent Game Banners</a> - <a style="color: orange;" href="/index.php?tab=recentbanners&bannertype=series&banneruser=<?=$id?>&ratedonly=WhatSoundDoWombatsMake">Rated Only</a></p>	
</div>
