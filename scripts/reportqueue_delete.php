<?php
	// Script to keep an image that was reported for moderation
	// Parameters: reportID - The ID of the item in the moderation queue
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
		if (isset($reportID))
		{
			if ($reportedResult = mysql_query("SELECT m.bannerid, b.filename FROM moderation_reported AS m, banners AS b WHERE m.bannerid = b.id AND m.id = $reportID LIMIT 1"))
			{	
				if (mysql_query("DELETE FROM moderation_reported WHERE id = $reportID"))
				{
					$reported = mysql_fetch_object($reportedResult);
					if (isset($reported->filename))
					{
						if(file_exists("../reportedqueue/_cache/$reported->filename"))
						{
							unlink("../reportedqueue/_cache/$reported->filename");
							
							## Delete SQL record
							if (mysql_query("DELETE FROM banners WHERE id=$reported->bannerid"))
							{
								## Delete ratings
								mysql_query("DELETE FROM ratings WHERE itemtype='banner' AND itemid=$reported->bannerid");

								## Delete files
								if(file_exists("../banners/$reported->filename")) { unlink("../banners/$reported->filename"); }
								if(file_exists("../banners/_cache/$reported->filename")) { unlink("../banners/_cache/$reported->filename"); }
								if(file_exists("../banners/_platformviewcache/$reported->filename")) { unlink("../banners/_platformviewcache/$reported->filename"); }
								if(file_exists("../banners/_gameviewcache/$reported->filename")) { unlink("../banners/_gameviewcache/$reported->filename"); }
								if(file_exists("../banners/_favcache/_banner-view/$reported->filename")) { unlink("../banners/_favcache/_banner-view/$reported->filename"); }
								if(file_exists("../banners/_favcache/_boxart-view/$reported->filename")) { unlink("../banners/_favcache/_boxart-view/$reported->filename"); }
								if(file_exists("../banners/_favcache/_tile-view/$reported->filename")) { unlink("../banners/_favcache/_tile-view/$reported->filename"); }
							
								echo "Success";
							}
							else
							{
								echo "Error: unable to delete original image record from banners db table";
							}
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
				else
				{
					echo "Error: Unable to remove the reported image from the moderation database table";
				}
			}
			else
			{
				echo "Error: Unable to look up existing image in the images database";
			}
		}
		else
		{
			echo "Error: You must provide a the ID of the reported image to keep";
		}
	}
	else
	{
		echo "Error: You must be logged in as an administrator to use the keep image script";
	}
	
?>