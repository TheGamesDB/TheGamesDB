<!DOCTYPE html >
<head>
	<meta charset="utf-8"/>
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" content="thegamesdb, the games db, games, database, meta, metadata, api, video, youtube, trailers, wallpapers, fanart, cover art, box art, fan art, open, source, game, search, forum," />
	<meta name="language" content="en-US" />
	<meta name="description" content="TheGamesDB is an open, online database for video game fans. We are driven by a strong community to provide the best place to find information, covers, backdrops screenshots and videos for games, both modern and classic." />
  
	<title>TheGamesDB.net - An open, online database for video game fans</title>
	
	<link rel="shortcut icon" href="<?= $baseurl ?>/favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/js/fullscreenslider/css/style.css"/>
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-ui/css/trontastic/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />
	<script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/jquery.tmpl.min.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/script.js"></script>
	<script type="text/JavaScript" src="<?php echo $baseurl; ?>/js/jquery-ui/js/jquery-ui-1.8.14.custom.min.js"></script>

	<!-- Start FaceBox Include -->
	<div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
	<!-- End FaceBox Include -->
	
	<!-- Start jQuery Snow Script -->
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-snowfall/styles.css" type="text/css" media="all" />
	<script src="<?php echo $baseurl; ?>/js/jquery-snowfall/snowfall.min.jquery.js" type="text/javascript"></script>
	<!-- End jQuery Snow Script -->
	
	<style type="text/css">
		body {
			background:#111111 url(<?php echo $baseurl; ?>/images/bg-main-background.jpg) repeat-x top center;
		}
		#frontHeader{
			color: #fff;
		}
		#frontHeader a{
			color: orange;
		}
		#frontnav a {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:link {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:visited {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:hover {
			color: #fff;
			text-decoration: underline;
		}
		.error { opacity: 0.7; font: bold 16px Helvetica, Arial, Sans-serif; text-shadow: 0px 2px 6px #333; color: red; width: 70%; margin: auto; margin-bottom: 20px; border: 2px solid #666; border-radius: 7px; padding: 15px; text-align: center; background: url(<?php echo $baseurl; ?>/images/common/bg_orange.png) repeat-x center center;}
		.message { opacity: 0.7; font: bold 16px Helvetica, Arial, Sans-serif; text-shadow: 0px 2px 6px #333; color: #fff; width: 70%; margin: auto; margin-bottom: 20px; border: 2px solid #666; border-radius: 7px; padding: 15px; text-align: center; background: url(<?php echo $baseurl; ?>/images/common/bg_orange.png) repeat-x center center;}
        
        input {
            height: 34px;
            font-size: 22px;
            line-height: 22px;
            margin: 0px;
            padding: 0px;
            border: 1px solid #fff;
            border-radius: 0px;
            background: url(<?php echo $baseurl; ?>/images/common/bg_glass.png) no-repeat center center;
            color: #fff;
        }
		
		.approve {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			color: #ffffff;
			padding: 6px 12px;
			background: -moz-linear-gradient(
				top,
				#c8ffbf 0%,
				#72cc72 25%,
				#22a800);
			background: -webkit-gradient(
				linear, left top, left bottom,
				from(#c8ffbf),
				color-stop(0.25, #72cc72),
				to(#22a800));
			-moz-border-radius: 11px;
			-webkit-border-radius: 11px;
			border-radius: 11px;
			border: 2px solid #ffffff;
			-moz-box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(43,255,0,1);
			-webkit-box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(43,255,0,1);
			box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(43,255,0,1);
			text-shadow:
				0px -1px 0px rgba(000,000,000,0.2),
				0px 1px 0px rgba(255,255,255,0.3);
			cursor: pointer;
		}
	</style>
	
	<?php
		$sql = "SELECT g.GameTitle, p.name, p.id AS platformid, p.icon, g.id, b.filename FROM games AS g, banners AS b, platforms AS p, ratings AS r WHERE r.itemid = b.id AND g.id = b.keyvalue AND r.itemtype = 'banner' AND b.keytype = 'fanart' AND g.platform = p.id GROUP BY g.GameTitle, p.name, g.id, b.filename    HAVING AVG(r.rating) = 10 ORDER BY RAND() LIMIT 6";
		$result = mysql_query($sql);
		if ($result !== FALSE) {
		$rows = mysql_num_rows($result);
	?>
	<script type="text/javascript">
		var photos = [
			<?php
				$colours = array("orange", "blue", "purple", "green", "red", "yellow");
				$colourCount = 0;
				$gameRowCount = 0;
				$imageUrls = array();
				
				// Include JPEG Reducer Class
                include('simpleimage50.php');
				
				while ($game = mysql_fetch_object($result)) {

					// Get Game Rating
					$ratingquery	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='game' AND itemid=$game->id";
					$ratingresult = mysql_query($ratingquery) or die('Query failed: ' . mysql_error());
					$rating = mysql_fetch_object($ratingresult);					
					
					if($gameRowCount != $rows - 1) 
					{
							// Recompress Fanart to 50% Jpeg Quality and save to front page image cache
							if(!file_exists("banners/_frontcache/$game->filename"))
							{
									$image = new SimpleImage();
									$image->load("banners/$game->filename");
									$image->save("banners/_frontcache/$game->filename");
							}
							
							$imageUrls[] = "banners/_frontcache/$game->filename";
					?>
							{
									"title" : "<?=$game->GameTitle?>",
									"cssclass" : "<?=$colours[$colourCount]?>",
									"image" : "banners/_frontcache/<?=$game->filename?>",
									"text" : "<?=$game->name?>",
									"icon" : "<?= $game->icon; ?>",
									"platformid" : "<?= $game->platformid; ?>",
									"rating" : "<?php for ($i = 2; $i <= 10; $i = $i + 2) {	if ($i <= $rating->average) { print '<img src=\'images/game/star_on.png\' width=15 height=15 border=0>'; }	else if ($rating->average > $i - 2 && $rating->average < $i) { print '<img src=\'images/game/star_half.png\' width=15 height=15 border=0>'; } else {	print '<img src=\'images/game/star_off.png\' width=15 height=15 border=0>'; } } ?>",
									"url" : '<?= $baseurl; ?>/game/<?=$game->id?>/',
									"urltext" : 'View Game'
							},
					<?php
							if($colourCount != 5)
							{
									$colourCount++;
							}
							else
							{
									$colourCount = 0;
							}
							$gameRowCount++;
					}
					else
					{
							// Recompress Fanart to 50% Jpeg Quality and save to front page image cache
							if(!file_exists("banners/_frontcache/$game->filename"))
							{
									$image = new SimpleImage();
									$image->load("banners/$game->filename");
									$image->save("banners/_frontcache/$game->filename");
							}
						
							$imageUrls[] = "banners/_frontcache/$game->filename";
					?>
							{
									"title" : "<?=$game->GameTitle?>",
									"cssclass" : "<?=$colours[$colourCount]?>",
									"image" : "banners/_frontcache/<?=$game->filename?>",
									"text" : "<?=$game->name?>",
									"icon" : "<?= $game->icon; ?>",
									"rating" : "<?php for ($i = 2; $i <= 10; $i = $i + 2) {	if ($i <= $rating->average) { print '<img src=\'images/game/star_on.png\' width=15 height=15 border=0>'; }	else if ($rating->average > $i - 2 && $rating->average < $i) { print '<img src=\'images/game/star_half.png\' width=15 height=15 border=0>'; } else {	print '<img src=\'images/game/star_off.png\' width=15 height=15 border=0>'; } } ?>",
									"url" : '<?= $baseurl; ?>/game/<?=$game->id?>/',
									"urltext" : 'View Game'
							}
					<?php
							if($colourCount != 2)
							{
									$colourCount++;
							}
							else
							{
									$colourCount = 0;
							}
					}
				}
	?>
		];
	</script>
	<?php
		}
	?>
</head>
<body>

	<div id="frontHeader" style="height: 78px; position: absolute; top 0px; width: 100%; z-index: 300; background: url(/images/bg_bannerws-thin-glass-strips.png) repeat-x center center;">
		<div id="frontBanner" style="width: 880px; margin: auto;">
			<p style="position: absolute; top: 10px; right: 15px; font-family:Arial; font-size:10pt;">
				<?php if ($loggedin) {
					$msgQuery = mysql_query(" SELECT id FROM messages WHERE status = 'new' AND messages.to = '$user->id' ");
					$msgCount = mysql_num_rows($msgQuery);
					?><a href="<?= $baseurl ?>/messages/">Messages</a> <?php if($msgCount > 0) { echo"<span style=\"color: Chartreuse;\">($msgCount)</span>"; } else { echo "($msgCount)"; } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/favorites/">Favorites</a> (<?php if($user->favorites != ""){ echo count(explode(",", $user->favorites)); } else{ echo "0"; } ?>) <span style="color: #ccc;">|</span> <?php if ($adminuserlevel == 'ADMINISTRATOR') { ?> <a href="<?= $baseurl ?>/admincp/">Admin Control Panel</a> <?php } else { ?><a href="<?= $baseurl ?>/userinfo/">My User Info</a><?php } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/?function=Log Out">Logout</a>
				<?php } else { ?>
					<a href="<?= $baseurl ?>/login/">Login</a> <span style="color: #ccc;">|</span> New to the site? <a href="<?= $baseurl ?>/register/">Register here!</a>
				<?php } ?>
			</p>
			<a href="<?php echo $baseurl; ?>/" title="An open database of video games">
				<img src="<?php echo $baseurl; ?>/images/bannerws-thin-glass-v2.png" style="border-width: 0px; padding: 12px 125px" />
			</a>
		</div>
	</div>
	
	<div style="position: absolute; top: 78px; background: url(<?php echo $baseurl; ?>/images/bg_banner-shadow.png) repeat-x center center; height: 15px; width: 100%; z-index: 200;"></div>
	
	<div id="messages" style="position: absolute; top: 160px; width: 100%;">
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	</div>
	
	<!-- Start Donation Box -->
	<!--<span  style="width: 244px; position: absolute; top: 17%; right: 2%; z-index: 200; padding: 12px; border: 1px solid #999; background: url(<?php echo $baseurl; ?>/images/bg_bannerws-thin-glass-strips.png); background-size: cover; border-radius: 12px;">
		<p style="text-align: center; font-size: 24px; padding: 12px; color: #FFF; font-family: sans-serif;">Funds Drive</p>
		<p style="padding-bottom: 12px; text-align: center; color: #FFF; font-family: sans-serif; font-size: 14px;">To keep our free service alive, please consider donating to our funds drive. Thank you!</p>
		<iframe src='http://gogetfunding.com/projects/widget/29570/6' width='240px' height='460px' frameborder='0' scrolling='no'></iframe>
		<p style="text-align: center; padding-top: 12px;"><a class="approve" href="http://gogetfunding.com/project/thegamesdb-net" target="_blank">Donate Here</a></p>
	</span>-->
	<!-- End Donation Box -->
	
	
	<div id="frontContentWrapper" style="position: absolute; top: 34%; width: 100%; height: 200px;  z-index: 200;">
	
		<div id="frontContent" style="opacity: 1; width: 600px; height: 160px; padding: 10px 30px; margin: auto; background: url(<?php echo $baseurl; ?>/images/bg_frontsearch.png) repeat-x center center; border-radius: 16px; border: 0px solid #333;">
		
			<h1 style="text-align: center; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size:26px; text-shadow: 0px 2px 6px #333; color:#fff; letter-spacing: 2px;">
			<?php
				$gamecountResult = mysql_query(" SELECT id FROM games ");
				$gamecount = mysql_num_rows($gamecountResult);
				echo number_format($gamecount) . " games and counting....";
			?>
			</h1>
			
			<div id="searchbox" style="padding: 16px 0px; text-align: center;">
				<form id="search" action="<?= $baseurl ?>/search/">
                    <div>
                        <input type="text" id="frontGameSearch" name="string" x-webkit-speech style="border-radius: 6px 0px 0px 6px; width: 450px;" /><input type="submit" value="Search" style="border-radius: 0px 6px 6px 0px; height: 36px; padding: 0px 5px 0px 5px;"  />
                        <!--<input id="frontGameSearch" name="string" type="text" style="height: 100%; padding: 0px; width: 440px; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 20px; text-shadow: 0px 2px 6px #666; color: #333; background: url(<?php echo $baseurl; ?>/images/common/bg_glass.png) no-repeat center center; color: #fff;  border: 1px solid #eee; margin-right: 0px; border-radius: 6px 0px 0px 6px;" />
                        <input type="submit" value="Search" style="height: 100%; padding: 0px 10px 0px 10px; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 22px; text-shadow: 0px 2px 6px #666; color: #333; background: url(<?php echo $baseurl; ?>/images/common/bg_glass.png) no-repeat center center; color: #fff;  border: 1px solid #eee; margin-left: 0px; border-radius: 0px 6px 6px 0px;" /> -->
                    </div>
					<input type="hidden" name="function" value="Search" />
				</form>
			</div>
			
			<div id="frontnav" style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 20px; text-shadow: 0px 2px 6px #666; color: #fff;">
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<? echo $baseurl; ?>/browse/">Games</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<? echo $baseurl; ?>/platforms/">Platforms</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<? echo $baseurl; ?>/stats/">Stats</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<? echo $baseurl; ?>/blog/">Blog</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="http://forums.thegamesdb.net" target="_blank">Forum</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="http://wiki.thegamesdb.net">Wiki</a></div>
				<div style="clear: both;"></div>
			</div>
			
		</div>
		
	</div>
	
	<div id="navigationBoxes">
		<!-- Navigation boxes will get injected by jQuery -->	
	</div>

	<div id="pictureSlider">
		<!-- Pictures will be injected by jQuery -->
	</div>
	
	<div id="footer" style="position:absolute; width: 100%; bottom:0px; z-index: 200; text-align: center;">
		<div id="footerbarShadow" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar-shadow.png) repeat-x center center; height: 15px;"></div>
		<div id="footerbar" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar.png) repeat-x center center; height: 30px;">
		
			<div id="Terms" style="padding-top: 5px; padding-left: 25px; float: left; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a href="<?=$baseurl?>/terms/" style="color: #333;">Terms &amp; Conditions</a>
			</div>
			
			<div id="theTeam" style="padding-top: 5px; padding-right: 25px; float: right; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a href="http://wiki.thegamesdb.net" style="color: #333;">TheGamesDB Wiki</a> | <a href="<?php echo $baseurl; ?>/showcase" style="color: #333;">Showcase</a>  
			</div>
			
			<div style="padding-top: 4px;">
			<a href="http://www.facebook.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png" alt="Visit us on Facebook" title="Visit us on Facebook" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png')" /></a>
			<a href="http://twitter.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png" alt="Visit us on Twitter" title="Visit us on Twitter" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png')" /></a>
			<a href="https://plus.google.com/116977810662942577082/posts" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/google_dark.png" alt="Visit us on Google Plus" title="Visit us on Google Plus"  style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_dark.png')" /></a>
			<a href="<?= $baseurl; ?>/mailshare.php?urlsubject=<?= urlencode("TheGamesDB.net - Home"); ?>&url=<?= urlencode($baseurl); ?>" rel="facebox"><img src="<?= $baseurl ?>/images/common/icons/social/24/share_dark.png" alt="Share via Email" title="Share via Email" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_dark.png')" /></a>
			</div>
			
		</div>
	</div>
	
	<div id="credits" style="display: none;">
	<div style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; text-shadow: 0px 2px 6px #666;">
		<h1>The Team</h1>
		<p>Here at TheGamesDB.net we have a small but very passionate and dedicated team.</p>
		<p>We are always striving to find ways to improve this site to provide our users with the best experience possible.</p>
		<p>&nbsp;</p>
		<p><strong>Owner:</strong> Scott Brant <em>(smidley)</em></p>
		<p><strong>Coding &amp; Design:</strong> Alex Nazaruk <em>(flexage)</em></p>
		<p><strong>Coding &amp; Design:</strong> Matt McLaughlin</p>
		<p>&nbsp;</p>
		<p>We would also like to give a big thanks to all our contributers, without your involvement this site wouldn't be as good as it is today.</p>
	</div>
	</div>
	<div style="display:none;">
		<?php
			for($i = 0; $i < count($imageUrls); $i++)
			{
			?>
				<img src="<?=$imageUrls[$i]?>" />
			<?php
			}
			?>
	</div>
	
	<script type="text/javascript">
		$(function() {
			var availableTags = [
				<?php
					if($titlesResult = mysql_query(" SELECT DISTINCT GameTitle FROM games ORDER BY GameTitle ASC; "))
					{
						while($titlesObj = mysql_fetch_object($titlesResult))
						{
							echo " \"$titlesObj->GameTitle\",\n";
						}
					}
				?>
			];
			$( "#frontGameSearch" ).autocomplete({
				source: availableTags,
				select: function(event, ui) { $("#search").submit(); }
			});
		});
	</script>
	
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-16803563-1']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	
	<script type="text/javascript">
		// jQuery Snow Script Instance
		// $(document).snowfall({ flakeCount : 200, maxSpeed : 10, round: true, shadow: true, collection: '#footer', minSize: 2, maxSize: 4 });
	</script>
	
</body>
</html>