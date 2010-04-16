<div class="section">
<h1>20 Most Recent Series Banners</h1>

<?php	## Display banners
	$bannercount = 0;
	$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS user FROM banners WHERE keytype='series' AND subkey !='blank' AND dateadded>1160466915 ORDER BY dateadded DESC LIMIT 20";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($banner = mysql_fetch_object($result))  {
		print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"banners/$banner->filename\" class=\"banner\" border=\"0\" alt=\"$banner->subkey\"></a>\n";
		print "<div id=\"bannerauth\">Banner by $banner->user</div>\n";
		$bannercount++;
	}
	?>
</div>


<h1>20 Most Recent Blank Series Banners</h1>

<?php	## Display banners
	$bannercount = 0;
	$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS user FROM banners WHERE keytype='series' AND subkey ='blank' AND dateadded>1160466915 ORDER BY dateadded DESC LIMIT 20";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($banner = mysql_fetch_object($result))  {
		print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"banners/$banner->filename\" class=\"banner\" border=\"0\" alt=\"$banner->subkey\"></a>\n";
		print "<div id=\"bannerauth\">Banner by $banner->user</div>\n";
		$bannercount++;
	}
	?>
</div>


<div class="section">
<h1>20 Most Recent Season Banners</h1>
<?php	## Display banners
	$bannercount = 0;
	$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS user FROM banners WHERE keytype='season' AND dateadded>1160466915 ORDER BY dateadded DESC LIMIT 20";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($banner = mysql_fetch_object($result))  {
		print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"banners/$banner->filename\" class=\"banner\" border=\"0\" alt=\"$banner->subkey\"></a>\n";
		print "<div id=\"bannerauth\">Banner by $banner->user</div>\n";
		$bannercount++;
	}
	?>
</div>
<div class="section">
<h1>20 Most Recent Wide Season Banners</h1>
<?php	## Display banners
	$bannercount = 0;
	$query	= "SELECT *, (SELECT username FROM users WHERE id=banners.userid) AS user FROM banners WHERE keytype='seasonwide' AND dateadded>1160466915 ORDER BY dateadded DESC LIMIT 20";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($banner = mysql_fetch_object($result))  {
		print "<a href=\"/?tab=series&id=$banner->keyvalue\"><img src=\"banners/$banner->filename\" class=\"banner\" border=\"0\" alt=\"$banner->subkey\"></a>\n";
		print "<div id=\"bannerauth\">Banner by $banner->user</div>\n";
		$bannercount++;
	}
	?>
</div>
</div>

