<?php
	include('simpleimage.php');
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
?>

<?php
	// Fetch Game Information from DB
	$id = mysql_real_escape_string($id);
	$query	= "SELECT g.*, p.name as PlatformName, p.icon as PlatformIcon FROM games as g, platforms as p WHERE g.id=$id AND g.Platform = p.id";
	$result = mysql_query($query) or die('Fetch Game Info Query Failed: ' . mysql_error());
	$rows = mysql_num_rows($result);
	$game = mysql_fetch_object($result);
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
	
		<div id="gameTitle">
			<?php	if ($loggedin == 1) {  ?>
				<span id ="gameUserLinks"><a href="<?=$baseurl?>?tab=game-edit&id=<?=$game->id?>"><img src="<?php echo $baseurl; ?>/images/common/icons/edit_128.png" style="width:16px; height: 16px; vertical-align: middle;" /></a>&nbsp;<a href="<?=$baseurl?>/game-edit/<?=$game->id?>/">Edit this Game</a>&nbsp;&nbsp;|&nbsp;
				<?php	## First, generate their userfavorites array
					$userfavorites = explode(",", $user->favorites);

					## If the user has this as a favorite, display a message and a button
					## to "Un-favorite".
					if (in_array($id, $userfavorites, 1)) {
						print "<a href=\"/?function=ToggleFavorite&id=$id\"><img src=\"$baseurl/images/common/icons/favorite_48.png\" style=\"width:16px; height: 16px; vertical-align: middle;\" /></a>&nbsp;<a href=\"/?function=ToggleFavorite&id=$id\">Unfavorite this Game</a>";
					}
					## If the user doesn't have this as a favorite, display a button to
					## mark it as a favorite.
					else {
						print "<a href=\"/?function=ToggleFavorite&id=$id\"><img src=\"$baseurl/images/common/icons/favorite_48.png\" style=\"width:16px; height: 16px; vertical-align: middle;\" /></a>&nbsp;<a href=\"/?function=ToggleFavorite&id=$id\">Favorite this Game</a>";
					}
				?>
			<?php } ?></span>
			<h1 style="margin: 0px; padding: 0px;"><?php echo $game->GameTitle; ?></h1>
			<?php if(!empty($game->Alternates)) { ?>
				<h3><span style="color: #888; font-size: 13px;"><em>
			<?php echo "a.k.a. ' " . str_replace(",", ", ", $game->Alternates) . " ' "; ?>
				</em></span></h3>
			<?php } ?>
		</div>
		<div id="gameCoversWrapper">
			<div id="gameCovers">
				<?php
					if ($frontCoverResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
					{
						$front = mysql_fetch_object($frontCoverResult);
						if (!empty($front))
						{
							?>
							<img id="frontCover" class="frontCover imgShadow" <?=imageResize("$baseurl/banners/$front->filename", "banners/_gameviewcache/$front->filename", 300, "width")?> alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
							<?php
							if ($backCoverResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.filename LIKE '%boxart%back%' LIMIT 1 "))
							{
								$back = mysql_fetch_object($backCoverResult);
								if (!empty($back))
								{
								?>
								<img  id="backCover" class="backCover imgShadow" style="display: none;" <?=imageResize("$baseurl/banners/$back->filename", "banners/_gameviewcache/$back->filename", 300, "width")?> alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
								<?php
								}
							}
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
			<p style="text-align: center; font-size: 15px;">
			<?php
			if (!empty($front) && !empty($back))
			{
			?>
			<a href="javascript: void();" class="gameCoversFlip"><img src="<?php echo $baseurl; ?>/images/common/icons/flip_32.png" style="width:24px; height: 24px; vertical-align: -7px;" /></a>&nbsp;<a href="javascript: void();" class="gameCoversFlip">Flip</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php
			}
			if (!empty($front))
			{
			?>
			<a href="<?="$baseurl/banners/$front->filename"?>" target="_blank"><img src="<?php echo $baseurl; ?>/images/common/icons/expand_48.png" style="width:24px; height: 24px; vertical-align: -6px;" /></a>&nbsp;<a href="<?="$baseurl/banners/$front->filename"?>" target="_blank">Front</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php
			}
			if (!empty($back))
			{
			?>
			<a href="<?="$baseurl/banners/$back->filename"?>" target="_blank"><img src="<?php echo $baseurl; ?>/images/common/icons/expand_48.png" style="width:24px; height: 24px; vertical-align: -6px;" /></a>&nbsp;<a href="<?="$baseurl/banners/$back->filename"?>" target="_blank">Back</a></p>
			<?php
			}
			?>
		</div>
		<div id="gameInfo">
			<span style="float: right;">
				
				<!-- Google plus share button -->
				<span style="float: right;">
				<!-- Place this tag where you want the +1 button to render -->
				<g:plusone size="medium"></g:plusone>

				<!-- Place this render call where appropriate -->
				<script type="text/javascript">
				  (function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				  })();
				</script>
				</span>
				
				<!-- Twitter share button -->
				<span style="float: right;">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= "$baseurl/game/$game->id/" ?>" data-text="<?= "$game->GameTitle on TheGamesDB.net" ?>" data-count="horizontal" data-via="thegamesdb">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
				</span>
				
				<!-- Facebook share button -->
				<span style="float: right; padding-top: 1px;">
				<a name="fb_share"></a> 
				<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" 
						type="text/javascript">
				</script>
				&nbsp;
				</span>
				
				<!-- Share via Email button -->
				<a href="<?= $baseurl; ?>/mailshare.php?urlsubject=<?= urlencode("TheGamesDB.net - $game->GameTitle"); ?>&url=<?= urlencode("$baseurl/game/$game->id/"); ?>" rel="facebox" style="float: right; margin-right: 10px; padding: 1px 6px 1px 3px; color: #fff; text-decoration: none; background-color: #333; border: 1px solid #444; border-radius: 3px; font-size: 11px; font-weight: bold;" onmouseover="$('#mailIcon').attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_active.png')" onmouseout="$('#mailIcon').attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_dark.png')"><img id="mailIcon" src="<?= $baseurl ?>/images/common/icons/social/24/share_dark.png" alt="Share via Email" title="Share via Email" style="vertical-align: middle; width: 18px; height: 18px;" />&nbsp;Share via Email</a>
				
			</span>
			
			<h2><img src="<?php echo $baseurl; ?>/images/common/consoles/png32/<?php echo $game->PlatformIcon; ?>" alt="<?php echo $game->PlatformName; ?>" title="<?php echo $game->PlatformName; ?>" style="vertical-align: -8px;" />&nbsp;<?php if (!empty($game->PlatformName)) { ?>
			<a style="color: #fff;" href="<?= $baseurl ?>/platform/<?= $game->Platform ?>/"><?= $game->PlatformName ?></a>
			<?php } else { echo "N/A"; } ?></h2>
			<hr />
			<div id="gameRating">
				<?php
					$query	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='game' AND itemid=$id";
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
							$query	= "SELECT rating FROM ratings WHERE itemtype='game' AND itemid=$id AND userid=$user->id";
							$result = mysql_query($query) or die('Query failed: ' . mysql_error());
							$rating = mysql_fetch_object($result);
							if (!$rating->rating) {
								$rating->rating = 0;
							}

							for ($i = 1; $i <= 10; $i++) {
								if ($i <= $rating->rating) {
									print "<a href=\"$baseurl/game/$id/?function=UserRating&type=game&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
								else {
									print "<a href=\"$baseurl/game/$id/?function=UserRating&type=game&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
							}
							?>
						<?php } ?>
			</div>
			<hr />
			<p><?php if (!empty($game->Overview)) { echo $game->Overview; } else { echo "\"No overview is currently available for this title.\""; } ?></p>
			<hr />
			<div id="gameVitals">
				<div id="esrbIcon" style ="float: right; width: 72px; height: 100px;">
					<?php
						switch($game->Rating)
						{
							case "EC - Early Childhood":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-ec.png" />
								<?php
							break;
							
							case "E - Everyone":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-everyone.png" />
								<?php
							break;
							
							case "E10+ - Everyone 10+":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-e10.png" />
								<?php
							break;
							
							case "T - Teen":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-teen.png" />
								<?php
							break;
							
							case "M - Mature":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-mature.png" />
								<?php
							break;
							
							case "RP - Rating Pending":
								?>
									<img src="<?= $baseurl ?>/images/game-view/esrb/esrb-rp.png" />
								<?php
							break;
						}
					?>
				</div>
				<p><span class="grey">Players:</span>&nbsp;&nbsp;<?php if (!empty($game->Players)) { echo $game->Players; } else { echo "N/A"; } ?>
					<span class="grey" style="padding-left: 20px;">Co-op:</span>&nbsp;&nbsp;<?php if($game->coop != false) { echo $game->coop; } else { echo "N/A"; } ?><br />
				<span class="grey">Genres:</span>&nbsp;&nbsp;<?php if (!empty($game->Genre)) { 
					$genres = explode("|", $game->Genre);
					$genreCount = 1; 
					while($genreCount < count($genres) - 1)
					{
						echo $genres[$genreCount];
						if ($genreCount < count($genres) - 2)
						{
							echo ", ";
						}
						$genreCount++;
						}
					}
					else { echo "N/A"; } ?><br />
				<span class="grey">Release Date:</span>&nbsp;&nbsp;<?php if (!empty($game->ReleaseDate)) { echo $game->ReleaseDate; } else { echo "N/A"; } ?><br /><br />
				
				<?php
				// Start Developer Logo Replacement
				if (!empty($game->Developer))
				{ 
					$developerBool = false;
					$devArray = explode(" ", $game->Developer);
					$i = 0;
					
					for($i = 0; $i < count($devArray); $i++)
					{
						$developerQuery = mysql_query(" SELECT logo FROM publishers WHERE keyword LIKE '%$devArray[$i]%' ");
						if($developerQuery)
						{
							if(mysql_num_rows($developerQuery) != 0)
							{
								$developerResult = mysql_fetch_object($developerQuery);
								$developerBool = true;
								$i = count($devArray);
							}
						}
					}
					if($developerBool == true)
					{
						if(!file_exists("banners/_gameviewcache/publishers/$developerResult->logo"))
						{
							WideImage::load("banners/publishers/$developerResult->logo")->resize(400, 60)->saveToFile("banners/_gameviewcache/publishers/$developerResult->logo");
						}
					?>
						<span class="grey">Developer:</span><br /><img src="<?= $baseurl; ?>/banners/_gameviewcache/publishers/<?= $developerResult->logo; ?>" alt="<?= $game->Developer; ?>" title="<?= $game->Developer; ?>" style="vertical-align: middle; padding-bottom: 14px; padding-top: 4px;" /><br />
					<?php
					}
					else
					{
					?>
						<span class="grey">Developer:</span>&nbsp;&nbsp;<?php if (!empty($game->Developer)) { echo $game->Developer; } else { echo "N/A"; } ?><br />
					<?php
					}
				}
				else
				{
				?>
					<span class="grey">Developer:</span>&nbsp;&nbsp;<?php if (!empty($game->Developer)) { echo $game->Developer; } else { echo "N/A"; } ?><br />
				<?php
				}
				?>
				
				<?php
				// Start Publisher Logo Replacement
				if (!empty($game->Publisher))
				{
					$publisherBool = false;
					$pubArray = explode(" ", $game->Publisher);
					$i = 0;
					
					for($i = 0; $i < count($pubArray); $i++)
					{
						$publisherQuery = mysql_query(" SELECT logo FROM publishers WHERE keyword LIKE '%$pubArray[$i]%' ");
						if($publisherQuery)
						{
							if(mysql_num_rows($publisherQuery) != 0)
							{
								$publisherResult = mysql_fetch_object($publisherQuery);
								$publisherBool = true;
								$i = count($pubArray);
							}
						}
					}
					if($publisherBool == true)
					{
						if(!file_exists("banners/_gameviewcache/publishers/$publisherResult->logo"))
						{
							WideImage::load("banners/publishers/$publisherResult->logo")->resize(400, 60)->saveToFile("banners/_gameviewcache/publishers/$publisherResult->logo");
						}
					?>
						<span class="grey">Publisher:</span><br /><img src="<?= $baseurl; ?>/banners/_gameviewcache/publishers/<?= $publisherResult->logo; ?>" alt="<?= $game->Publisher; ?>" title="<?= $game->Publisher; ?>" style="vertical-align: middle; padding-bottom: 14px; padding-top: 4px;" />
					<?php
					}
					else
					{
					?>
						<span class="grey">Publisher:</span>&nbsp;&nbsp;<?php if (!empty($game->Publisher)) { echo $game->Publisher; } else { echo "N/A"; } ?>
					<?php
					}
				}
				else
				{
				?>
					<span class="grey">Publisher:</span>&nbsp;&nbsp;<?php if (!empty($game->Publisher)) { echo $game->Publisher; } else { echo "N/A"; } ?>
				<?php
				}
				?>
				
				</p>
				<div style="clear: both;"></div>
			</div>
			<?php if($game->Platform == 1 || $game->Platform == 37) { ?>
			<hr />
				<div id="sysReq">
					<p><span class="grey">System Requirements</span></p>
					<p><span class="grey">OS:</span> <?php if($game->os == ""){echo "N/A";} else{echo $game->os;} ?><br />
					<span class="grey">Processor:</span> <?php if($game->processor == ""){echo "N/A";} else{echo $game->processor;} ?><br />
					<span class="grey">RAM:</span> <?php if($game->ram == ""){echo "N/A";} else{echo $game->ram;} ?><br />
					<span class="grey">Hard Drive:</span> <?php if($game->hdd == ""){echo "N/A";} else{echo $game->hdd;} ?><br />
					<span class="grey">Video:</span> <?php if($game->video == ""){echo "N/A";} else{echo $game->video;} ?><br />
					<span class="grey">Sound:</span> <?php if($game->sound == ""){echo "N/A";} else{echo $game->sound;} ?></p>
				</div>
			<? } ?>
			
		</div>
		<div style="clear:both"></div>
	</div>
	
	<div id="gameContent">
		<div id="gameContentTop">
		
			<div id="panelNav">
				<ul>
					<li><a id="nav_fanartScreens" class="active" href="#gameContentTop" onclick="contentShow('fanartScreens');">Fanart &amp; Screenshots</a></li>
					<li><a id="nav_banners" href="#gameContentTop" onclick="contentShow('banners');">Banners</a></li>
					<li><a id="nav_platforms" href="#gameContentTop" onclick="contentShow('platforms');">Other Platforms</a></li>
					<li><a id="nav_trailer" href="#gameContentTop" onclick="contentShow('trailer');">Game Trailer</a></li>
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
						if ($fanartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'fanart' "))
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
									<img  class="fanartSlide imgShadow" <?=imageResize("$baseurl/banners/$fanart->filename", "banners/_gameviewcache/$fanart->filename", 470, "width")?> alt="<?php echo $game->GameTitle; ?> Fanart" title="<a href='<?="$baseurl/banners/$fanart->filename"?>' target='_blank'>View Full-Size</a> | <a href='<?= $baseurl; ?>/game-fanart-slideshow.php?id=<?=$game->id?>' target='_blank'>Full-screen Slideshow</a>" />
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
					
					<div class="slider-wrapper theme-default">
						<div id="screensRibbon" style="position: absolute; width: 125px; height: 125px; background: url(<?= $baseurl ?>/images/game-view/ribbon-screens.png) no-repeat; z-index: 10"></div>
						<?php
						if ($screenResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'screenshot' "))
						{
							if(mysql_num_rows($screenResult) > 0)
							{
							?>
							<div id="screenSlider" class="nivoSlider">
							<?php
								$screenSlideCount = 0;
								while($screen = mysql_fetch_object($screenResult))
								{	
							?>
									<img  class="screenSlide" <?=imageDualResize("$baseurl/banners/$screen->filename", "banners/_gameviewcache/$screen->filename", 470, 264)?> alt="<?php echo $game->GameTitle; ?> Screenshot" title="<a href='<?="$baseurl/banners/$screen->filename"?>' target='_blank'>View Full-Size</a>" />
							<?php
									$screenSlideCount++;
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

				<div style="clear: both;"></div>

			</div>
			
			<div id="banners">
				<div class="slider-wrapper theme-default">
					<div id="bannerRibbon" style="display: none; position: absolute; width: 125px; height: 125px; background: url(<?= $baseurl ?>/images/game-view/ribbon-banners.png) no-repeat; z-index: 10"></div>
					<?php
					if ($bannerResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'series' ") or die ("banner query failed" . mysql_error()))
					{
						if(mysql_num_rows($bannerResult) > 0)
						{
						?>
							<div id="bannerSlider" class="nivoSlider" style="width:760px important; height: 140px !important;">
						<?php
							$bannerSlideCount = 0;
							while($banner = mysql_fetch_array($bannerResult))
							{	
						?>
								<img class="bannerSlide" src="<?="$baseurl/banners/$banner[filename]"?>" width="760" height="140" alt="<?php echo $game->GameTitle; ?> Banner" />
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
				<div style="margin: auto; width: 500px; box-shadow: 0px 0px 22px #000; border-radius: 16px; background-color: #1e1e1e; text-align: center; margin-top: 20px;">
					<div style="padding: 20px;">
						<h3 style="color: #fff;">Other Platforms with this Game</h3>
						<?php
							$similarResult = mysql_query(" SELECT g.id, g.platform, g.GameTitle, p.name, p.icon FROM games as g, platforms as p WHERE g.GameTitle = \"$game->GameTitle\" AND g.Platform <> '$game->Platform' AND g.Platform = p.id ORDER BY p.name");
							$similarRowCount = mysql_num_rows($similarResult);
							if($similarRowCount > 0)
							{
								?>
								<p>This game exists on <?=$similarRowCount?> other platforms.</p>
								<?php
								while($similarRow = mysql_fetch_assoc($similarResult))
								{
									?>
										<div style="margin-top: 10px; font-size: 16px;"><img src="<?=$baseurl?>/images/common/consoles/png32/<?=$similarRow['icon']?>" alt="<?=$similarRow['name']?>" style="vertical-align: -8px;" />&nbsp;&nbsp;<a href="<?=$baseurl?>?tab=game&id=<?=$similarRow['id']?>"><?=$similarRow['name']?> - <?=$similarRow['GameTitle']?></a></div>
									<?php
								}
								?>
								<p>If you know this game exists on another platform, why not <a href="<?=$baseurl?>?tab=addgame&passTitle=<?=urlencode($game->GameTitle)?>">add it</a>.</p>
								<?php
							}
							else
							{
								?>
								<p>There are currently no other platforms that have this game yet...</p>
								<p>If you know of one, why not <a href="<?=$baseurl?>?tab=addgame&passTitle=<?=urlencode($game->GameTitle)?>">add it</a>.</p>
								<?php
							}
						?>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			
			<div id="trailer">
				<?php if ($game->Youtube != "") { ?>
				<div style="margin: auto; width: 853px; box-shadow: 0px 0px 22px #000;">
					<iframe width="853" height="510" src="http://www.youtube.com/embed/<?=str_replace("&hd=1", "", str_replace("?hd=1", "", "$game->Youtube")) . "?hd=1"?>" frameborder="0" allowfullscreen></iframe>
					<div style="clear: both;"></div>
				</div>
				<?php } else { ?>
				<div style="margin: auto; width: 500px; box-shadow: 0px 0px 22px #000; border-radius: 16px; background-color: #1e1e1e;">
					<p style="color: #fff; font-size: 18px; text-shadow: 0px 0px 5px #000; text-align: center; padding: 125px 10px;">This game does not currently have a trailer added.</p>
				</div>
				<?php } ?>
			</div>
		
		</div>
		
		<div style="clear: both;"></div>
		
		<div id="gameContentBottom">

			<div style="text-align: center;"><a style="font-size: 18px; color: #fff; text-decoration: none; text-shadow: 0px 0px 10px #000;" href="#gameContentBottom"  onclick="$('#comments').slideToggle();">Comments&nbsp;&nbsp;<img style="vertical-align: middle;" src="<?= $baseurl; ?>/images/common/icons/collapse-alt_16.png" alt="Show Comments" title="Show Comments" /></a></div>
			
			<hr style="margin: 10px 0px 14px 0px;" />

			<div id="comments" style="display: none;">
					<?php
						$commentsQuery = mysql_query(" SELECT c.*, u.username FROM comments AS c , users AS u WHERE c.gameid='$game->id' AND c.userid = u.id ORDER BY c.timestamp ASC");
						if(mysql_num_rows($commentsQuery))
						{
							while($comments = mysql_fetch_object($commentsQuery))
							{
					?>
								<div class="comment">
								<?php
								$filename = glob("banners/users/" . $comments->userid . "-*.jpg");
								if(file_exists($filename[0]))
								{
								?>
									<div style="float: left; width: 64px; height: 64px; padding: 0px 15px 15px 0px; text-align: center;"><img src="<?= $baseurl; ?>/<?= $filename[0]; ?>" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" /></div>
								<?php
									$filename = null;
								}
								else
								{
								?>
									<img style="float: left; padding: 0px 15px 5px 0px;" src="<?=$baseurl; ?>/images/common/icons/user-black_64.png" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" />
								<?php
								}
								?>
									<span style="float: right;"><?= date("l, jS F Y - g:i A (T)", strtotime($comments->timestamp)) ?></span>
									<h2><?= $comments->username; ?> says...</h2>
									<p><?= $comments->comment; ?></p>
									<?php
										if($comments->userid == $user->id || $adminuserlevel == 'ADMINISTRATOR')
										{
									?>
											<p style="text-align: right;"><a href="<?= $baseurl; ?>/game/<?= $game->id; ?>/?function=Delete+Game+Comment&commentid=<?= $comments->id; ?>">Delete Comment</a></p>
									<?php
										}
									?>
									<div style="clear: both;"></div>
								</div>
					<?php
							}
							if($loggedin == 1)
							{
					?>
							<div class="comment">
								<?php
								$filename = glob("banners/users/" . $comments->userid . "-*.jpg");
								if(file_exists($filename[0]))
								{
								?>
									<div style="float: left; width: 64px; height: 64px; padding: 0px 15px 15px 0px; text-align: center;"><img src="<?= $baseurl; ?>/<?= $filename[0]; ?>" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" /></div>
								<?php
									$filename = null;
								}
								else
								{
								?>
									<img style="float: left; padding: 0px 15px 5px 0px;" src="<?=$baseurl; ?>/images/common/icons/user-black_64.png" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" />
								<?php
								}
								?>
								<h2>Leave a comment...</h2>
								<p>Comments are plain-text only: bb-code, html and so forth are not allowed.</p>
								<form method="post" action="<?= $baseurl; ?>/game/<?= $game->id; ?>/">
									<textarea name="comment" style="width: 100%; height: 60px;"></textarea>
									<input type="hidden" name="userid" value="<?= $user->id; ?>" />
									<input type="hidden" name="gameid" value="<?= $game->id; ?>" />
									<input type="hidden" name="function" value="Add Game Comment" />
									<p style="text-align: right;"><input type="submit" name="button" value="Leave Comment..." /></p>
								</form>
								<div style="clear: both;"></div>
							</div>
					<?php
							}
							else
							{
					?>
							<div class="comment">
								<?php
								$filename = glob("banners/users/" . $comments->userid . "-*.jpg");
								if(file_exists($filename[0]))
								{
								?>
									<div style="float: left; width: 64px; height: 64px; padding: 0px 15px 15px 0px; text-align: center;"><img src="<?= $baseurl; ?>/<?= $filename[0]; ?>" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" /></div>
								<?php
									$filename = null;
								}
								else
								{
								?>
									<img style="float: left; padding: 0px 15px 5px 0px;" src="<?=$baseurl; ?>/images/common/icons/user-black_64.png" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" />
								<?php
								}
								?>
									<h2>Leave a comment...</h2>
									<p>Comments are plain-text only: bb-code, html and so forth are not allowed.</p>
									<div style="clear: both;"></div>
									<p style="font-size: 14px; text-align: center"><em>You must be logged in to leave a comment,<br />click <a href="<?= $baseurl; ?>/login/">here</a> to log in...</em></p>
									<div style="clear: both;"></div>
								</div>
					<?php
							}
						}
						else
						{
					?>
							<div class="comment">
								<?php
								$filename = glob("banners/users/" . $comments->userid . "-*.jpg");
								if(file_exists($filename[0]))
								{
								?>
									<div style="float: left; width: 64px; height: 64px; padding: 0px 15px 15px 0px; text-align: center;"><img src="<?= $baseurl; ?>/<?= $filename[0]; ?>" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" /></div>
								<?php
									$filename = null;
								}
								else
								{
								?>
									<img style="float: left; padding: 0px 15px 5px 0px;" src="<?=$baseurl; ?>/images/common/icons/user-black_64.png" alt="<?= $comments->username; ?>" title="<?= $comments->username; ?>" />
								<?php
								}
								?>
								<h2>No one has left a comment yet...</h2>
								<p>Be the first to leave a comment!</p>
								<?php
									if ($loggedin == 1)
									{
								?>
										<p>Comments are plain-text only: bb-code, html and so forth are not allowed.</p>
										<form method="post" action="<?= $baseurl; ?>/game/<?= $game->id; ?>/">
											<textarea name="comment" style="width: 100%; height: 60px;"></textarea>
											<input type="hidden" name="userid" value="<?= $user->id; ?>" />
											<input type="hidden" name="gameid" value="<?= $game->id; ?>" />
											<input type="hidden" name="function" value="Add Game Comment" />
											<p style="text-align: right;"><input type="submit" name="button" value="Leave Comment..." /></p>
										</form>
								<?php
									}
									else
									{
									?>
										<div style="clear: both;"></div>
										<p style="font-size: 14px; text-align: center"><em>You must be logged in to leave a comment,<br />click <a href="<?= $baseurl; ?>/login/">here</a> to log in...</em></p>
									<?php
									}
								?>
								<div style="clear: both;"></div>
							</div>
					<?php
						}
					?>
				<div style="width: 96%; margin: auto; background: #333; box-shadow: 0px 0px 22px #000; border-radius: 16px; text-align: center;">
				</div>
				<div style="clear: both;"></div>
			</div>
			
		</div>
		
		<!--
		<div id="gameFooter">
		
		</div>
		-->

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
	
	
	
<!-- Start Boxart Flip Script -->
<script type="text/javascript">
	$('#frontCover').css({display: "none"});
	
	$(document).ready(function(){
		$('#frontCover').fadeIn(2000);
	});	
</script>

<?php
	if (!empty($back))
	{
?>
<script type="text/javascript">
	$('#gameCovers').css({cursor : "pointer"});

	$(document).ready(function(){		
		$('.gameCoversFlip').bind("click",function(){		 
			var elem = $('#gameCovers');
			 
			if(elem.data('flipped'))
			{
				elem.revertFlip();
				elem.data('flipped',false)
			}
			else
			{
				var frontWidth = $("#frontCover").attr("width");
				var frontHeight = $("#frontCover").attr("height");
				elem.flip({
				direction:'rl',
				speed: 350,
				color: "#ff9000",
				content: "<img class=\"imgShadow\" src=\"" + $("#backCover").attr("src") + "\" width=\"" + frontWidth + "\" height=\"" + frontHeight + "\" />"
				});
				elem.data('flipped',true);
			}
		});	
		$('#gameCovers').bind("click",function(){		 
			var elem = $(this);
			 
			if(elem.data('flipped'))
			{
				elem.revertFlip();
				elem.data('flipped',false)
			}
			else
			{
				var frontWidth = $("#frontCover").attr("width");
				var frontHeight = $("#frontCover").attr("height");
				elem.flip({
				direction:'rl',
				speed: 350,
				color: "#ff9000",
				content: "<img class=\"imgShadow\" src=\"" + $("#backCover").attr("src") + "\" width=\"" + frontWidth + "\" height=\"" + frontHeight + "\" />"
				});
				elem.data('flipped',true);
			}
		});	
	});
</script>
<!-- End Boxart Flip Script -->
<?php
	}
?>

<!-- Start Fanart nivoSlider -->
<script type="text/javascript">
    $(window).load(function() {
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
		<h2 style="text-align: center;">We can't find the game you requested...</h2>
		<p style="text-align: center;">If you believe you have recieved this message in error, please let us know.</p>
		<p style="text-align: center;"><a href="<?= $baseurl; ?>/" style="color: orange;">Click here to return to the homepage</a></p>
	</div>
</div>
<?php
	}
?>