<?php
	include("../include.php");
	print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$date = date("r");
?>
<rss version="2.0"  xmlns:dc="http://purl.org/dc/elements/1.1/"  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"  xmlns:admin="http://webns.net/mvcb/"  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
<title>TheTVDB.com Today's New Episodes</title>
<ttl>60</ttl>
<link>http://www.thetvdb.com</link>
<description>TheTVDB.com Today's New Episodes feed will display all of the episodes in our database that are airing today.</description>
<language>en-us</language>
<pubDate><?=$date?></pubDate>
<?php	## Display shows episodes that are airing today
        $query = "SELECT id, seriesid, seasonid, (SELECT translation FROM translation_seriesname WHERE seriesid=tvepisodes.seriesid AND languageid=7 LIMIT 1) AS SeriesName, (SELECT translation FROM translation_episodename WHERE episodeid=tvepisodes.id AND languageid=7 LIMIT 1) AS EpisodeName, (SELECT translation FROM translation_episodeoverview WHERE episodeid=tvepisodes.id AND languageid=7 LIMIT 1) AS EpisodeOverview FROM tvepisodes WHERE FirstAired=CURDATE() ORDER BY SeriesName, EpisodeNumber, EpisodeName";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($db = mysql_fetch_object($result))  {
		if ($db->EpisodeName) {
	?>
		<item>
			<title><?=htmlspecialchars($db->SeriesName)?>: <?=htmlspecialchars($db->EpisodeName)?></title>
			<link>http://www.thetvdb.com/index.php?tab=episode&amp;seriesid=<?=$db->seriesid?>&amp;seasonid=<?=$db->seasonid?>&amp;id=<?=$db->id?></link>
			<description><?=htmlspecialchars($db->EpisodeOverview)?></description>
			<pubDate><?=$date?></pubDate>
		</item>

	<?php
		}
	}
?>

</channel>
</rss>
