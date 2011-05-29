<?php
$sql = "SELECT g.GameTitle, b.filename, g.id FROM games as g LEFT JOIN banners as b ON g.id = b.keyvalue ORDER BY lastupdated desc LIMIT 6";
$result = mysql_query($sql);
?>

<?php if ($result !== FALSE): ?>
    <div class="right" id="recent">
        <h2>Recently Updated</h2>
        <ul>
        <?php while ($row = mysql_fetch_array($result)): ?>
            <li>
                <a href="<?= $baseurl ?>/?tab=game&id=<?= $row[2] ?>&lid=1" >
                    <span><?= $row[0] ?></span>
                <?php if (isset($row[1])): ?>
                    <img src="<?= $baseurl ?>/banners/_cache/<?= $row[1] ?>" />
                <?php endif ?>
                </a>
            </li>
        <?php endwhile ?>
                </ul>
            </div>
<?php endif ?>
                    <div id="news">
                        <h2>Welcome</h2>
    <? if ($loggedin): ?>
                        <a href="<?= $baseurl ?>/?tab=userinfo">User Info</a>
    <? endif; ?>
    <p>This website aims to be the top resource for video game scraping via our API.  We strive to have the highest quality available in our artwork and metadata.  This site is open and entirely community drivin, and relies on user submissions for content.  We host Fanart, Banners, Covers, and Metadata that can be incorporated into media center front-ends for HTPC's in various ways.  Please feel free to contribute!</p>
	<h2>Site News</h2>
    <p>We are currently working on the layout, the engine, and a couple of other things.  We have recently upgraded to a new host server, so enjoy the increased speed!</p>
	<h2>API In Action</h2>
	<p>Imagine yourself sitting on your couch with your remote in hand. You're browsing through an endless number of video games, all beautifully displayed on your screen with high resolution art work and accurate info.  Now stop imagining, and start living the dream!
	Here's some examples of how TheGamesDB's API and database can be used to display artwork and metadata for your games.</p>
	<center><a href=http://thegamesdb.net/?tab=showcase><img src=http://thegamesdb.net/images/showcase.png></a></center>
</div>

<div style="clear:both;"></div>

