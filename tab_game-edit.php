<?php	if ($loggedin == 1) {  ?>

<?php
	// Fetch Game Information from DB
	$id = mysql_real_escape_string($id);
	$query	= "SELECT g.*, p.name as PlatformName, p.icon as PlatformIcon FROM games as g, platforms as p WHERE g.id=$id AND g.Platform = p.id";
	$result = mysql_query($query) or die('Fetch Game Info Query Failed: ' . mysql_error());
	$rows = mysql_num_rows($result);
	$game = mysql_fetch_object($result);
?>

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
		
		$str = "Uploader:&nbsp;<a href='$baseurl/artistbanners/?id=$imageUser->id' style='color: orange;'>$imageUser->username</a>";
		
		return $str;
	}
?>

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
	
	<form id="editGameForm" name="editGameForm" action="<?= $baseurl ?>/game-edit/<?= $game->id ?>/" method="post" onsubmit="mergeAltTitles(); if(editGameForm.coopfake.checked == false) {$('#coop').val('No');} else if(editGameForm.coopfake.checked == true){ $('#coop').val('Yes');}">

		<div id="gameTitle">
			<?php if ($loggedin == 1) {  ?>
				<span id="gameUserLinks">
					<a class="greyButton" href="<?=$baseurl?>/game/<?=$game->id?>/"><img src="<?= $baseurl ?>/images/common/icons/arrowleft_16.png" style="vertical-align: -2px;" />&nbsp;View (Exit Edit)</a>
			<?php
				if ($game->locked != 'yes' OR $lockadmin->userlevel == 'ADMINISTRATOR')
				{
			?>
					<input class="greyButton" type="submit" name="function" value="Save Game">
					<input type="hidden" name="newshowid" value="<?=$game->id?>">

			<?php
					if ($adminuserlevel == 'ADMINISTRATOR')
					{
			?>
						<input class="greyButton" type="submit" name="function" value="Delete Game" onClick="return confirmSubmit()">
			<?php
					}
				}
			?>
				</span>
			<?php
			}
			?>
			<input type="text" name="GameTitle" style="font-size: 18px; font-weight: bold; width: 240px;" value="<?php echo $game->GameTitle; ?>" />
			
			<p style="display: none; clear: both; text-align: center;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#frontBoxartUpload' rel='facebox' style="color: orange;">Upload Front Boxart</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#rearBoxartUpload' rel='facebox' style="color: orange;">Upload Rear Boxart</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#fanartUpload' rel='facebox' style="color: orange;">Upload Fanart</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#clearartUpload' rel='facebox' style="color: orange;">Upload ClearLOGO</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#screenshotUpload' rel='facebox' style="color: orange;">Upload Screenshot</a> | <img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> <a href='#bannerUpload' rel='facebox' style="color: orange;">Upload Banner</a></p>
		</div>
		<div style="clear: left; padding: 10px 0px 0px 0px;"></div>
		<div id="gameCoversWrapper">
			<div id="gameCovers">
				<?php
					if ($frontCoverResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.filename LIKE '%boxart%front%' LIMIT 1 "))
					{
						$front = mysql_fetch_object($frontCoverResult);
						if (!empty($front))
						{
							?>
							<img id="frontCover" class="frontCover imgShadow" <?=imageResize("$baseurl/banners/$front->filename", "banners/_gameviewcache/$front->filename", 300, "width")?> alt="<?php echo $game->GameTitle; ?>" title="<?php echo $game->GameTitle; ?>" />
							<?php
							if ($backCoverResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.filename LIKE '%boxart%back%' LIMIT 1 "))
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
			<a href="<?="$baseurl/banners/$front->filename"?>" target="_blank"><img src="<?php echo $baseurl; ?>/images/common/icons/expand_48.png" style="width:24px; height: 24px; vertical-align: -6px;" /></a>&nbsp;<a href="<?="$baseurl/banners/$front->filename"?>" target="_blank">Front</a>
			<?php
			}
			if (!empty($front) && !empty($back))
			{
			?>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php
			}
			if (!empty($back))
			{
			?>
			<a href="<?="$baseurl/banners/$back->filename"?>" target="_blank"><img src="<?php echo $baseurl; ?>/images/common/icons/expand_48.png" style="width:24px; height: 24px; vertical-align: -6px;" /></a>&nbsp;<a href="<?="$baseurl/banners/$back->filename"?>" target="_blank">Back</a></p>
			<?php
			}
			?>

			<? if (!empty($front) || !empty($back)) { ?>
			<table call-padding="0" cell-spacing="0" style="border: 2px solid #444; border-radius: 6px; background-color: #333; color: #FFF; border-collapse: separate; border-spacing: 2px; border-color: gray; width: 100%;">
				<tr>
					<? if (!empty($front)) { ?>
					<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 16px; border-bottom: 1px solid #444; color: #333;">Front Boxart</th>
					<? } ?>
					<? if (!empty($back)) { ?>
					<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 16px; border-bottom: 1px solid #444; color: #333;">Rear Boxart</th>
					<? } ?>
				</tr>
				<tr>
					<? if (!empty($front)) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><?= imageUsername($front->id) ?></td>
					<? } ?>
					<? if (!empty($back)) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><?= imageUsername($back->id) ?></td>
					<? } ?>
				</tr>
				<? if ($loggedin == 1) { ?>
				<tr>
					<? if (!empty($front) && $loggedin = 1) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><a href="<?=$baseurl;?>/scripts/reportqueue_submit.php?reporttype=image&reportid=<?=$front->id;?>" rel="facebox" style="color: orange;">Report Image</a></td>
					<? } ?>
					<? if (!empty($back)) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><a href="<?=$baseurl;?>/scripts/reportqueue_submit.php?reporttype=image&reportid=<?=$back->id;?>" rel="facebox" style="color: orange;">Report Image</a></td>
					<? } ?>
				</tr>
				<? } ?>
				<? if ($adminuserlevel == 'ADMINISTRATOR') { ?>
				<tr>
					<? if (!empty($front) && $loggedin = 1) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><a href='<?= $baseurl?>/game-edit/<?= $game->id ?>/?function=Delete+Banner&bannerid=<?= $front->id?>' style="color: orange;">Delete This Art</a></td>
					<? } ?>
					<? if (!empty($back)) { ?>
					<td style="padding: 10px 10px; vertical-align: top; text-align: center;"><a href='<?= $baseurl?>/game-edit/<?= $game->id ?>/?function=Delete+Banner&bannerid=<?= $back->id?>' style="color: orange;">Delete This Art</a></td>
					<? } ?>
				</tr>
				<? } ?>
			</table>
			<? } ?>

		</div>
		
		<div id="gameInfo">
		
		<a href="<?= $baseurl ?>/uploader/?gameid=<?= $game->id ?>" class="greyButton uploadButton"><img src="<?= $baseurl ?>/images/common/icons/dropbox_32.png" style="vertical-align: -10px; margin-right: 10px;" />Upload Artwork</a>
		
		<div id="altTitleWrapper">
		<span class="grey">Alt. Titles</span>&nbsp;<span class="button altAdd" onclick="altAdd(this);">+</span><br /><br />
		<input type="hidden" id="alternatives" name="Alternates" />
		
		<script type="text/javascript">
			
			var addElements = "<div><input type='text' class='altTitle' /><span class='button altMinus' onclick='altMinus(this);'>-</span><br /></div>";
		
			function altMinus(element)
			{
				$(element).closest('div').remove();
			}
			
			function altAdd(element)
			{
				$('#altTitleWrapper').append(addElements);
			}
			
			function mergeAltTitles()
			{
				var allAltTitles = "";
				$('#altTitleWrapper div input').each(function(index) {
					if($(this).val() != "")
					{
						allAltTitles = allAltTitles + $(this).val() + ",";
					}
				});
				
				$("#alternatives").val(allAltTitles.slice(0, -1));
			}
		</script>
		
		<?php
			if(!empty($game->Alternates))
			{
				$alternates = explode(",", $game->Alternates); 
				foreach ($alternates as $value)
				{
			?>
					<div>
						<input type="text" class="altTitle" value="<?= $value ?>" /><span class="button altMinus" onclick="altMinus(this);">-</span><br />
					</div>
			<?php
				}
			}
		?>
	</div>
	<hr />
			<?php
				$platformQuery = mysql_query(" SELECT * FROM platforms ORDER BY name ASC");
			?>
				<p><span class="grey">Platform</span>&nbsp;&nbsp;
				<select name="Platform"<?php if ($adminuserlevel != 'ADMINISTRATOR') { echo "disabled"; } ?>>
					<?php
						while($platformResult = mysql_fetch_object($platformQuery))
						{
					?>
							<option value="<?=$platformResult->id?>"<?php if($platformResult->id == $game->Platform){ echo " selected"; } ?>><?=$platformResult->name?></option>
					<?php
						}
					?>
				</select>
				</p>
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
							if ($rating && $rating->rating)
								$rating = $rating->rating;
							else
								$rating = 0;

							for ($i = 1; $i <= 10; $i++) {
								if ($i <= $rating) {
									print "<a href=\"$baseurl/game/$id/?function=UserRating&type=game&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating)\"><img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
								else {
									print "<a href=\"$baseurl/game/$id/?function=UserRating&type=game&itemid=$id&rating=$i\" OnMouseOver=\"UserRating2('userrating',$i)\" OnMouseOut=\"UserRating2('userrating',$rating)\"><img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
								}
							}
							?>
						<?php } ?>
			</div>
			<hr />
			<?php
			$clearlogoQuery = mysql_query(" SELECT * FROM banners WHERE keytype='clearlogo' AND keyvalue='$game->id' LIMIT 1 ");
			if(mysql_num_rows($clearlogoQuery) != 0)
			{
				$clearlogoResult = mysql_fetch_object($clearlogoQuery);
			?>
			<div style="margin: auto; padding-top: 10px;">
				<div style="width: 400px; padding: 5px; margin: 0px auto 20px auto; border: 1px solid #555; border-radius: 5px; background-color: #090909; box-shadow: 3px 6px 20px 5px #000;">
					<h2 class="grey">ClearLOGO</h2>
					<img src="<?= $baseurl ?>/banners/<?= $clearlogoResult->filename ?>" alt="<?= $game->GameTitle . "ClearLOGO" ?>" title="<?= $game->GameTitle . "ClearLOGO" ?>" />
					<div style="text-align: center; !important"><?= imageUsername($clearlogoResult->id); ?>&nbsp;|&nbsp;Resolution: <?= $clearlogoResult->resolution ?><br />
					<a href='<?= "$baseurl/game-edit/$game->id/?function=Delete+Banner&bannerid=$clearlogoResult->id" ?>' style="color: orange;">Delete This Art</a><br />
					<?= imageRating($clearlogoResult->id) ?><br /><?= userImageRating($clearlogoResult->id, $baseurl, $game->id, $user->id) ?></div>
					</div>
			</div>
			<hr />
			<?php
			}
			?>
			<p><span class="grey">Overview</span></p>
			<p><textarea name="Overview" style="width: 630px; height: 200px;"><?= $game->Overview ?></textarea></p>
			<hr />
			<div id="gameVitals">
				<table>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Players</span>
						</td>
						<td>
							<select name="Players">
								<option vlaue="0">Select . . . </option>
								<option value="1" <?php if ($game->Players == 1) echo 'selected' ?>>1</option>
								<option value="2" <?php if ($game->Players == 2) echo 'selected' ?>>2</option>
								<option value="3" <?php if ($game->Players == 3) echo 'selected' ?>>3</option>
								<option value="4" <?php if ($game->Players == 4) echo 'selected' ?>>4+</option>
									?>
							</select>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grey">Co-op:</span><input type="checkbox" id="coopfake" <?php if ($game->coop == "Yes") echo 'checked' ?> />
							<input type="hidden" id="coop" name="coop" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Genres</span>
						</td>
						<td>
							<input type="text" name="Genrefake" value="<?=$game->Genre?>" maxlength="255" disabled="true">
							<a style="color: #fff;" onclick="openChild('<?=$baseurl?>/genres.php?Genre=<?=addcslashes($game->Genre,"'")?>&amp;GameTitle=<?echo addcslashes($game->GameTitle,"'");?>', 'GenresEditor<?=$game->id?>', 480, 295); return false" href="#">Choose</a>
							<input type="hidden" name="Genre" value="<?=$game->Genre?>"><br />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Release Date</span>
						</td>
						<td>
							<input type="text" name="ReleaseDate" id="ReleaseDate" value="<?=$game->ReleaseDate?>" readonly />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">ESRB Rating</span>
						</td>
						<td>
							<select name="Rating">
								<option value="">Select...</option>
								<option <?php if ($game->Rating=='EC - Early Childhood') print 'selected'; ?>>EC - Early Childhood</option>
								<option <?php if ($game->Rating=='E - Everyone') print 'selected'; ?>>E - Everyone</option>
								<option <?php if ($game->Rating=='E10+ - Everyone 10+') print 'selected'; ?>>E10+ - Everyone 10+</option>
								<option <?php if ($game->Rating=='T - Teen') print 'selected'; ?>>T - Teen</option>
								<option <?php if ($game->Rating=='M - Mature') print 'selected'; ?>>M - Mature</option>
								<option <?php if ($game->Rating=='RP - Rating Pending') print 'selected'; ?>>RP - Rating Pending</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Developer</span>
						</td>
						<td>
							<input type="text" list="developers" name="Developer" value="<?= $game->Developer ?>" />
							<datalist id="developers">
							<?php	
								$query = "SELECT keywords FROM pubdev";
								$result = mysql_query($query) or die('Query failed: ' . mysql_error());
								while($row = mysql_fetch_array($result)) {
									list($dev) = $row;
									$dev = ucfirst($dev);
									echo "<option value=\"$dev\">";
								}
								unset($dev);
							?>
							</datalist>
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Publisher</span>
						</td>
						<td>
							<input type="text" list="developers" name="Publisher" value="<?= $game->Publisher ?>" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="grey">Youtube Trailer</span>
						</td>
						<td>
							<input type="text" size="46" id="Youtube" name="Youtube" value="<?=$game->Youtube?>" onblur="$('#Youtube').val($('#Youtube').val().replace(/(^.*youtu)(be.com\/(watch\?v=|embed\/|user\/|v\/)|.be\/)|&.*$|;.*$|#.*$|\?.*$/gmi,'')); " />
						</td>
					</tr>
				</table>
			</div>
			<?php if($game->Platform == 1 || $game->Platform == 37) { ?>
			<hr />
				<div id="sysReq">
					<p><span class="grey">System Requirements</span></p>
					<table>
						<tr>
							<td style="text-align: right;">
								<span class="grey">OS:</span>
							</td>
							<td>
								<input type="text" size="20" name="os" value="<?=$game->os?>" />
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">
								<span class="grey">Processor:</span>
							</td>
							<td>
								<input type="text" size="20" name="processor" value="<?=$game->processor?>" />
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">
								<span class="grey">RAM:</span>
							</td>
							<td>
								<input type="text" size="20" name="ram" value="<?=$game->ram?>" />
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">
								<span class="grey">Hard Drive:</span>
							</td>
							<td>
								<input type="text" size="20" name="hdd" value="<?=$game->hdd?>" />
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">
								<span class="grey">Video:</span>
							</td>
							<td>
								<input type="text" size="20" name="video" value="<?=$game->video?>" />
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">
								<span class="grey">Sound:</span>
							</td>
							<td>
								<input type="text" size="20" name="sound" value="<?=$game->sound?>" />
							</td>
						</tr>
					</table>
				</div>
			<? } ?>
			
			<hr />
			
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
					<li><a id="nav_platforms" href="#gameContentTop" onclick="contentShow('platforms');">Other Platforms</a></li>
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
						if ($fanartResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'fanart' "))
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
									<img  class="fanartSlide imgShadow" <?=imageResize("$baseurl/banners/$fanart->filename", "banners/_gameviewcache/$fanart->filename", 470, "width")?> alt="<?php echo $game->GameTitle; ?> Fanart" title="<?= imageUsername($fanart->id) ?><br /><a href='<?="$baseurl/banners/$fanart->filename"?>' target='_blank'>View Full-Size</a> | <a href='<?= $baseurl; ?>/game-fanart-slideshow.php?id=<?=$game->id?>' target='_blank'>Full-screen Slideshow</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/game-edit/$game->id/?function=Delete+Banner&bannerid=$fanart->id'>Delete This Art</a>"; } ?><br /><?= imageRating($fanart->id) ?> | <?= userImageRating($fanart->id, $baseurl, $game->id, $user->id) ?>" />
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
						if ($screenResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'screenshot' "))
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
									<img  class="screenSlide" <?=imageDualResize("$baseurl/banners/$screen->filename", "banners/_gameviewcache/$screen->filename", 470, 264)?> alt="<?php echo $game->GameTitle; ?> Screenshot" title="<?= imageUsername($screen->id) ?><br /><a href='<?="$baseurl/banners/$screen->filename"?>' target='_blank'>View Full-Size</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/game-edit/$game->id/?function=Delete+Banner&bannerid=$screen->id'>Delete This Art</a>"; } ?><br /><?= imageRating($screen->id) ?> | <?= userImageRating($screen->id, $baseurl, $game->id, $user->id) ?>" />
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
					if ($bannerResult = mysql_query(" SELECT b.id, b.filename FROM banners as b WHERE b.keyvalue = '$game->id' AND b.keytype = 'series' ") or die ("banner query failed" . mysql_error()))
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
								<img class="bannerSlide" src="<?="$baseurl/banners/$banner->filename"?>" alt="<?php echo $game->GameTitle; ?> Banner" title="<?= imageUsername($banner->id) ?> | <a href='<?="$baseurl/banners/$banner->filename"?>' target='_blank'>View Full-Size</a> | <?php if($adminuserlevel == 'ADMINISTRATOR') { echo "<a href='$baseurl/game-edit/$game->id/?function=Delete+Banner&bannerid=$banner->id'>Delete This Art</a>"; } ?><br /><?= imageRating($banner->id) ?> | <?= userImageRating($banner->id, $baseurl, $game->id, $user->id) ?>" />
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
		
		</div>
		
		<!--
		<div id="gameFooter">
		
		</div>
		-->

	</div>
	
	
	<!-- Start of Upload Dialogs -->
	<?php if ($loggedin == 1) {  ?>
	<div style="display: none;">
	<div id="frontBoxartUpload" class="miniPanel">
		<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Front Box Art Upload</h2>
		<?php  	## check for agreement to terms
		if ($user->banneragreement != 1) {
			print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
		} ## Check for disabled banner upload
		elseif ($user->bannerlimit == 0) {
			print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
		} ## Check banner limit
		elseif ($game->disabled == 'Yes') {
			print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
		}
		else {
			?>
		<p>The only accepted image formats for box art are JPG and PNG.</p>
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
						<input type="hidden" name="function" value="Upload Box Art">
						<input type="submit" name="button" value="Upload" class="submit">
					</td>
				</tr>
			</table>
		</form>
			<?php } ?>
	</div>
	<?php } ?>
	
	<?php if ($loggedin == 1) {  ?>
	<div id="rearBoxartUpload" class="miniPanel">
		<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Rear Box Art Upload</h2>
		<?php  	## check for agreement to terms
		if ($user->banneragreement != 1) {
			print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
		} ## Check for disabled banner upload
		elseif ($user->bannerlimit == 0) {
			print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
		} ## Check banner limit
		elseif ($game->disabled == 'Yes') {
			print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
		}
		else {
			?>
		<p>The only accepted image formats for box art are JPG and PNG.</p>
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
						<input type="hidden" name="cover_side" value="back">
						<input type="hidden" name="function" value="Upload Box Art">
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
				elseif ($user->bannerlimit == 0) {
					print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
				} ## Check banner limit
				elseif ($game->disabled == 'Yes') {
					print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
				}
				else {
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
						<input type="hidden" name="function" value="Upload Fan Art">
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
	<div id="clearartUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> ClearLOGO Upload</h2>

				<?php  	## check for agreement to terms
				if ($user->banneragreement != 1) {
					print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
				}
				else {
					?>
			<p>All ClearLOGO images must be 400px wide, and may be up to a maximum height of 250px.</p>
			<p>The only accepted image format for ClearLOGO art is PNG.</p>
			<p>As the name suggests, ClearLOGO artwork must be on a clear (transparent) background. Solid background colors and gradients are strictly not allowed.</p>
			<p>More information can be found on the <a href="http://wiki.thegamesdb.net" target="_blank">Site Wiki</a>.</p>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
				<tr>
					<td><strong>File to Upload:</strong><br /><br />
						<input type="file" name="clearlogofile" size="42">
					</td>
				</tr>
				<tr>
					<td>
						<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
					</td>
				</tr>
				<tr>
					<td style="text-align: right">
						<input type="hidden" name="function" value="Upload Clear Logo">
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
	<div id="screenshotUpload" class="miniPanel">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
			<h2><img src="<?= $baseurl ?>/images/common/icons/upload-black_32.png" alt="Upload" style="vertical-align: -7px;" /> Screenshot Upload</h2>

				<?php  	## check for agreement to terms
				if ($user->banneragreement != 1) {
					print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
				} ## Check for disabled banner upload
				elseif ($user->bannerlimit == 0) {
					print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
				} ## Check banner limit
				elseif ($game->disabled == 'Yes') {
					print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
				}
				else {
					?>
			<p>All screenshots <strong>must</strong> be a maximum of <strong>2MB file size.</strong></p>
			<p>The only accepted image format for screenshots is JPG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images. (But screens of older pixel art style games are ok!)</p>
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
						<input type="hidden" name="function" value="Upload Screenshot">
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

				<?php  	## check for agreement to terms
				if ($user->banneragreement != 1) {
					print "You must agree to the site terms and conditions before you can upload. Go to the <a href=\"/?tab=agreement\">Agreement Page</a>";
				} ## Check for disabled banner upload
				elseif ($user->bannerlimit == 0) {
					print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
				} ## Check banner limit
				elseif ($game->disabled == 'Yes') {
					print "The ability to upload has been removed, because an admin has flagged this record as a duplicate or inaccurate";
				}
				elseif ($userbanners < $user->bannerlimit || $adminuserlevel == 'ADMINISTRATOR') {
					?>
			<p>All banner resolutions <strong>must</strong> be 760x140.</p>
			<p>The only accepted image formats for banners are JPG and PNG.</p>
			<p>Images must be of good quality. We don't want blurry or pixelated images.</p>
			<p>More information can be found on the <a href="<?= $baseurl ?>/terms/" target="_blank">Terms and Conditions page</a>.</p>
			<input type="hidden" name="subkey" value="graphical" />
			<p><nobr><strong>Banner Language:</strong></nobr>
				<select name="languageid" size="1">
					<?php

					## Display language selector
					foreach ($languages AS $langid => $langname) {
						## If we have the currently selected language
						if ($lid == $langid) {
							$selected = 'selected';
						}
						## Otherwise
						else {
							$selected = '';
						}

						## If a translation is found
						print "<option value=\"$langid\" $selected>$langname</option>\n";
					}
					?>
					</select></p>
				<p><strong>File:</strong> <input type="file" name="bannerfile" size="36"></p>
				<p><em>Please Note: Uploading an image with out saving game info first will result in data loss.</em></p>
				<p style="text-align: right;">
					<input type="hidden" name="function" value="Upload Game Banner">
					<input type="submit" name="button" value="Upload" class="submit">
				</p>
				<?php
				} ## Print banner limit message
				else {
					print "<p>You have already uploaded $userbanners banners for this game, which is your banner limit.  To get your banner limit increased, please post a request on the forums.</p>";
				}
				?>
		</form>
	</div>
	</div>
	<?php	}  ?>	
	<!-- END of Upload Dialogs -->

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
    
    $( "[name='GameTitle']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='Overview']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='Developer']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='Publisher']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='os']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='ram']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='hdd']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='video']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    $( "[name='sound']" ).blur(function() {
      $( this ).val($.trim($( this ).val()));
    });
    
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
<!-- Start Release Date Picker Script -->
<script type="text/javascript">
    $(document).ready(function(){
        $('#ReleaseDate').datepicker({ changeMonth: true,changeYear: true, yearRange: '1950:2020'  });
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
		<h2 style="text-align: center;">We can't find the game you requested...</h2>
		<p style="text-align: center;">If you believe you have recieved this message in error, please let us know.</p>
		<p style="text-align: center;"><a href="<?= $baseurl; ?>/" style="color: orange;">Click here to return to the homepage</a></p>
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
	
		<div id="gameHead">
			
			<h1>Oops!</h1>
			<h2 style="text-align: center;">You must be logged in to edit a game!</h2>
			<p style="text-align: center;">If you haven't already, please make an account with us and then log in.</p>
			<p style="text-align: center;"><a href="<?= $baseurl; ?>/login/" style="color: orange;">Click here to log in</a></p>
			
		</div>
	</div>
<?php } ?>