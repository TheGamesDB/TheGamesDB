<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");

	// Script to Compare images
	//	-------------------------------------
	// Parameters:
	//		$modimageid
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	include("../extentions/wideimage/WideImage.php"); ## Image Manipulation Library
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
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
			<img src="<?= "$baseurl/moderationqueue/_cache/compare/original.jpg?" . generateRandomString(32) ?>" />
		</div>
		
		<div style="float:left; width: 400px; padding: 5px; margin: 5px; text-align: center;">
			<h2>Submitted Image</h2>
			<p><span style="font-weight: bold;">Dimensions:</span> <?= $modimage->resolution ?>px</p>
			<!-- Load with WideImage -->
			<?php WideImage::load("../moderationqueue/$modimage->filename")->resize(400, 600)->saveToFile("../moderationqueue/_cache/compare/submitted.jpg"); ?>
			<img src="<?= "$baseurl/moderationqueue/_cache/compare/submitted.jpg?" . generateRandomString(32) ?>" />
		</div>
		
		<div style="clear: both;"></div>
		
	</div>
	
	<?php
	
			}
		}
	}
	
?>