<!-- Start Admin Only Stats & Reports -->
<?php
	if ($adminuserlevel == 'ADMINISTRATOR') {
?>
	<div style="text-align: center;">
		<h1 class="arcade">Admin Reports &amp; Statistics:</h1>
		<p><a href="?tab=userlist">User List</a></p>
		<p><a href="?tab=adminstats&statstype=missingplatform">Games Missing Platform Data</a></p>
		<p><a href="?tab=adminstats&statstype=morefront">Games With 2 or More Front Boxart</a></p>
		<p><a href="?tab=adminstats&statstype=multipleplatform">Games With Multiple Platforms</a></p>
		<p><a href="?tab=adminstats&statstype=locked">Locked Games</a></p>
	</div>
	<hr />
<?php
	}
?>
<!-- End Admin Only Stats & Reports -->

<div style="text-align: center;">
	<h1 class="arcade">Site Reports and Statistics:</h1>
	<p><a href="?tab=bannerartists">Top 50 Art Contributors</a></p>
	<p><a href="?tab=recentbanners&bannertype=series">50 Most Recent Game Banners</a></p>
	<p><a href="?tab=recentbanners&bannertype=fanart">20 Most Recent Fanart Images</a></p>
	<p><a href="?tab=recentbanners&bannertype=boxart">50 Most Recent Boxart Images</a></p>
	<p><a href="?tab=adminstats&statstype=missingoverview">Games Missing Overview</a></p>
	<p><a href="?tab=adminstats&statstype=missinggenre">Games Missing Genre Data</a></p>
	<p><a href="?tab=adminstats&statstype=missingfront">Games Missing Front Boxart</a></p>
	<p><a href="?tab=adminstats&statstype=missingback">Games Missing Back Boxart</a></p>
	<p><a href="?tab=adminstats&statstype=missingfanart">Games Missing Fanart</a></p>
	<p><a href="?tab=adminstats&statstype=missingbanner">Games Missing Banners</a></p>
	<p><a href="?tab=adminstats&statstype=missingscreenshot">Games Missing Screenshots</a></p>
	<p><a href="?tab=adminstats&statstype=missingyoutube">Games Missing Youtube Trailers</a></p>
</div>

