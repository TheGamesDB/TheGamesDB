<?php
	if ($adminuserlevel == 'ADMINISTRATOR')  {
	## Get this user's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM users WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$db = mysql_fetch_object($result);
?>

<div class="section">
<h1>100 Most Recent Series Banners by <?=$db->username?></h1>
	<div style="text-align: center">
	<?php	## Display all banners by this user
		$bannercount = 0;
		$query	= "SELECT * FROM banners WHERE keytype='series' AND userid=$id ORDER BY dateadded DESC LIMIT 100";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"/banners/$banner->filename\" class=\"banner\" border=\"0\"></a><br>\n";
       		print "<div id=\"bannerauthor\">You can <a href=\"$fullurl&function=Delete+Banner&bannerid=$banner->id\">delete it</a>.</div><br>\n";
			$bannercount++;
		}
		if ($bannercount == 0)  {
			print "There are no series banners by this user\n";
		}
	?>
	</div>
</div>

<div class="section">
<h1>100 Most Recent Season Banners by <?=$db->username?></h1>
	<div style="text-align: center">
	<?php	## Display all banners by this user
		$bannercount = 0;
		$query	= "SELECT * FROM banners WHERE keytype='season' AND userid=$id ORDER BY dateadded DESC LIMIT 100";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"/banners/$banner->filename\" class=\"banner\" border=\"0\"></a><br>\n";
       		print "<div id=\"bannerauthor\">You can <a href=\"$fullurl&function=Delete+Banner&bannerid=$banner->id\">delete it</a>.</div><br>\n";
			$bannercount++;
		}
		if ($bannercount == 0)  {
			print "There are no season banners by this user\n";
		}
	?>
	</div>
</div>

<div class="section">
<h1>100 Most Recent Wide Season Banners by <?=$db->username?></h1>
	<div style="text-align: center">
	<?php	## Display all banners by this user
		$bannercount = 0;
		$query	= "SELECT * FROM banners WHERE keytype='seasonwide' AND userid=$id ORDER BY dateadded DESC LIMIT 100";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"/banners/$banner->filename\" class=\"banner\" border=\"0\"></a><br>\n";
       		print "<div id=\"bannerauthor\">You can <a href=\"$fullurl&function=Delete+Banner&bannerid=$banner->id\">delete it</a>.</div><br>\n";
			$bannercount++;
		}
		if ($bannercount == 0)  {
			print "There are no season banners by this user\n";
		}
	?>
	</div>
</div>
<?php
	} //
	else  {
?>
		<div class="section">
		<h1>Administrators Only</h1>
		</div>
<?php
	}
?>
