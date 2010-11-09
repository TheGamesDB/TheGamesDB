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
    <p>This website will serve as a frontend to a complete database of video games for a wide range of commercial platforms. The site includes series banners and backdrops that can be incorporated into various HTPC software and plugins.</p>
    <h2>Site News</h2>
    <p>We are currently working on the layout, the engine, and a couple of other things.  The Go-Live date is still kind of an unknown right now, but we should be ready to roll any day now.</p>
</div>

<div style="clear:both;"></div>

