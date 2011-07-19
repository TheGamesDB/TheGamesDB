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
	return "src=\"$cleanFilename\"";
}
?>
<div>
<?php
	if($user->favorites == "")
	{
	?>
		<div style="text-align: center; width: 500px; padding: 15px; margin:30px auto; background-color: #eee; border: 1px solid #666;">
			<h3><em>Whoops!</em> You haven't added any favorites yet.</h3>
			<p>Consider visiting some games pages and favoriting some.</p>
		</div>
	<?php
	}
	else
	{
		$increment = "odd";
		$counter = 0;
		$favoritesArray = explode(",", $user->favorites);
		?>
			<div style="text-align: center; width: 800px; padding: 15px; margin:30px auto; background-color: #eee; border: 1px solid #666;">
				<div>
					<h2 class="arcade" style="float: left;"><?=$user->username?>'s Favorites</h2>
					<div style="width: 80px; text-align: center; float: right;">
						<a href="<?=$baseurl?>?tab=favorites&favoritesview=table"><img src="<?=$baseurl?>/images/common/icons/viewicons/table.png" alt="table"/></a>
						<p style="margin-top: 2px;"><a href="<?=$baseurl?>?tab=favorites&favoritesview=table" style="color: #dd4400">Table</a></p>
					</div>
					<div style="width: 80px; text-align: center; float: right;">
						<a href="<?=$baseurl?>?tab=favorites&favoritesview=banner"><img src="<?=$baseurl?>/images/common/icons/viewicons/banner.png" alt="banner"/></a>
						<p style="margin-top: 2px;"><a href="<?=$baseurl?>?tab=favorites&favoritesview=banner" style="color: #dd4400">Banner</a></p>
					</div>
					<div style="width: 80px; text-align: center; float: right;">
						<a href="<?=$baseurl?>?tab=favorites&favoritesview=boxart"><img src="<?=$baseurl?>/images/common/icons/viewicons/boxart.png" alt="boxart"/></a>
						<p style="margin-top: 2px;"><a href="<?=$baseurl?>?tab=favorites&favoritesview=boxart" style="color: #dd4400">Boxart</a></p>
					</div>
					<div style="width: 80px; text-align: center; float: right;">
						<a href="<?=$baseurl?>?tab=favorites&favoritesview=tile"><img src="<?=$baseurl?>/images/common/icons/viewicons/tile.png" alt="tile"/></a>
						<p style="margin-top: 2px;"><a href="<?=$baseurl?>?tab=favorites&favoritesview=tile" style="color: #dd4400">Tile</a></p>
					</div>
					<div style="clear: both;"></div>
				</div>
		<?
		if($favoritesview != "table")
		{
			foreach($favoritesArray as $favoriteID)
			{
				if($gameResult = mysql_query(" SELECT g.id, g.GameTitle, p.name, p.icon FROM games as g, platforms as p WHERE g.id = '$favoriteID' AND g.Platform = p.id"))
				{
					if($game = mysql_fetch_object($gameResult))
					{
						if($favoritesview == "tile")
						{
							if($boxartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$favoriteID' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
							{
								$boxart = mysql_fetch_object($boxartResult);
							}
							?>
								<div style="width: 356px; height: 102px; float: left; padding: 10px; margin: 10px; border-radius: 16px; border: 2px solid #333; background-color: #fff;">
									<div style="height: 102px; float:left">
									<?php
										if($boxart->filename != "")
										{
									?>
										<img <?=imageResize("$baseurl/banners/$boxart->filename", "banners/_favcache/_tile-view/$boxart->filename", 100)?> alt="<?=$game->GameTitle?> Boxart" style="border: 1px solid #666;"/>
									<?php
										}
										else
										{
									?>
										<img src="<?=$baseurl?>/images/common/placeholders/boxart_blank.png" alt="<?=$game->GameTitle?> Boxart"  style="width:70px; height: 100px; border: 1px solid #666;"/>
									<?php
										}
									?>
									</div>
									<h3><a href="<?=$baseurl?>?tab=game&id=<?=$game->id?>" style="color: #000;"><?=$game->GameTitle?></a></h3>
									<p><img src="<?=$baseurl?>/images/common/consoles/png24/<?=$game->icon?>" alt="<?=$game->name?>" style="vertical-align: -6px;" />&nbsp;<?=$game->name?></p>
									<?php
										$platformIdQuery = mysql_query("SELECT * FROM platforms WHERE id = '$game->Platform' LIMIT 1");
										$platformIdResult = mysql_fetch_object($platformIdQuery);
										
										$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
										$boxartResult = mysql_num_rows($boxartQuery);
										
										$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
										$fanartResult = mysql_num_rows($fanartQuery);

										$bannerQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'series' LIMIT 1");
										$bannerResult = mysql_num_rows($bannerQuery);
										
										if($boxartResult != 0){ ?>Boxart:&nbsp;<img src="images/common/icons/tick_16.png" alt="Yes" /> | <?php } else{ ?>Boxart:&nbsp;<img src="images/common/icons/cross_16.png" alt="No" /> | <?php }
										if($fanartResult != 0){ ?>Fanart:&nbsp;<img src="images/common/icons/tick_16.png" alt="Yes" /> | <?php } else{ ?>Fanart:&nbsp;<img src="images/common/icons/cross_16.png" alt="No" /> | <?php }
										if($bannerResult != 0){ ?>Banner:&nbsp;<img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?>Banner:&nbsp;<img src="images/common/icons/cross_16.png" alt="No" /><?php }?>
									<div style="clear: both;"></div>
								</div>
							<?php
							if($increment == "even")
							{
							?>
								<div style="clear: both;"></div>
							<?
							}
						}
						elseif($favoritesview == "boxart")
						{
							if($boxartResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$favoriteID' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
							{
								$boxart = mysql_fetch_object($boxartResult);
							}
							?>
								<div style="width: 222px; min-height: 280px; float: left; padding: 10px; margin: 10px; border-radius: 16px; border: 2px solid #333; background-color: #fff;">
									<div style="height: 200px;">
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
									<h3><a href="<?=$baseurl?>?tab=game&id=<?=$game->id?>" style="color: #000;"><?=$game->GameTitle?></a></h3>
									<p><img src="<?=$baseurl?>/images/common/consoles/png24/<?=$game->icon?>" alt="<?=$game->name?>" style="vertical-align: -6px;" />&nbsp;<?=$game->name?></p>
									<div style="clear: both;"></div>
								</div>
							<?php
							if($counter == 2)
							{
								$counter = 0;
							?>
								<div style="clear: both;"></div>
							<?
							}
							else
							{
								$counter++;
							}
						}
						elseif($favoritesview == "banner")
						{
							if($bannerResult = mysql_query(" SELECT b.filename FROM banners as b WHERE b.keyvalue = '$favoriteID' AND b.keytype = 'series' LIMIT 1 "))
							{
								$banner = mysql_fetch_object($bannerResult);
							}
							?>
								<div style="width: 222px; min-height: 80px; float: left; padding: 10px; margin: 10px; border-radius: 16px; border: 2px solid #333; background-color: #fff;">
									<div style="height: 47px;">
									<?php
										if($banner->filename != "")
										{
									?>
										<img <?=imageResize("$baseurl/banners/$banner->filename", "banners/_favcache/_banner-view/$banner->filename", 200)?> alt="<?=$game->GameTitle?> Boxart" style="border: 1px solid #666;"/>
									<?php
										}
										else
										{
									?>
										<img src="<?=$baseurl?>/images/common/placeholders/banner_blank.png" alt="<?=$game->GameTitle?> Boxart"  style="width:200px; height: 47px; border: 1px solid #666;"/>
									<?php
										}
									?>
									</div>
									<h3><a href="<?=$baseurl?>?tab=game&id=<?=$game->id?>" style="color: #000;"><?=$game->GameTitle?></a></h3>
									<p><img src="<?=$baseurl?>/images/common/consoles/png24/<?=$game->icon?>" alt="<?=$game->name?>" style="vertical-align: -6px;" />&nbsp;<?=$game->name?></p>
									<div style="clear: both;"></div>
								</div>
							<?php
							if($counter == 2)
							{
								$counter = 0;
							?>
								<div style="clear: both;"></div>
							<?
							}
							else
							{
								$counter++;
							}
						}
						
						if($increment == "odd")
						{
							$increment = "even";
						}
						else
						{
							$increment = "odd";
						}
					}
				}
			}
		}
		elseif($favoritesview == "table")
		{
			?>
				<table width="100%" border="0" cellspacing="1" cellpadding="7" id="listtable">
					<tr>
						<td class="head arcade" align="center">ID</td>
						<td class="head arcade">Game Title</td>
						<td class="head arcade">P</td>
						<td class="head arcade">Genre</td>
						<td class="head arcade">ESRB</td>
						<td class="head arcade">Boxart</td>
						<td class="head arcade">Fanart</td>
						<td class="head arcade">Banner</td>
					</tr>
			<?php
			foreach($favoritesArray as $favoriteID)
			{
				if($gameResult = mysql_query(" SELECT g.id, g.GameTitle, g.Genre, g.Rating, p.name, p.icon FROM games as g, platforms as p WHERE g.id = '$favoriteID' AND g.Platform = p.id"))
				{
					if($game = mysql_fetch_object($gameResult))
					{
						$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
						$boxartResult = mysql_num_rows($boxartQuery);
						
						$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
						$fanartResult = mysql_num_rows($fanartQuery);

						$bannerQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'series' LIMIT 1");
						$bannerResult = mysql_num_rows($bannerQuery);
						
						if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
						?>
						<tr>
							<td align="center" class="<?php echo $class; ?>"><?php echo $game->id; ?></td>
							<td class="<?php echo $class; ?>"><a href="<?php echo $baseurl; ?>/?tab=game&id=<?php echo $game->id; ?>&lid=1"><?php echo $game->GameTitle; ?></a></td>
							<td class="<?php echo $class; ?>"><img src="images/common/consoles/png16/<?php echo $game->icon; ?>" alt="<?php echo $game->name; ?>" style="vertical-align: middle;" /> <?php echo $platformIdResult->name; ?></td>
							<td class="<?php echo $class; ?>">
								<?php if(!empty($game->Genre))
								{
									$mainGenre = explode("|", $game->Genre);
									if(!empty($stringGenres))
									{
										for($i = 0; $i <= count($mainGenre); $i++)
										{
											if($mainGenre[$i] == $stringGenres)
											{
												if(strlen($mainGenre[$i]) > 15)
												{
													$mainGenre[$i] = substr($mainGenre[$i], 0, 15) . "...";
												}
												echo $mainGenre[$i];
											}
										}
									}
									else
									{
										if(strlen($mainGenre[1]) > 15)
										{
											$mainGenre[1] = substr($mainGenre[1], 0, 15) . "...";
										}
										echo $mainGenre[1];
									}
								}
								?>
							</td>
							<td class="<?php echo $class; ?>"><?php echo $game->Rating; ?></td>
							<td align="center" class="<?php echo $class; ?>"><?php if($boxartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
							<td align="center" class="<?php echo $class; ?>"><?php if($fanartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
							<td align="center" class="<?php echo $class; ?>"><?php if($bannerResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
						</tr>
					<?php
					}
				}
			}
			?>
				</table>
			<?php
		}
	}
?>
				<div style="clear: both;"></div>
				<h3 class="arcade" style="color: #ff6600;">Total Favorites: <?=count($favoritesArray)?></h3>
				
			</div>
</div>
