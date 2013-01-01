<?php
	// Script to approve items in the upload moderation queue
	// Parameters: modID - The ID of the item in the moderation queue
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
		if(isset($modID))
		{
			$time = time();
		
			// Look-up current item
			$modItemResult = mysql_query("SELECT * from moderation_uploads WHERE id = $modID");
			$modItem = mysql_fetch_object($modItemResult);
			
			// Remove previous art
			## Find previous art in DB
			$key = $modItem->imagekey;
			$gameid = $modItem->gameID;
			
			if ($key == "front")
			{
				$query = "SELECT id, filename FROM banners WHERE keytype = 'boxart' AND filename LIKE 'boxart/original/front/$gameid-1.%' LIMIT 1";
				$keytype = "boxart";
			}
			else if ($key == "back")
			{
				$query = "SELECT id, filename FROM banners WHERE keytype = 'boxart' AND filename LIKE 'boxart/original/back/$gameid-1.%' LIMIT 1";
				$keytype = "boxart";
			}
			else if ($key == "clearlogo")
			{
				$query = "SELECT id, filename FROM banners WHERE keytype = 'clearlogo' AND filename LIKE 'clearlogo/$gameid.%' LIMIT 1";
				$keytype = "clearlogo";
			}
			else
			{
				echo "Error: Couldn't match Key.";
			}
			
			if ($deletebanner = mysql_fetch_object(mysql_query($query)))
			{
				## Delete SQL record
				$query = "DELETE FROM banners WHERE id=$deletebanner->id";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());

				## Delete ratings
				$query = "DELETE FROM ratings WHERE itemtype='banner' AND itemid=$deletebanner->id";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());

				## Delete files
				if(file_exists("../banners/$deletebanner->filename")) { unlink("../banners/$deletebanner->filename"); }
				if(file_exists("../banners/_cache/$deletebanner->filename")) { unlink("../banners/_cache/$deletebanner->filename"); }
				if(file_exists("../banners/_platformviewcache/$deletebanner->filename")) { unlink("../banners/_platformviewcache/$deletebanner->filename"); }
				if(file_exists("../banners/_gameviewcache/$deletebanner->filename")) { unlink("../banners/_gameviewcache/$deletebanner->filename"); }
				if(file_exists("../banners/_favcache/_banner-view/$deletebanner->filename")) { unlink("../banners/_favcache/_banner-view/$deletebanner->filename"); }
				if(file_exists("../banners/_favcache/_boxart-view/$deletebanner->filename")) { unlink("../banners/_favcache/_boxart-view/$deletebanner->filename"); }
				if(file_exists("../banners/_favcache/_tile-view/$deletebanner->filename")) { unlink("../banners/_favcache/_tile-view/$deletebanner->filename"); }
				
				if ($key == "clearlogo")
				{
					$filename = "clearlogo/$modItem->gameID.png";
				}
				else
				{
					// MOVE APPROVED ART TO BANNERS FOLDER AND INSERT INTO BANNERS DB TABLE
					list($image_width, $image_height, $image_type, $image_attr) = getimagesize("../moderationqueue/$modItem->filename");
					
					## See if image is jpeg format
					if($image_type == 2)
					{
						$filename = "boxart/original/$modItem->imagekey/$modItem->gameID-1.jpg";
					}
					## or see if image is png format
					elseif($image_type == 3)
					{
						$filename = "boxart/original/$modItem->imagekey/$modItem->gameID-1.png";
					}
				}
				
				// Move approved Item to banners folder
				if(rename("../moderationqueue/$modItem->filename", "../banners/$filename"))
				{
					## Insert database record
					if(mysql_query("INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('$keytype', $modItem->gameID, $modItem->userID, $time, '$filename', 1, '$modItem->resolution')"))
					{
						## Delete Moderation Item SQL record
						if(mysql_query("DELETE FROM moderation_uploads WHERE id=$modItem->id"))
						{
							// Delete item from moderation queue cache folder
							if(file_exists("../moderationqueue/_cache/$modItem->filename")) { unlink("../moderationqueue/_cache/$modItem->filename"); }
							
							echo "Success";
						}
					}
					else
					{
						echo "Failed: Could not insert record into database.";
					}
				}
				else
				{
					echo "Failed: Could not move item art to banners folder.";
				}
			}
			else
			{
				echo "Failed: Could not locate old artwork in the database.";
			}
		}
		else
		{
			echo "Failed: A moderation item to process was not supplied.";
		}
	}
	else
	{
		echo "Failed: User authentication did not pass.";
	}
	
?>