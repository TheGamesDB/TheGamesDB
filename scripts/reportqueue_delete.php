<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");

	// Script to keep an image that was reported for moderation
	// Parameters: 
	//	reportType - The type of the item in the moderation queue
	//	reportID - The ID of the report in the moderation queue
	// 	reportedID - The ID of the item that was reported
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
		// Check if the report ID and type exist
		if (isset($reportID) and isset($reportType) and isset($reportedID))
		{
			// Delete the report from moderation queue
			if (mysql_query("DELETE FROM moderation_reported WHERE id = $reportID"))
			{
				echo "Deleted report succesfully.\n";

				// Start delete - Images
				// ========================
				if ($reportType == "image")
				{
					// Find the image in the database
					if ($reportedResult == mysql_query("SELECT b.filename FROM banners WHERE b.id = $reportedID LIMIT 1"))
					{
						$reported = mysql_fetch_object($reportedResult);

						if (isset($reported->filename))
						{
							// Check if the file exists
							if(file_exists("../reportedqueue/_cache/$reported->filename"))
							{
								// Delete the file
								unlink("../reportedqueue/_cache/$reported->filename");
								
								// Delete SQL record
								if (mysql_query("DELETE FROM banners WHERE id=$reportedID"))
								{
									// Delete ratings
									mysql_query("DELETE FROM ratings WHERE itemtype='banner' AND itemid=$reportedID");

									// Delete files
									if (file_exists("../banners/$reported->filename")) { unlink("../banners/$reported->filename"); }
									if (file_exists("../banners/_cache/$reported->filename")) { unlink("../banners/_cache/$reported->filename"); }
									if (file_exists("../banners/_platformviewcache/$reported->filename")) { unlink("../banners/_platformviewcache/$reported->filename"); }
									if (file_exists("../banners/_gameviewcache/$reported->filename")) { unlink("../banners/_gameviewcache/$reported->filename"); }
									if (file_exists("../banners/_favcache/_banner-view/$reported->filename")) { unlink("../banners/_favcache/_banner-view/$reported->filename"); }
									if (file_exists("../banners/_favcache/_boxart-view/$reported->filename")) { unlink("../banners/_favcache/_boxart-view/$reported->filename"); }
									if (file_exists("../banners/_favcache/_tile-view/$reported->filename")) { unlink("../banners/_favcache/_tile-view/$reported->filename"); }
								
									echo "Successfully deleted image from database.";
								}
								else
								{
									echo "Error: Unable to delete original image record from banners DB table";
								}
							}
							else
							{
								echo "Error: Unable to locate the reported cache file for this image. Most likely was deleted already.";
							}
						}
						else
						{
							echo "Error: Unable to locate the filename of existing image from the image database.";
						}
					}
					else
					{
						echo "Error: Unable to look up existing image in the database. Most likely was deleted already.";
					}
				}
				// ========================


				// Start delete - Games
				// ========================
				if ($reportType == "game")
				{
					// Delete SQL record
					if (mysql_query("DELETE FROM games WHERE id=$reportedID"))
					{
							// Delete ratings
							mysql_query("DELETE FROM ratings WHERE itemtype='game' AND itemid=$reportedID");
							echo "Successfully deleted game from database.";
					}
					else
					{
						echo "Error: Unable to delete original game from games DB table. Most likely was deleted already.";
					}
				}
				// ========================
			}
			else
			{
				echo "Error: Unable to delete the reported $reportType from the moderation database table. Most likely doesn't exist anymore.";
			}
		}
		else
		{
			echo "Error: You must provide the report ID, ID to remove, and type of the reported image/game.";
		}
	}
	else
	{
		echo "Error: You must be logged in as an administrator to use this script.";
	}
	
?>