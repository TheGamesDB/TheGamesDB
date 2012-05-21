 <?php	if ($loggedin == 1 && $adminuserlevel == 'ADMINISTRATOR') {  ?>

<?php
	// Fetch Game Information from DB
	$id = mysql_real_escape_string($id);
	$query	= "SELECT p.* FROM platforms as p WHERE p.id=$id";
	$result = mysql_query($query) or die('Fetch Game Info Query Failed: ' . mysql_error());
	$rows = mysql_num_rows($result);
	$platform = mysql_fetch_object($result);
?>

<?php
	include_once('simpleimage.php');
	function imageResize($filename, $cleanFilename, $target, $axis)
	{
		if(!file_exists($cleanFilename))
		{
			$dims = getimagesize($filename);
			$width = $dims[0];
			$height = $dims[1];
			//takes the larger size of the width and height and applies the formula accordingly...this is so this script will work dynamically with any size image
			
			if($axis == "width")
			{
				$percentage = ($target / $width);
			}
			else if ($axis == "height")
			{
				$percentage = ($target / $height);
			}
			else if ($width > $height)
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
	
	function imageDualResize($filename, $cleanFilename, $wtarget, $htarget)
	{
		if(!file_exists($cleanFilename))
		{
			$dims = getimagesize($filename);
			$width = $dims[0];
			$height = $dims[1];
			
			while($width > $wtarget || $height > $htarget)
			{
				if($width > $wtarget)
				{
					$percentage = ($wtarget / $width);
				}
			
				if($height > $htarget)
				{
					$percentage = ($htarget / $height);
				}
			
				/*if($width > $height)
				{
					$percentage = ($target / $width);
				}
				else
				{
					$percentage = ($target / $height);
				}*/
				
				//gets the new value and applies the percentage, then rounds the value
				$width = round($width * $percentage);
				$height = round($height * $percentage); 
			}
			
			$image = new SimpleImage();
			$image->load($filename);
			$image->resize($width, $height);
			$image->save($cleanFilename);
			$image = null;
		}
		//returns the new sizes in html image tag format...this is so you can plug this function inside an image tag and just get the
		return "src=\"$baseurl/$cleanFilename\"";
	}
	
	function imageRating($fanartID)
	{
		## Get the site banner rating
		$query  = "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='banner' AND itemid=$fanartID";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$rating = mysql_fetch_object($result);
		
		$str = "Site Rating: ";
		
		## Display the site rating
		for ($i = 2; $i <= 10; $i = $i + 2) {
			if ($i <= $rating->average) {
				$str = $str . "<div style='display: inline-block; width: 15px; height: 15px; background-image: url($baseurl/images/game/star_on.png);'></div>";
			}
			else if ($rating->average > $i - 2 && $rating->average < $i) {
				$str = $str . "<div style='display: inline-block; width: 15px; height: 15px; background-image: url($baseurl/images/game/star_half.png);'></div>";
			}
			else {
				$str = $str . "<div style='display: inline-block; width: 15px; height: 15px; background-image: url($baseurl/images/game/star_off.png);'></div>";
			}
		}
		
		return $str;
	}
	
	function userImageRating($imageID, $baseurl, $gameid, $userID)
	{
			$str = "Your Rating: ";
			## Get user rating for this image
				$query  = "SELECT rating FROM ratings WHERE itemtype='banner' AND itemid=$imageID AND userid=$userID";
				if($result = mysql_query($query))
				{
					$rating = mysql_fetch_object($result);	
				}

				if (!$rating->rating)  {
					$rating->rating = 0;
				}
		
				for ($i = 1; $i <= 10; $i++)  {
					if ($i <= $rating->rating)  {
						$str = $str . "<script type='text/javascript'>var anchorname = 'rating$imageID';</script><a style='display: inline-block !important; width: 15px; height: 15px; border: 0px; background-image: url($baseurl/images/game/star_on.png);' id='rating$imageID$i' href='$baseurl/game-edit/$gameid/?function=UserRating&type=banner&itemid=$imageID&rating=$i' OnMouseOver='UserRateArt(anchorname, $i)' OnMouseOut='UserRateArt(anchorname, $rating->rating)'></a>";
					}
					else  {
						$str = $str . "<script type='text/javascript'>var anchorname = 'rating$imageID';</script><a style='display: inline-block !important; width: 15px; height: 15px; border: 0px; background-image: url($baseurl/images/game/star_off.png);' id='rating$imageID$i' href='$baseurl/game-edit/$gameid/?function=UserRating&type=banner&itemid=$imageID&rating=$i' OnMouseOver='UserRateArt(anchorname, $i)' OnMouseOut='UserRateArt(anchorname, $rating->rating)'></a>";
					}
				}
				
				return $str;
	}
	
	function imageUsername($artID)
	{
		## Get the site banner rating
		$query  = "SELECT u.id, u.username FROM users AS u, banners AS b WHERE b.id = '$artID' AND u.id = b.userid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$imageUser = mysql_fetch_object($result);
		
		$str = "Uploader:&nbsp;<a href='$baseurl/artistbanners/?id=$imageUser->id'>$imageUser->username</a>";
		
		return $str;
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
	
	<?php
	if(mysql_num_rows($result) != 0)
	{
	?>
	
	<form id="editPlatformForm" name="editPlatformForm" action="<?= $baseurl ?>/platform-edit/<?= $platform->id ?>/" method="post" onsubmit="">

		<div id="gameTitle">
			<span style="float: left;">
				<img src="<?php echo $baseurl; ?>/images/common/consoles/png48/<?php if(!empty($platform->icon)){ echo $platform->icon; } else { echo "console_default.png"; } ?>" alt="<?php echo $platform->name; ?>" title="<?php echo $platform->name; ?>" style="vertical-align: middle;" />&nbsp;
				Title:<input type="text" name="name" style="font-size: 18px; font-weight: bold; width: 240px;" value="<?php echo $platform->name; ?>" /><br />
			</span>
			
			<span id ="gameUserLinks"><a href="<?=$baseurl?>/platform/<?=$platform->id?>/"><img src="<?php echo $baseurl; ?>/images/common/icons/edit_128.png" style="width:16px; height: 16px; vertical-align: middle;" /></a>&nbsp;<a href="<?=$baseurl?>/platform/<?=$platform->id?>/">View this Platform</a></span>
			
			<span style="float: left; clear: left;">
				URL Alias:<input type="text" name="alias" style="font-size: 18px; font-weight: bold; width: 240px;" value="<?php echo $platform->alias; ?>" /><br />
				<span style="font-style: italic;" class="grey">E.g. "nintendo-entertainment-system" (Alpha-numeric Characters and Hypens Only)</span>
			</span>
			
			<p style="text-align: center; clear: both; padding: 10px; 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#platformIconUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Platform Icon</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#frontBoxartUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Platform Art</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#fanartUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Fanart</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#bannerUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Banner</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Console Art' /> <a href='#consoleartUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Console Art</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Controller Art' /> <a href='#controllerartUpload' rel='facebox' style="color: orange; text-decoration: underline;">Upload Controller Art</a>
			</p>
			
			<div style="clear: both;"></div>
		</div>
		<div id="gameCoversWrapper">
			<div>
			
				<div id="frontBoxart">
					<div class="slider-wrapper theme-default">
						<?php
						
						if ($frontCoverResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-boxart' "))
						{
							if(mysql_num_rows($frontCoverResult) > 0)
							{
							?>
								<div id="frontBoxartSlider" class="nivoSlider">
							<?php
								$frontBoxartSlideCount = 0;
								while($front = mysql_fetch_object($frontCoverResult))
								{	
							?>
									<img <?=imageResize("$baseurl/banners/$front->filename", "banners/_platformviewcache/$front->filename", 300, "width")?> title="<?= imageUsername($front->id) ?><br /><a href='<?="$baseurl/banners/$front->filename"?>' target='_blank'>View Full-Size</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/platform-edit/$platform->id/?function=Delete+Banner&bannerid=$front->id'>Delete This Art</a>"; } ?><br /><?= imageRating($front->id) ?><br /><?= userImageRating($front->id, $baseurl, $game->id, $user->id) ?>" />
							<?php
									$frontBoxartSlideCount++;
								}
							?>
								</div>
							<?php
							}
							else
							{
							?>
								<img class="imgShadow" src="<?php echo $baseurl; ?>/images/common/placeholders/boxart_blank.png" width="300" height="417" alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
							<?php
							}
						}
						?>	
					</div>
					<div style="clear: both;"></div>
				</div>
			
			</div>
		</div>
		<div id="gameInfo">

			<div id="gameRating">
				<?php
					$query	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='platform' AND itemid=$id";
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());
						$rating = mysql_fetch_object($result);

						for ($i = 2; $i <= 10; $i = $i + 2) {
							if ($i <= $rating->average) {
								print "<img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0>";
							}
							else if ($rating->average > $i - 2 && $rating->average < $i) {
								print "<img src=\"$baseurl/images/game/star_half.png\" width=15 height=15 border=0>";
							}
							else {
								print "<img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0>";
							}
						}
						?>
						&nbsp;&nbsp;<span style="font-weight: bold; color: #bbb;"><?=(int)$rating->average?> / 10</span>
						&nbsp;&nbsp;<span style="color: #888; font-size: 13px;"><em><?=$rating->count?> rating<?php if ($rating->count != 1) print "s" ?></em></span>
						<?php	if ($loggedin == 1) {  ?>
						&nbsp;&nbsp;|&nbsp;&nbsp;Your Rating:&nbsp;
						<?php
							$query	= "SELECT rating FROM ratings WHERE itemtype='platform' AND itemid=$id AND userid=$user->id";
							$result = mysql_query($query) or die('Query failed: ' . mysql_error());
							$rating = mysql_fetch_object($result);
							if (!$rating->rating) {
								$rating->rating = 0;
							}

							for ($i = 1; $i <= 10; $i++) {
								if ($i <= $rating->rating) {
									print "<a href=\"$baseurl/platform-edit/$id/?function=UserRating&type=platform&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
								else {
									print "<a href=\"$baseurl/platform-edit/$id/?function=UserRating&type=platform&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
							}
							?>
						<?php } ?>
			</div>
			<hr />
			<p><span class="grey">Overview</span></p>
			<p><textarea name="overview" style="width: 630px; height: 200px;"><?= $platform->overview ?></textarea></p>
			<hr />
			<?php
				if(!empty($platform->console))
				{
			?>
					<div id="consoleArt" style="float: left; width: 300px; padding: 6px; margin: 0px 3px;">
						<h3 class="grey">Console Art</h3>
						<img src="<?= $baseurl ?>/banners/platform/consoleart/<?= $platform->console ?>" alt="<?= $platform->name ?> Console Art" title="<?= $platform->name ?> Console Art" style="margin-top: 12px;"/>
						<p style="text-align: center;"><span style="text-decoration: none; color: red;">[x]&nbsp;</span><a href="<?= $baseurl ?>/platform-edit/<?= $platform->id ?>/?function=Delete+Console+Art" style="color: orange; text-decoration: underline;">Delete Console Art</a></p>
					</div>
			<?php
				}
			?>
			<?php
					if(!empty($platform->controller))
					{
				?>
				<div id="controllerArt" style="float: left; width: 300px; padding: 6px; margin: 0px 3px;">
					<h3 class="grey">Controller Art</h3>
						<img src="<?= $baseurl ?>/banners/platform/controllerart/<?= $platform->controller ?>" alt="<?= $platform->name ?> Controller Art" title="<?= $platform->name ?> Controller Art" style="margin-top: 12px;"/>
						<p style="text-align: center;"><span style="text-decoration: none; color: red;">[x]&nbsp;</span><a href="<?= $baseurl ?>/platform-edit/<?= $platform->id ?>/?function=Delete+Controller+Art" style="color: orange; text-decoration: underline;">Delete Controller Art</a></p>
				</div>
				<?php
					}
				?>
			<?php
				if(!empty($platform->console) || !empty($platform->controller))
				{
			?>
					<div style="clear: both;"></div>
					<hr />
			<?php
				}
			?>
			<div id="gameVitals">
				<table>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Developer</span>
						</td>
						<td>
							<input type="text" name="developer" id="developer" size="80" value="<?=$platform->developer?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Manufacturer</span>
						</td>
						<td>
							<input type="text" name="manufacturer" id="manufacturer" size="80" value="<?=$platform->manufacturer?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">CPU</span>
						</td>
						<td>
							<input type="text" name="cpu" id="cpu" size="80" value="<?=$platform->cpu?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Memory</span>
						</td>
						<td>
							<input type="text" name="memory" id="memory" size="80" value="<?=$platform->memory?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Graphics</span>
						</td>
						<td>
							<input type="text" name="graphics" id="graphics" size="80" value="<?=$platform->graphics?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Sound</span>
						</td>
						<td>
							<input type="text" name="sound" id="sound" size="80" value="<?=$platform->sound?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Display</span>
						</td>
						<td>
							<input type="text" name="display" id="display" size="80" value="<?=$platform->display?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Media</span>
						</td>
						<td>
							<input type="text" name="media" id="media" size="80" value="<?=$platform->media?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Max. Controllers</span>
						</td>
						<td>
							<input type="text" name="maxcontrollers" id="maxcontrollers" size="80" value="<?=$platform->maxcontrollers?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Youtube Trailer</span>
						</td>
						<td>
							<input type="text" size="46" id="Youtube" name="youtube" value="<?=$platform->youtube?>" onblur="$('#Youtube').val($('#Youtube').val().replace('http://www.youtube.com/watch?v=', '')); $('#Youtube').val($('#Youtube').val().replace('www.youtube.com/watch?v=', '')); $('#Youtube').val($('#Youtube').val().replace('youtube.com/watch?v=', '')); $('#Youtube').val($('#Youtube').val().replace('http://youtu.be/', '')); $('#Youtube').val($('#Youtube').val().replace('http://www.youtu.be/', '')); $('#Youtube').val($('#Youtube').val().replace('www.youtu.be/', '')); " />
						</td>
					</tr>
				</table>
			</div>
			
			<hr />
			
			<input type="hidden" name="platformid" value="<?=  $platform->id ?>">
			<input type="submit" name="function" value="Save Platform">
			
		</div>
		<div style="clear:both"></div>
	</form>
	</div>
	
	<div id="gameContent">
		<div id="gameContentTop">
		
			<div id="panelNav">
				<ul>
					<li><a id="nav_fanartScreens" class="active" href="#gameContentTop" onclick="contentShow('fanartScreens');">Fanart &amp; Screenshots</a></li>
					<li><a id="nav_banners" href="#gameContentTop" onclick="contentShow('banners');">Banners</a></li>
					<li><!-- <a id="nav_platforms" href="#gameContentTop" onclick="contentShow('platforms');">Other Platforms</a> --></li>
					<li><!-- <a id="nav_trailer" href="#gameContentTop" onclick="contentShow('trailer');">Game Trailer</a> --></li>
				</ul>
				<div style="clear: both;"></div>
			</div>
			
			<div style="clear: both;"></div>
			
			<hr />
			
			<div id="fanartScreens">
				
				<div id="fanart">
				
					<div class="slider-wrapper theme-default">
						<div id="fanartRibbon" style="position: absolute; width: 125px; height: 125px; background: url(<?= $baseurl ?>/images/game-view/ribbon-fanart.png) no-repeat; z-index: 10"></div>
						<?php
						if ($fanartResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-fanart' "))
						{
							$fanSlideCount = 0;
							if(mysql_num_rows($fanartResult) > 0)
							{
							?>
								<div id="fanartSlider" class="nivoSlider">
							<?php
								while($fanart = mysql_fetch_object($fanartResult))
								{	
									// $dims = getimagesize("$baseurl/banners/$fanart->filename"); echo "$dims[0] x $dims[1]"; 
							?>
									<img  class="fanartSlide imgShadow" <?=imageResize("$baseurl/banners/$fanart->filename", "banners/_platformviewcache/$fanart->filename", 470, "width")?> alt="<?php echo $game->GameTitle; ?> Fanart" title="<?= imageUsername($fanart->id) ?><br /><a href='<?="$baseurl/banners/$fanart->filename"?>' target='_blank'>View Full-Size</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/platform-edit/$platform->id/?function=Delete+Banner&bannerid=$fanart->id'>Delete This Art</a>"; } ?><br /><?= imageRating($fanart->id) ?> | <?= userImageRating($fanart->id, $baseurl, $game->id, $user->id) ?>" />
							<?php
									$fanSlideCount++;
								}
							?>
								</div>
							<?php
							}
							else
							{
								?>
								<img class="imgShadow" src="<?php echo $baseurl; ?>/images/common/placeholders/fanart_blank.png" width="470" height="264" alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
								<?php
							}
						}
						?>	
					</div>
					
				</div>
				
				<div id="screens">
					
				</div>

				<div style="clear: both;"></div>

			</div>
			
			<div id="banners">
				<div class="slider-wrapper theme-default">
					<div id="bannerRibbon" style="display: none; position: absolute; width: 125px; height: 125px; background: url(<?= $baseurl ?>/images/game-view/ribbon-banners.png) no-repeat; z-index: 10"></div>
					<?php
					if ($bannerResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-banner' ") or die ("banner query failed" . mysql_error()))
					{
						if(mysql_num_rows($bannerResult) > 0)
						{
						?>
							<div id="bannerSlider" class="nivoSlider" style="width:760px important; height: 140px !important;">
						<?php
							$bannerSlideCount = 0;
							while($banner = mysql_fetch_object($bannerResult))
							{	
						?>
								<img class="bannerSlide" src="<?="$baseurl/banners/$banner->filename"?>" alt="<?php echo $game->GameTitle; ?> Banner" title="<?= imageUsername($banner->id) ?> | <a href='<?="$baseurl/banners/$banner->filename"?>' target='_blank'>View Full-Size</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/platform-edit/$platform->id/?function=Delete+Banner&bannerid=$banner->id'>Delete This Art</a>"; } ?><br /><?= imageRating($banner->id) ?> | <?= userImageRating($banner->id, $baseurl, $game->id, $user->id) ?>" />
						<?php
								$bannerSlideCount++;
							}
						?>
							</div>
						<?php
						}
						else
						{
						?>
							<img class="imgShadow" src="<?php echo $baseurl; ?>/images/common/placeholders/banner_blank.png" width="760" height="140" alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
						<?php
						}
					}
					?>	
				</div>
			
				<div style="clear: both;"></div>
			
			</div>
		
			<div id="platforms">

				<div style="clear: both;"></div>
			</div>
			
			<div id="trailer">

				<div style="clear: both;"></div>
			</div>
		
		</div>
		
		<div style="clear: both;"></div>
		
		<!--<div id="gameContentBottom">

			<div style="text-align: center; font-size: 18px; color: #fff; text-decoration: none; text-shadow: 0px 0px 10px #000;">Comments</div>

			<hr style="margin: 10px 0px 14px 0px;" />

			<div id="comments">
				<div style="width: 96%; margin: auto; background: #333; box-shadow: 0px 0px 22px #000; border-radius: 16px; text-align: center;">
					<h1 style="align: center; color: #fff; text-shadow: 0px 0px 10px #000; padding: 100px 0px;">Coming Soon!</h1>
				</div>
				<div style="clear: both;"></div>
			</div>
			
		</div>-->
		
		<!--
		<div id="gameFooter">
		
		</div>
		-->

	</div>
	
	
	<!-- Start of Upload Dialogs -->
	<div style="display: none;">
	<?php if ($loggedin == 1) {  ?>
	<div id="platformIconUpload" class="miniPanel">
		<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Platform Icon Upload</h2>
		<?php  	## check for agreement to terms
		if ($user->banneragreement != 1) {
			print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
		}
		else {
			?>
		<p>The only accepted image format for platform icons is PNG.</p>
		<p>Preferable images will have a transparent background and square dimensions.</p>
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
				<tr>
					<td>File:
						<input type="file" name="iconfile" size="36">
					</td>
				</tr>
				<tr>
					<td>
						<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
					</td>
				</tr>
				<tr>
					<td style="text-align: right">
						<input type="hidden" name="function" value="Upload Platform Icon">
						<input type="hidden" name="platformId" value="<?=  $platform->id ?>">
						<input type="hidden" name="platformAlias" value="<?=  $platform->alias ?>">
						<input type="submit" name="button" value="Upload" class="submit">
					</td>
				</tr>
			</table>
		</form>
			<?php } ?>
	</div>
	<?php } ?>
	
	<?php if ($loggedin == 1) {  ?>
	<div id="frontBoxartUpload" class="miniPanel">
		<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Platform Art Upload</h2>
		<?php  	## check for agreement to terms
		if ($user->banneragreement != 1) {
			print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
		}
		else {
			?>
		<p>The only accepted image formats for platform art are JPG and PNG.</p>
		<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
		<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
				<tr>
					<td>File:
						<input type="file" name="bannerfile" size="36">
					</td>
				</tr>
				<tr>
					<td>
						<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
					</td>
				</tr>
				<tr>
					<td style="text-align: right">
						<input type="hidden" name="cover_side" value="front">
						<input type="hidden" name="function" value="Upload Platform Box Art">
						<input type="submit" name="button" value="Upload" class="submit">
					</td>
				</tr>
			</table>
		</form>
			<?php } ?>
	</div>
	<?php } ?>
	
	<?php	if ($loggedin == 1) {  ?>
	<div id="fanartUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Fan Art Upload</h2>

				<?php  	## check for agreement to terms
				if ($user->banneragreement != 1) {
					print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
				} ## Check for disabled banner upload
				else
				{
				?>
			<p>All fan art resolutions <strong>must</strong> be 1920x1080 <em>(2MB Max Size)</em> or 1280x720.<em>(600KB Max Size)</em></p>
			<p>The only accepted image format for fan art is JPG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
			<p>Please set your artist colors after uploading.</p>
			<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
				<tr>
					<td><strong>File to Upload:</strong><br /><br />
						<input type="file" name="bannerfile" size="42">
					</td>
				</tr>
				<tr>
					<td>
						<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
					</td>
				</tr>
				<tr>
					<td style="text-align: right">
						<input type="hidden" name="function" value="Upload Platform Fan Art">
						<input type="submit" name="button" value="Upload" class="submit">
					</td>
				</tr>
			</table>
					<?php
				}
				?>
		</form>
	</div>
	<?php	}  ?>
	
	<?php	if ($loggedin == 1) {  ?>
	<div id="bannerUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Banner Upload</h2>
			<p>All banner resolutions <strong>must</strong> be 760x140.</p>
			<p>The only accepted image formats for banners are JPG and PNG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
			<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
			<p><strong>File:</strong> <input type="file" name="bannerfile" size="36"></p>
			<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
			<p style="text-align: right;">
				<input type="hidden" name="function" value="Upload Platform Banner">
				<input type="submit" name="button" value="Upload" class="submit">
			</p>
		</form>
	</div>
	<?php	}  ?>
	
	<?php	if ($loggedin == 1) {  ?>
	<div id="controllerartUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Controller Art Upload</h2>
			<p>All controller art <strong>must</strong> be 300x300px.</p>
			<p>The only accepted image format for controller art is PNG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
			<p>We only want images with <strong>transparent</strong> backgrounds.</p>
			<p>Solid, gradiated or textured backgrounds are not permitted.</p>
			<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
			<p><strong>File:</strong> <input type="file" name="controllerartfile" size="36"></p>
			<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
			<p style="text-align: right;">
				<input type="hidden" name="function" value="Upload Controller Art">
				<input type="submit" name="button" value="Upload" class="submit">
			</p>
		</form>
	</div>
	<?php	}  ?>	
	
	<?php	if ($loggedin == 1) {  ?>
	<div id="consoleartUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Console Art Upload</h2>
			<p>All controller art <strong>must</strong> be 300x300px.</p>
			<p>The only accepted image format for controller art is PNG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
			<p>We only want images with <strong>transparent</strong> backgrounds.</p>
			<p>Solid, gradiated or textured backgrounds are not permitted.</p>
			<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
			<p><strong>File:</strong> <input type="file" name="consoleartfile" size="36"></p>
			<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
			<p style="text-align: right;">
				<input type="hidden" name="function" value="Upload Console Art">
				<input type="submit" name="button" value="Upload" class="submit">
			</p>
		</form>
	</div>
	<?php	}  ?>	
	<!-- END of Upload Dialogs -->
	
	</div>

<!-- Start #panelNav Scripts -->
<script type="text/javascript">
	function contentShow(id)
	{
		switch (id)
		{
			case "fanartScreens":
				contentHide();
				$("#nav_fanartScreens").addClass("active");
				$("#fanartScreens").slideDown("400");
				$("#fanartRibbon").slideDown("400");
				$("#screensRibbon").slideDown("400");
			break;
			
			case "banners":
				contentHide();
				$("#nav_banners").addClass("active");
				$("#banners").slideDown("400");
				$("#bannerRibbon").slideDown("400");
			break;
			
			case "platforms":
				contentHide();
				$("#nav_platforms").addClass("active");
				$("#platforms").slideDown("400");
			break;
			
			case "trailer":
				contentHide();
				$("#nav_trailer").addClass("active");
				$("#trailer").slideDown("400");
			break;
		}
	}
	
	function contentHide(id)
	{
		// Remove active class from nav item
		$("#panelNav ul li a").each( function(index) { $(this).removeClass("active"); } );
		
		// Hide all panels
		$("#fanartScreens").slideUp("400");
		$("#fanartRibbon").slideUp("400");
		$("#screensRibbon").slideUp("400");
		$("#banners").slideUp("400");
		$("#bannerRibbon").slideUp("400");
		$("#platforms").slideUp("400");
		$("#trailer").slideUp("400");
	}
</script>
<!-- End #panelNav Scripts -->
	
<!-- Start Release Date Picker Script -->
<script type="text/javascript">
    $(document).ready(function(){
        $('#ReleaseDate').datepicker({ changeYear: true, yearRange: '1950:2020'  });
    });
</script>
<!-- End Release Date Picker Script -->

<!-- Start Rating Script -->
<script type="text/javascript">
	// User ratings (turns stars on and off)
    function UserRateArt(prefix,rating)  {
        for (var i=1; i<=10; i++)  {
            if (i <= rating)  {
                //var thisimage = eval("document.anchors." + prefix + i);
                //thisimage.style = 'background-image: url(<?= $baseurl ?>/images/game/star_on.png)';
				//alert("on");
                //alert(prefix + i);
				//document.getElementById(prefix + i).style.backgroundImage="url('<?= $baseurl ?>/images/game/star_on.png')";
				//alert(prefix + " - " + i);
				//$("#rating6728" + i).style.backgroundImage="url('<?= $baseurl ?>/images/game/star_on.png')";
            }
            else  {
                //var thisimage = eval("document.anchors." + prefix + i);
                //thisimage.style = 'background-image: url(<?= $baseurl ?>/images/game/star_off.png)';
				//alert("off");
				//document.getElementById(prefix + i).style.backgroundImage="url('<?= $baseurl ?>/images/game/star_off.png')";
				//$("#rating6728" + i).style.backgroundImage="url('<?= $baseurl ?>/images/game/star_off.png')";
            }
        }
    }
</script>
<!-- End Rating Script -->
	
<!-- Start Fanart nivoSlider -->
<script type="text/javascript">
    $(window).load(function() {
        $('#frontBoxartSlider').nivoSlider({animSpeed: 220, effect: 'fade'});
        $('#rearBoxartSlider').nivoSlider({animSpeed: 220, effect: 'fade'});
        $('#fanartSlider').nivoSlider({animSpeed: 220, effect: 'fade'});
        $('#screenSlider').nivoSlider({animSpeed: 220, effect: 'fade'});
        $('#bannerSlider').nivoSlider({animSpeed: 220, effect: 'fade'});
    });
</script>
<!-- End Fanart nivoSlider -->

<!-- Start jQuery Smooth Vertical Page Scrolling -->
<script type="text/javascript">
    $(document).ready(function() {  function filterPath(string) {  return string    .replace(/^\//,'')    .replace(/(index|default).[a-zA-Z]{3,4}$/,'')    .replace(/\/$/,'');  }  var locationPath = filterPath(location.pathname);  var scrollElem = scrollableElement('html', 'body');  $('a[href*=#]').each(function() {    var thisPath = filterPath(this.pathname) || locationPath;    if (  locationPath == thisPath    && (location.hostname == this.hostname || !this.hostname)    && this.hash.replace(/#/,'') ) {      var $target = $(this.hash), target = this.hash;      if (target) {        var targetOffset = $target.offset().top;        $(this).click(function(event) {          event.preventDefault();          $(scrollElem).animate({scrollTop: targetOffset}, 400, function() {            location.hash = target;          });        });      }    }  }); 
	// use the first element that is "scrollable"
	function scrollableElement(els) {    for (var i = 0, argLength = arguments.length; i <argLength; i++) {      var el = arguments[i],          $scrollElement = $(el);      if ($scrollElement.scrollTop()> 0) {        return el;      } else {        $scrollElement.scrollTop(1);        var isScrollable = $scrollElement.scrollTop()> 0;        $scrollElement.scrollTop(0);        if (isScrollable) {          return el;        }      }    }    return [];  }});
</script>
<!-- End jQuery Smooth Vertical Page Scrolling -->


<?php
	}
	else
	{
?>
		<h1>Oops!</h1>
		<h2 style="text-align: center;">We can't find the platform you requested...</h2>
		<p style="text-align: center;">If you believe you have recieved this message in error, please let us know.</p>
		<p style="text-align: center;"><a href="<?= $baseurl; ?>/" style="color: orange;">Click here to return to the homepage</a></p>
	</div>
</div>
<?php
	}
?>


<?php
	}
	//<END> IF LOGGED IN
	//AND IF NOT THEN DISPLAY LOGIN MESSAGE
	else
	{
?>
	<div id="gameWrapper">
		<div id="gameHead">
			
			<h1>Oops!</h1>
			<h2 style="text-align: center;">You must be logged in and be an administrator to edit a platform!</h2>
			<p style="text-align: center;">If you haven't already, please make an account with us and then log in.</p>
			<p style="text-align: center;"><a href="<?= $baseurl; ?>/login/" style="color: orange;">Click here to log in</a></p>
			
		</div>
	</div>
<?php } ?>