<?php

	// Script to Compare images
	//	-------------------------------------
	// Parameters:
	//		$modimageid
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	include("../extentions/wideimage/WideImage.php"); ## Image Manipulation Library
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
	
		// Look-up Submitted Image in DB
		if (isset($modimageid))
		{
			if( $modimage = mysql_fetch_object(mysql_query("SELECT * FROM moderation_uploads WHERE id = $modimageid")) )
			{
				$currentimage = mysql_fetch_object(mysql_query("SELECT * FROM banners WHERE keyvalue = $modimage->gameID AND filename LIKE '%$modimage->imagekey%' LIMIT 1"));
	?>
	
	<div style="width: 841px;">
	
		<div style="float:left; width: 400px; padding: 5px; margin: 5px; text-align: center; border-right: 1px solid #999;">
			<h2>Current Image</h2>
			<p><span style="font-weight: bold;">Dimensions:</span> <?= $currentimage->resolution ?>px</p>
			<!-- Load with WideImage -->
			<?php WideImage::load("../banners/$currentimage->filename")->resize(400, 600)->saveToFile("../moderationqueue/_cache/compare/original.jpg"); ?>
			<img src="<?= "$baseurl/moderationqueue/_cache/compare/original.jpg" ?>" />
		</div>
		
		<div style="float:left; width: 400px; padding: 5px; margin: 5px; text-align: center;">
			<h2>Submitted Image</h2>
			<p><span style="font-weight: bold;">Dimensions:</span> <?= $modimage->resolution ?>px</p>
			<!-- Load with WideImage -->
			<?php WideImage::load("../moderationqueue/$modimage->filename")->resize(400, 600)->saveToFile("../moderationqueue/_cache/compare/submitted.jpg"); ?>
			<img src="<?= "$baseurl/moderationqueue/_cache/compare/submitted.jpg" ?>" />
		</div>
		
		<div style="clear: both;"></div>
		
	</div>
	
	<?php
	
			}
		}
	}
	
?>