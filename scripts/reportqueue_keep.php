<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");

	// Script to keep an image that was reported for moderation
	// Parameters: 
	//	reportType - The type of the item in the moderation queue
	// 	reportedID - The ID of the item that was reported
	// 	reportID - The ID of the item in the moderation queue
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
		if (isset($reportID) and isset($reportType) and isset($reportedID))
		{	
			if (mysql_query("DELETE FROM moderation_reported WHERE id = $reportID"))
			{
				echo "Deleted report succesfully.\n";

				// Start keep - Images
				// ========================
				if ($reportType == "image")
				{
					// Gather info to clear from cache, but keep the image data	
					if ($reportedResult = mysql_query("SELECT filename FROM banners WHERE id = $reportedID LIMIT 1"))
					{
						$reported = mysql_fetch_object($reportedResult);

						// Find the filename
						if (isset($reported->filename))
						{
							// Remove the reported image from the report cache
							if(file_exists("../reportedqueue/_cache/$reported->filename"))
							{
								unlink("../reportedqueue/_cache/$reported->filename");
								echo "Successfully kept image in database.";
							}
							else
							{
								echo "Error: The image was removed from the reported list succesfully, however we were unable to locate the reported cache file for this image";
							}
						}
						else
						{
							echo "Error: Unable to locate the filename of existing image from the image database";
						}
					}
				}
				// ========================

				// Start keep - Games
				// ========================
				if ($reportType == "game")
				{
					// Nothing to do here.
					echo "Successfully kept game in database.";
				}
			}
			else
			{
				echo "Error: Unable to remove the reported $reportType from the moderation database table. Most likely was deleted already.";
			}
		}
		else
		{
			echo "Error: You must provide a the report ID, ID to keep, and type of the reported image/game.";
		}
	}
	else
	{
		echo "Error: You must be logged in as an administrator to use the keep image script";
	}
	
?>