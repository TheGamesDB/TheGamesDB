<?php
include('simpleimage.php');
function imageResize($filename, $cleanFilename, $target)
{
	if(!file_exists($cleanFilename))
	{
		$dims = getimagesize($filename);
		$width = $dims[0];
		$height = $dims[1];
		//takes the larger size of the width and height and applies the formula accordingly...this is so this script will work dynamically with any size image
		if ($width > $height)
		{
			$percentage = ($target / $width);
		}
		else
		{
			$percentage = ($target / $height);
		}
		
		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage); 
		
		$image = new SimpleImage();
		$image->load($filename);
		$image->resize($width, $height);
		$image->save($cleanFilename);
		$image = null;
	}
	//returns the new sizes in html image tag format...this is so you can plug this function inside an image tag and just get the
	return "src=\"$baseurl/$cleanFilename\"";
}
?>
<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
	
	<h1>Top Rated Games</h1>
	
	<?php
		$recentResult = mysql_query(" SELECT g.*, p. name, p.icon, g.id, AVG(r.rating) as toprating FROM games AS g, platforms AS p, ratings AS r WHERE r.itemid = g.id AND r.itemtype = 'game' AND g.platform = p.id GROUP BY g.GameTitle ORDER BY toprating DESC, GameTitle ASC LIMIT 50 ");
		$count = 1;
		$recent = mysql_fetch_object($recentResult)
			//echo "$recent->id, $recent->GameTitle, $recent->lastupdated <br />";
	?>
		<div style=" width: 90%; padding: 16px; margin: 10px auto 20px auto; border-radius: 4px; border: 1px solid #4f4f4f; background-color: #333;">

			<?php
				if($boxartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$recent->id' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
				{
					$boxart = mysql_fetch_object($boxartResult);
				}
			?>
			
			<div style="height: 200px; float: left; padding-right: 12px; width: 202px; text-align: center;">
			<?php
				if($boxart->filename != "")
				{
			?>
				<img <?=imageResize("$baseurl/banners/$boxart->filename", "banners/_favcache/_boxart-view/$boxart->filename", 200)?> alt="<?=$game->GameTitle?> Boxart" style="border: 1px solid #666;"/>
			<?php
				}
				else
				{
			?>
				<img src="<?=$baseurl?>/images/common/placeholders/boxart_blank.png" alt="<?=$game->GameTitle?> Boxart"  style="width:140px; height: 200px; border: 1px solid #666;"/>
			<?php
				}
			?>
			</div>
			
			<h2><?= $count ?>: <a style="color: orange; text-decoration: none;" href="<?= $baseurl ?>/game/<?= $recent->id ?>/"><?= $recent->GameTitle ?></a></h2>
			<p><img src="<?=$baseurl?>/images/common/consoles/png24/<?=$recent->icon?>" alt="<?=$recent->name?>" style="vertical-align: -6px;" />&nbsp;<a style="font-size: 14px; color: #fff;" href="<?= $baseurl; ?>/platform/<?php if(!empty($recent->PlatformAlias)) { echo $recent->PlatformAlias; } else { echo $recent->Platform; } ?>/"><?=$recent->name?></a>
			<span style=" float: right; background-color: #333; padding: 6px; border-radius: 6px;">
			<?php
			$ratingquery	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='game' AND itemid=$recent->id";
			$ratingresult = mysql_query($ratingquery) or die('Query failed: ' . mysql_error());
			$rating = mysql_fetch_object($ratingresult);
			for ($i = 2; $i <= 10; $i = $i + 2) {
				if ($i <= $rating->average) {
					print "<img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 />";
				}
				else if ($rating->average > $i - 2 && $rating->average < $i) {
					print "<img src=\"$baseurl/images/game/star_half.png\" width=15 height=15 border=0 />";
				}
				else {
					print "<img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 />";
				}
			}
			?>
			</span></p>
			<p style="text-align: justify;"><?php if ($recent->Overview != "") { echo substr($recent->Overview, 0, 410) . "..."; } else { echo "<br />No Overview Available...<br /><br />"; } ?></p>
			<div>
				<p>
				<?php
				$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$recent->id' AND banners.filename LIKE '%front%' LIMIT 1");
				$boxartResult = mysql_num_rows($boxartQuery);
				
				$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$recent->id' AND keytype = 'fanart' LIMIT 1");
				$fanartResult = mysql_num_rows($fanartQuery);

				$bannerQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$recent->id' AND keytype = 'series' LIMIT 1");
				$bannerResult = mysql_num_rows($bannerQuery);
				
				$screenQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$recent->id' AND keytype = 'screenshot' LIMIT 1");
				$screenResult = mysql_num_rows($screenQuery);
				?>
				
				<?php if($recent->Rating != ""){ ?>ESRB:&nbsp;<?php echo "<b style=\"color: orange;\">$recent->Rating</b> | "; } else{ ?>ESRB:&nbsp;<b style="color: orange;">N/A</b> | <?php }
				if($boxartResult != 0){ ?>Boxart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /> | <?php } else{ ?>Boxart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /> | <?php }
				if($fanartResult != 0){ ?>Fanart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /> | <?php } else{ ?>Fanart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /> | <?php }
				if($bannerResult != 0){ ?>Banner:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /> | <?php } else{ ?>Banner:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /> | <?php }
				if($screenResult != 0){ ?>Screens:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /> | <?php } else{ ?>Screens:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /> | <?php }
				if($recent->Youtube != ""){ ?>Trailer:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /><?php } else{ ?>Trailer:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /><?php }?>
				</p>
			</div>
			<div style="clear: both;"></div>
		</div>
	<?php
			$count++;
			
			
		## Tile Items Display
		while($recent = mysql_fetch_object($recentResult))
		{
			if($boxartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$recent->id' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
			{
				$boxart = mysql_fetch_object($boxartResult);
			}
			?>
				<div style="width: 440px; min-height: 150px; float: left; padding: 6px; margin: 10px 13px; border-radius: 4px; border: 1px solid #4f4f4f; background-color: #333;">
					<div style="height: 102px; width: 106px; text-align: center; float:left">
					<?php
						if($boxart->filename != "")
						{
					?>
						<img <?=imageResize("$baseurl/banners/$boxart->filename", "banners/_favcache/_tile-view/$boxart->filename", 100)?> alt="<?=$recent->GameTitle?> Boxart" style="border: 1px solid #666;"/>
					<?php
						}
						else
						{
					?>
						<img src="<?=$baseurl?>/images/common/placeholders/boxart_blank.png" alt="<?=$recent->GameTitle?> Boxart"  style="width:70px; height: 100px; border: 1px solid #666;"/>
					<?php
						}
					?>
					</div>
					<h3 style="margin: 0px; padding: 0px 10px 10px 10px;"><?= $count; ?>:&nbsp;<a href="<?=$baseurl?>/game/<?=$recent->id?>/" style="color: orange;"><?=$recent->GameTitle?></a></h3>
					<p style="margin: 0px; padding: 0px 10px 10px 10px;"><img src="<?=$baseurl?>/images/common/consoles/png24/<?=$recent->icon?>" alt="<?=$recent->name?>" style="vertical-align: -6px;" />&nbsp;<a style="color: #fff;" href="<?= $baseurl; ?>/platform/<?php if(!empty($recent->PlatformAlias)) { echo $recent->PlatformAlias; } else { echo $recent->Platform; } ?>/"><?=$recent->name?></a>
					<span style=" float: right; background-color: #333; padding: 6px; border-radius: 6px;">
					<?php
					$ratingquery	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='game' AND itemid=$recent->id";
					$ratingresult = mysql_query($ratingquery) or die('Query failed: ' . mysql_error());
					$rating = mysql_fetch_object($ratingresult);
					for ($i = 2; $i <= 10; $i = $i + 2) {
						if ($i <= $rating->average) {
							print "<img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 />";
						}
						else if ($rating->average > $i - 2 && $rating->average < $i) {
							print "<img src=\"$baseurl/images/game/star_half.png\" width=15 height=15 border=0 />";
						}
						else {
							print "<img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 />";
						}
					}
					?>
					</span></p>
					<p style="margin: 0px; padding: 0px 10px 10px 10px; text-align: justify;"><?php if ($recent->Overview != "") { echo substr($recent->Overview, 0, 140) . "..."; } else { echo "No Overview Available..."; } ?></p>
					<?php
						$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
						$boxartResult = mysql_num_rows($boxartQuery);
						
						$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
						$fanartResult = mysql_num_rows($fanartQuery);

						$bannerQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'series' LIMIT 1");
						$bannerResult = mysql_num_rows($bannerQuery);
						
						$screenQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$recent->id' AND keytype = 'screenshot' LIMIT 1");
						$screenResult = mysql_num_rows($screenQuery);
					?>
						<div style="clear: both; padding-top: 10px; text-align: center;">
					<?php
						if($boxartResult != 0){ ?>Boxart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" /> | <?php } else{ ?>Boxart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" /> | <?php }
						if($fanartResult != 0){ ?>Fanart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" /> | <?php } else{ ?>Fanart:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" /> | <?php }
						if($bannerResult != 0){ ?>Banner:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?>Banner:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" /><?php }
						if($screenResult != 0){ ?>Screens:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /> | <?php } else{ ?>Screens:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /> | <?php }
						if($recent->Youtube != ""){ ?>Trailer:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/tick_16.png" alt="Yes" style="vertical-align: -3px;" /><?php } else{ ?>Trailer:&nbsp;<img src="<?= $baseurl ?>/images/common/icons/cross_16.png" alt="No" style="vertical-align: -3px;" /><?php }?>
						</div>
					<div style="clear: both;"></div>
				</div>
			<?php
			if($increment == "odd")
			{
				$increment = "even";
			}
			else
			{
				$increment = "odd";
			}
			if($increment == "even")
			{
			?>
				<div style="clear: both;"></div>
			<?
			}
			$count++;
		}
	?>
	
		<div style="clear: both;"></div>
	
	</div>
</div>