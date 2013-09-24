	<div id="gameHead">
	
		<?php
			switch ($itemtype)
			{
				case "overview":
					$titleType = "Overview";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE games.Platform = platforms.id AND (games.Overview IS NULL OR games.Overview = '') ";
					break;
					
				case "releasedate":
					$titleType = "Release Date";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE (games.ReleaseDate IS NULL OR games.ReleaseDate = '') AND games.Platform = platforms.id ";
					break;
					
				case "genres":
					$titleType = "Genres";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE games.Platform = platforms.id AND (games.Genre IS NULL OR games.Genre = '') ";
					break;
					
				case "frontboxart":
					$titleType = "Front Boxart";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') AND games.Platform = platforms.id ";
					break;
					
				case "rearboxart":
					$titleType = "Rear Boxart";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%back%') AND games.Platform = platforms.id ";
					break;
					
				case "fanart":
					$titleType = "Fanart";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'fanart') AND games.Platform = platforms.id ";
					break;
					
				case "clearlogo":
					$titleType = "ClearLOGOs";
					$query = "  SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'clearlogo') AND games.Platform = platforms.id ";
					break;
					
				case "banner":
					$titleType = "Banners";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'series') AND games.Platform = platforms.id ";
					break;
					
				case "screenshot":
					$titleType = "Screenshots";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'screenshot') AND games.Platform = platforms.id ";
					break;
					
				case "trailer":
					$titleType = "YouTube Trailers";
					$query = " SELECT games.id, games.GameTitle, platforms.name, platforms.icon FROM games, platforms WHERE (games.Youtube IS NULL OR games.Youtube = '') AND games.Platform = platforms.id ";
					break;
			}
			
			if (isset($query))
			{
				$query .= " ORDER BY RAND() LIMIT 10";
				$results = mysql_query($query);
			}
		?>
	
		<h1>Random Games Missing <?php echo $titleType; ?></h1>
	
		<?php
			echo '<hr class="boxShadow" style="border-collapse: collapse; border: 0; height: 1px; background-color: #333333; padding: 0px; margin: 0px; margin-top: 20px;" />';
			
			if (mysql_num_rows($results) > 0)
			{
				while ($currentResult = mysql_fetch_object($results))
				{
					echo '<div class="fadeBox">';
					echo '<p style="text-align: center; font-size: 1.4em;">';
					echo '<a href="' . $baseurl . '/game/' . $currentResult->id . '/" style="font-size: 1.25em; color: #FFA500; text-decoration: none;">';
					echo $currentResult->GameTitle;
					echo '</a>';
					echo '<br />';
					echo '<img src="' . $baseurl . '/images/common/consoles/png48/' . $currentResult->icon . '" style="vertical-align: middle; padding-right: 10px;" />';
					echo $currentResult->name;
					echo '</p>';
					echo '</div>';
					echo '<hr class="boxShadow" style="border-collapse: collapse; border: 0; height: 1px; background-color: #333333; padding: 0px; margin: 0px;" />';
				}
			}
			else
			{
				echo '<div class="fadeBox">';
				echo '<p style="text-align: center; font-size: 1.4em;">';
				echo '<span style="font-size: 1.25em; color: gold;">Wooooo Yeah!</span>';
				echo '<br />';
				echo 'There aren\'t any games remaining that need this info!';
				echo '<br />';
				echo 'Now that is AWESOME!';
				echo '</p>';
				echo '</div>';
				echo '<hr class="boxShadow" style="border-collapse: collapse; border: 0; height: 1px; background-color: #333333; padding: 0px; margin: 0px; margin-top: 20px;" />';
			}
		?>
	
	</div>