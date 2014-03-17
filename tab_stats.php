
	<div id="gameHead">
	
		<?php
			//Google AdSense - Right of Main Content Skyscraper
			include_once("adverts/adsense-leaderboard_content-top.php");
		?>
		
		<h1 style="text-align: center;">Database Completion Overview</h1>
		
		<?php
			// Get Total Number Of Games
			$totalGamesQuery = mysql_query("SELECT `id` from `games`");
			$totalGamesCount = mysql_num_rows($totalGamesQuery);
			
			// Get Number of Games Having Front Boxart
			$frontBoxartQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') AND games.Platform = platforms.id ");
			$frontBoxartCount = mysql_num_rows($frontBoxartQuery);
			
			// Get Number of Games Having Rear Boxart
			$rearBoxartQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%back%') AND games.Platform = platforms.id ");
			$rearBoxartCount = mysql_num_rows($rearBoxartQuery);
			
			// Get Number of Games Having Fanart
			$fanartQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'fanart') AND games.Platform = platforms.id ");
			$fanartCount = mysql_num_rows($fanartQuery);
			
			// Get Number of Games Having ClearLOGOs
			$clearLogoQuery = mysql_query("  SELECT games.id FROM games, platforms WHERE  EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'clearlogo') AND games.Platform = platforms.id ");
			$clearLogoCount = mysql_num_rows($clearLogoQuery);
			
			// Get Number of Games Having Banners
			$bannersQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'series') AND games.Platform = platforms.id ");
			$bannersCount = mysql_num_rows($bannersQuery);
			
			// Get Number of Games Having Screenshots
			$screenshotsQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'screenshot') AND games.Platform = platforms.id ");
			$screenshotsCount = mysql_num_rows($screenshotsQuery);
			
			// Get Number of Games Having Overviews
			$overviewQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE games.Platform = platforms.id AND games.Overview IS NOT NULL ");
			$overviewCount = mysql_num_rows($overviewQuery);
			
			// Get Number of Games Having Genres
			$genresQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE games.Platform = platforms.id AND games.Genre IS NOT NULL ");
			$genresCount = mysql_num_rows($genresQuery);
			
			// Get Number of Games Having Trailers
			$trailersQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE (games.Youtube IS NOT NULL OR games.Youtube != '') AND games.Platform = platforms.id ");
			$trailersCount = mysql_num_rows($trailersQuery);
			
			// Get Number of Games Release Date
			$releaseDateQuery = mysql_query(" SELECT games.id FROM games, platforms WHERE (games.ReleaseDate IS NOT NULL OR games.ReleaseDate != '') AND games.Platform = platforms.id ");
			$releaseDateCount = mysql_num_rows($releaseDateQuery);
			
			
			// Calculate Front Boxart Completeness Percentage
			$ratioFrontBoxart = round(($frontBoxartCount / $totalGamesCount) * 100);
			
			// Calculate Front Boxart Completeness Percentage
			$ratioRearBoxart = round(($rearBoxartCount / $totalGamesCount) * 100);
			
			// Calculate Front Boxart Completeness Percentage
			$ratioFanart = round(($fanartCount / $totalGamesCount) * 100);
			
			// Calculate ClearLOGOs Completeness Percentage
			$ratioClearLogos = round(($clearLogoCount / $totalGamesCount) * 100);
			
			// Calculate Banners Completeness Percentage
			$ratioBanners = round(($bannersCount / $totalGamesCount) * 100);
			
			// Calculate Screenshots Completeness Percentage
			$ratioScreenshots = round(($screenshotsCount / $totalGamesCount) * 100);
			
			// Calculate Overviews Completeness Percentage
			$ratioOverview = round(($overviewCount / $totalGamesCount) * 100);
			
			// Calculate Genres Completeness Percentage
			$ratioGenres = round(($genresCount / $totalGamesCount) * 100);
			
			// Calculate Trailers Completeness Percentage
			$ratioTrailers = round(($trailersCount / $totalGamesCount) * 100);
			
			// Calculate Release Date Completeness Percentage
			$ratioReleaseDate = round(($releaseDateCount / $totalGamesCount) * 100);
		?>
		
		<div id="completionGauges">
			
			<p style="text-align: center;">
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=overview" id="gaugeOverview" style="width: 230px; height: 184px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=frontboxart" id="gaugeBoxartFront" style="width: 230px; height: 184px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=fanart" id="gaugeFanart" style="width: 230px; height: 184px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=releasedate" id="gaugeReleaseDate" style="width: 230px; height: 184px; display: inline-block; text-decoration: none;"></a>
			</p>

			<p style="text-align: center; font-size: 1.3em; color: #bdbdbd; margin: 0px; padding: 0px;"><span style="margin: 0px 50px;">&uarr;</span><span style="margin: 0px 50px;">&uarr;</span><span style="margin: 0px 50px;">&uarr;</span><span style="margin: 0px 50px;">&uarr;</span><span style="margin: 0px 50px;">&uarr;</span></p>
			<p style="text-align: center; font-size: 1.3em; color: #bdbdbd; margin: 0px; padding: 4px;">Click on a Gauge To See Some Random Items Requiring Infomation</p>
			<p style="text-align: center; font-size: 1.3em; color: #bdbdbd; margin: 0px; padding: 0px;"><span style="margin: 0px 50px;">&darr;</span><span style="margin: 0px 50px;">&darr;</span><span style="margin: 0px 50px;">&darr;</span><span style="margin: 0px 50px;">&darr;</span><span style="margin: 0px 50px;">&darr;</span></p>
			
			<p style="text-align: center;">
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=genres" id="gaugeGenres" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=rearboxart" id="gaugeBoxartRear" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=clearlogo" id="gaugeClearLogo" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=banner" id="gaugeBanner" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=screenshot" id="gaugeScreenshot" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
				<a href="<?php echo $baseurl; ?>/missingitems/?itemtype=trailer" id="gaugeTrailer" style="width: 150px; height: 120px; display: inline-block; text-decoration: none;"></a>
			</p>
			
			<p style="text-align: center;">
			</p>
			
			<script type="text/javascript">
				var gBoxartFront = new JustGage({
					id: "gaugeBoxartFront", 
					value: <?php echo $ratioFrontBoxart; ?>, 
					min: 0,
					max: 100,
					title: "Games Front Boxart",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gBoxartRear = new JustGage({
					id: "gaugeBoxartRear",
					value: <?php echo $ratioRearBoxart; ?>,
					min: 0,
					max: 100,
					title: "Games Rear Boxart",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gFanart = new JustGage({
					id: "gaugeFanart", 
					value: <?php echo $ratioFanart; ?>, 
					min: 0,
					max: 100,
					title: "Games Fanart",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gClearLogo = new JustGage({
					id: "gaugeClearLogo", 
					value: <?php echo $ratioClearLogos; ?>, 
					min: 0,
					max: 100,
					title: "Games ClearLOGOs",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gBanner = new JustGage({
					id: "gaugeBanner",
					value: <?php echo $ratioBanners; ?>,
					min: 0,
					max: 100,
					title: "Games Banners",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gScreenshot = new JustGage({
					id: "gaugeScreenshot", 
					value: <?php echo $ratioScreenshots; ?>, 
					min: 0,
					max: 100,
					title: "Games Screenshots",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gOverview = new JustGage({
					id: "gaugeOverview", 
					value: <?php echo $ratioOverview; ?>, 
					min: 0,
					max: 100,
					title: "Games Overviews",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gGenres = new JustGage({
					id: "gaugeGenres",
					value: <?php echo $ratioGenres; ?>,
					min: 0,
					max: 100,
					title: "Games Genres",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gTrailers = new JustGage({
					id: "gaugeTrailer", 
					value: <?php echo $ratioTrailers; ?>, 
					min: 0,
					max: 100,
					title: "Games Trailers",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
				
				var gReleaseDate = new JustGage({
					id: "gaugeReleaseDate", 
					value: <?php echo $ratioReleaseDate; ?>, 
					min: 0,
					max: 100,
					title: "Games Release Date",
					label: "% Complete",
					titleFontColor: "#FFA500",
					valueFontColor: "#cfcfcf",
					startAnimationTime: 1800,
					startAnimationType: "<>",
					levelColors: [ "#EB1C1C", "#FFA500", "#00E300" ]
				}); 
			</script>
			
		</div>
		<hr />
	
		<div class="links">

			<!-- Start Admin Only Stats & Reports -->
			<?php
				if ($adminuserlevel == 'ADMINISTRATOR') {
			?>
				<div style="text-align: center;">
					<h1>Admin Reports &amp; Statistics</h1>
					<p><a href="?tab=userlist">User List</a></p>
					<p><a href="?tab=adminstats&statstype=missingplatform">Games Missing Platform Data</a></p>
					<p><a href="?tab=adminstats&statstype=morefront">Games With 2 or More Front Boxart</a></p>
					<p><a href="?tab=adminstats&statstype=multipleplatform">Games With Multiple Platforms</a></p>
					<p><a href="?tab=adminstats&statstype=locked">Locked Games</a></p>
					<p><a href="?tab=adminstats&statstype=newgametitles">New Games Titles</a></p>
				</div>
				<hr />
			<?php
				}
			?>
			<!-- End Admin Only Stats & Reports -->
			
			<p>&nbsp;</p>
			
			<h1 style="text-align: center;">Site Reports and Statistics</h1>

			<div style="text-align: center;">
				
				<p>&nbsp;</p>
				
				<div style="width: 300px; float: left; margin-right: 30px; text-align: center;">
					<h3>Most Recent</h3>
					<p><a href="?tab=recentbanners&bannertype=boxart">50 Most Recent Boxart Images</a></p>
					<p><a href="?tab=recentbanners&bannertype=fanart">20 Most Recent Fanart Images</a></p>
					<p><a href="?tab=recentbanners&bannertype=clearlogo">50 Most Recent ClearLOGOs</a></p>
					<p><a href="?tab=recentbanners&bannertype=series">50 Most Recent Game Banners</a></p>
				</div>
				
				<div style="width: 300px; float: left; text-align: center;">
					<h3>Missing</h3>
					<p><a href="?tab=adminstats&statstype=missingoverview">Games Missing Overview</a></p>
					<p><a href="?tab=adminstats&statstype=missinggenre">Games Missing Genre Data</a></p>
					<p><a href="?tab=adminstats&statstype=missingfront">Games Missing Front Boxart</a></p>
					<p><a href="?tab=adminstats&statstype=missingback">Games Missing Back Boxart</a></p>
					<p><a href="?tab=adminstats&statstype=missingfanart">Games Missing Fanart</a></p>
					<p><a href="?tab=adminstats&statstype=missingbanner">Games Missing Banners</a></p>
					<p><a href="?tab=adminstats&statstype=missingclearlogo">Games Missing ClearLogo's</a></p>
					<p><a href="?tab=adminstats&statstype=missingscreenshot">Games Missing Screenshots</a></p>
					<p><a href="?tab=adminstats&statstype=missingyoutube">Games Missing Youtube Trailers</a></p>
				</div>
				
				<div style="width: 300px; float: left; margin-right: 30px; text-align: center;">
					<h3>Top Rated</h3>
					<p><a href="?tab=adminstats&statstype=topratedgames">Top Rated Games</a></p>
					<p><a href="?tab=adminstats&statstype=topratedfanart">Top Rated Fanart</a></p>
					<p><a href="?tab=bannerartists">Top 50 Art Contributors</a></p>
				</div>

				<div style="clear: both;"></div>
			</div>

		</div>
	
	</div>