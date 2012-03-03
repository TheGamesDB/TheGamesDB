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
		// Fetch Platform Information from DB
		if(isset($alias))
		{
			$alias = mysql_real_escape_string($alias);
			$query	= "SELECT p.* FROM platforms as p WHERE p.alias='$alias'";			
		}
		else
		{
			$id = mysql_real_escape_string($id);
			$query	= "SELECT p.* FROM platforms as p WHERE p.id=$id";
		}
		$result = mysql_query($query) or die('Fetch Platform Info Query Failed: ' . mysql_error());
		$rows = mysql_num_rows($result);
		$platform = mysql_fetch_object($result);
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
		
			<div id="gamePlatformIcon">
			</div>
			<div id="gameTitle">
			
				<?php	if ($loggedin == 1) {  ?>
					<span id ="gameUserLinks"><a href="<?=$baseurl?>/platform-edit/<?=$platform->id?>/"><img src="<?php echo $baseurl; ?>/images/common/icons/edit_128.png" style="width:16px; height: 16px; vertical-align: middle;" /></a>&nbsp;<a href="<?=$baseurl?>/platform-edit/<?=$platform->id?>/">Edit this Platform</a></span>
				<?php } ?><br /><br />
				
				<span style="float: right; padding-top: 16px;">
					
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
					<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= "$baseurl/platform/$platform->id/" ?>" data-text="<?= "$platform->name on TheGamesDB.net" ?>" data-count="horizontal" data-via="thegamesdb">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
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
					<a href="<?= $baseurl; ?>/mailshare.php?urlsubject=<?= urlencode("TheGamesDB.net - $platform->name"); ?>&url=<?= urlencode("$baseurl/platform/$platform->id/"); ?>" rel="facebox" style="float: right; margin-right: 10px; padding: 1px 6px 1px 3px; color: #fff; text-decoration: none; background-color: #333; border: 1px solid #444; border-radius: 3px; font-size: 11px; font-weight: bold;" onmouseover="$('#mailIcon').attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_active.png')" onmouseout="$('#mailIcon').attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_dark.png')"><img id="mailIcon" src="<?= $baseurl ?>/images/common/icons/social/24/share_dark.png" alt="Share via Email" title="Share via Email" style="vertical-align: middle; width: 18px; height: 18px;" />&nbsp;Share via Email</a>
					
				</span>
				
				<h1 style="margin: 0px; padding: 0px;"><img src="<?php echo $baseurl; ?>/images/common/consoles/png48/<?php echo $platform->icon; ?>" alt="<?php echo $platform->name; ?>" title="<?php echo $platform->name; ?>" style="vertical-align: middle;" />&nbsp;<?php echo $platform->name; ?></h1>
				<?php if(!empty($game->Alternates)) { ?>
					<h3><span style="color: #888; font-size: 13px;"><em>
				<?php echo "a.k.a. ' " . str_replace(",", ", ", $game->Alternates) . " ' "; ?>
					</em></span></h3>
				<?php } ?>
			</div>
			<div id="gameCoversWrapper">
				<div id="gameCovers">
					<?php
						if ($boxartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-boxart' LIMIT 1 "))
						{
							$boxart = mysql_fetch_object($boxartResult);
							if (!empty($boxart))
							{
								?>
									<img class="imgShadow" <?=imageResize("$baseurl/banners/$boxart->filename", "banners/_platformviewcache/$boxart->filename", 300, "width")?> alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
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
			</div>
			<div id="gameInfo">

				<div id="gameRating">
					<?php
						$query	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='platform' AND itemid=$platform->id";
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
								$query	= "SELECT rating FROM ratings WHERE itemtype='platform' AND itemid=$platform->id AND userid=$user->id";
								$result = mysql_query($query) or die('Query failed: ' . mysql_error());
								$rating = mysql_fetch_object($result);
								if (!$rating->rating) {
									$rating->rating = 0;
								}

								for ($i = 1; $i <= 10; $i++) {
									if ($i <= $rating->rating) {
										print "<a href=\"$baseurl/platform/$platform->id/?function=UserRating&type=platform&itemid=$platform->id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
									}
									else {
										print "<a href=\"$baseurl/platform/$platform->id/?function=UserRating&type=platform&itemid=$platform->id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating->rating)\"><img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
									}
								}
								?>
							<?php } ?>
				</div>
				<hr />
				<p><?php if (!empty($platform->overview)) { echo $platform->overview; } else { echo "\"No overview is currently available for this platform.\""; } ?></p>
				<hr />
				<?php
					if(!empty($platform->console))
					{
				?>
						<div id="consoleArt" style="float: left; width: 300px; padding: 6px; margin: 0px 3px;">
							<h3 class="grey">Console Art</h3>
							<img src="<?= $baseurl ?>/banners/platform/consoleart/<?= $platform->console ?>" alt="<?= $platform->name ?> Console Art" title="<?= $platform->name ?> Console Art" style="margin-top: 12px;"/>
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
						</div>
				<?php
					}
				?>
				<div style="clear: both;"></div>
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
					<p><span class="grey">Developer</span>&nbsp;&nbsp;<?php if (!empty($platform->developer)) { echo $platform->developer; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Manufacturer</span>&nbsp;&nbsp;<?php if (!empty($platform->manufacturer)) { echo $platform->manufacturer; } else { echo "N/A"; } ?></p>
					<p><span class="grey">CPU</span>&nbsp;&nbsp;<?php if (!empty($platform->cpu)) { echo $platform->cpu; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Memory</span>&nbsp;&nbsp;<?php if (!empty($platform->memory)) { echo $platform->memory; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Graphics</span>&nbsp;&nbsp;<?php if (!empty($platform->graphics)) { echo $platform->graphics; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Sound</span>&nbsp;&nbsp;<?php if (!empty($platform->sound)) { echo $platform->sound; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Display</span>&nbsp;&nbsp;<?php if (!empty($platform->display)) { echo $platform->display; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Media</span>&nbsp;&nbsp;<?php if (!empty($platform->media)) { echo $platform->media; } else { echo "N/A"; } ?></p>
					<p><span class="grey">Max. Controllers</span>&nbsp;&nbsp;<?php if (!empty($platform->maxcontrollers)) { echo $platform->maxcontrollers; } else { echo "N/A"; } ?></p>
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
						<li><a id="nav_fanartScreens" class="active" href="#gameContentTop" onclick="contentShow('fanartScreens');">Fanart</a></li>
						<li><a id="nav_banners" href="#gameContentTop" onclick="contentShow('banners');">Banners</a></li>
						<li><a id="nav_platforms" href="#gameContentTop" onclick="contentShow('platforms');">Top Rated Games</a></li>
						<li><a id="nav_trailer" href="#gameContentTop" onclick="contentShow('trailer');">Platform Trailer</a></li>
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
							if ($fanartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-fanart' "))
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
										<img  class="fanartSlide imgShadow" <?=imageResize("$baseurl/banners/$fanart->filename", "banners/_platformviewcache/$fanart->filename", 470, "width")?> alt="<?php echo $platform->name; ?> Fanart" title="<a href='<?="$baseurl/banners/$fanart->filename"?>' target='_blank'>View Full-Size</a>" />
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
							<div id="screensRibbon" style="position: absolute; width: 125px; height: 125px; background: url(<?= $baseurl ?>/images/game-view/ribbon-fanart.png) no-repeat; z-index: 10"></div>
							<?php
							if ($screenResult = mysql_query(" SELECT b.filename, AVG(r.rating) AS rating FROM games AS g, banners AS b, ratings AS r WHERE r.itemid = b.id AND g.id = b.keyvalue AND r.itemtype = 'banner' AND b.keytype = 'fanart' AND g.Platform = $platform->id GROUP BY b.filename HAVING AVG(r.rating) > 5 ORDER BY AVG(r.rating) DESC LIMIT 10 "))
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
										<img  class="screenSlide" <?=imageDualResize("$baseurl/banners/$screen->filename", "banners/_platformviewcache/$screen->filename", 470, 264)?> alt="<?php echo $platform->name; ?> Top-Rated Fanart" title="Top-Rated Game Fanart for this Platform | <a href='<?="$baseurl/banners/$screen->filename"?>' target='_blank'>View Full-Size</a>" />
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
						if ($bannerResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$platform->id' AND b.keytype = 'platform-banner' ") or die ("banner query failed" . mysql_error()))
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
									<img class="bannerSlide" src="<?="$baseurl/banners/$banner[filename]"?>" alt="<?php echo $platform->name; ?> Banner" />
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
					<div style="padding: 20px;">
							<h3 style="color: #fff; text-align: center;">Top Rated Games for <?= $platform->name ?></h3>
									<?php
										$sql = "SELECT DISTINCT g.GameTitle, AVG(r.rating) AS gamerating, g.id FROM games AS g, platforms AS p, ratings AS r WHERE g.Platform = $platform->id AND r.itemid = g.id AND r.itemtype = 'game'  GROUP BY g.GameTitle, g.id ORDER BY AVG(r.rating)  DESC LIMIT 6";
										$result = mysql_query($sql);
										if ($result != false) {
											$rows = mysql_num_rows($result);
											//echo "NUMBER: $rows<br />";
									?>
									<?php
											while($row = mysql_fetch_object($result))
											{
												$coverResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = $row->id AND b.keytype = 'boxart' AND b.filename LIKE '%front%' LIMIT 1 ");
												if ($coverResult != false)
												{
													$cover = mysql_fetch_object($coverResult)
													
													//echo "$row->id, $row->GameTitle, $row->name, " . (int)$row->gamerating . "/10, $cover->filename<br />";
													?>
														<div class="topGame" style="width: 240px; height: 270px; padding: 20px;  float: left; margin: 16px;">
															<div style="height: 202px; text-align: center;">
															<?php if($cover->filename != false) { ?>
																<a href="<?= $baseurl ?>/game/<?= $row->id ?>/"><img class="imgShadow" <?=imageDualResize("$baseurl/banners/$cover->filename", "banners/_platformviewcache/$cover->filename", 200, 200)?> style="border: 1px solid #FFF" alt="<?= $row->GameTitle ?>" title="<?= $row->GameTitle ?>" /></a>
																<?php } else { ?>
																<a href="<?= $baseurl ?>/game/<?= $row->id ?>/"><img class="imgShadow" src="<?= $baseurl ?>/images/common/placeholders/boxart_blank.png" style="width: 140px; height: 200px; border: 1px solid #FFF" alt="<?= $row->GameTitle ?>" title="<?= $row->GameTitle ?>" /></a>
																<?php } ?>
															</div>
															<div style="text-align: center;">
																<p><a href="<?= $baseurl ?>/game/<?= $row->id ?>/"><?= $row->GameTitle ?></a><br />
																Site Rating: <?= (int)$row->gamerating ?>/10</p>
															</div>
														</div>
													<?php
												}
											}
									?>
											
									<?php
										}
									?>
						</div>
					<!-- </div> -->
					<div style="clear: both;"></div>
				</div>
				
				<div id="trailer">
					<?php if ($platform->youtube != "") { ?>
					<div style="margin: auto; width: 853px; box-shadow: 0px 0px 22px #000;">
							<iframe width="853" height="510" src="http://www.youtube.com/embed/<?=str_replace("&hd=1", "", str_replace("?hd=1", "", "$platform->youtube")) . "?hd=1"?>" frameborder="0" allowfullscreen></iframe>
							<div style="clear: both;"></div>
					</div>
					<?php } else { ?>
					<div style="margin: auto; width: 500px; box-shadow: 0px 0px 22px #000; border-radius: 16px; background-color: #1e1e1e;">
							<p style="color: #fff; font-size: 18px; text-shadow: 0px 0px 5px #000; text-align: center; padding: 125px 10px;">This platform does not currently have a trailer added.</p>
					</div>
					<?php } ?>
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
			<h2 style="text-align: center;">We can't find the platform you requested...</h2>
			<p style="text-align: center;">If you believe you have recieved this message in error, please let us know.</p>
			<p style="text-align: center;"><a href="<?= $baseurl; ?>/" style="color: orange;">Click here to return to the homepage</a></p>
		</div>
	</div>
	<?php
		}
	?>