<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");
	
	// Script to deny items in the upload moderation queue
	// Parameters:
	// 		modID - The ID of the item in the moderation queue
	//		denyreason - A string representing the reason for denial
	//		denyadditional - A string representing additional comments of the reason for denia
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1 && $adminuserlevel = 'ADMINISTRATOR')
	{
		if(isset($modID))
		{
			$time = time();
		
			$modItemResult = mysql_query("SELECT m.*, g.GameTitle from moderation_uploads AS m, games AS g WHERE m.id = $modID AND g.id = m.gameID");
			$modItem = mysql_fetch_object($modItemResult);
		
			## Delete Moderation Item SQL record
			if(mysql_query("DELETE FROM moderation_uploads WHERE id=$modItem->id"))
			{
				// Delete item from moderation queue cache folder
				if(file_exists("../moderationqueue/$modItem->filename")) { unlink("../moderationqueue/$modItem->filename"); }
				if(file_exists("../moderationqueue/_cache/$modItem->filename")) { unlink("../moderationqueue/_cache/$modItem->filename"); }
				
				$pmmessage = "Reason For Denial:\n $denyreason \n\n Additional Comments:\n $denyadditional";
				$pmmessage = htmlspecialchars($pmmessage, ENT_QUOTES);
				mysql_query(" INSERT INTO messages (`from`, `to`, `subject`, `message`, `status`, `timestamp`) VALUES ('$user->id', '$modItem->userID', 'Your submitted artwork for $modItem->GameTitle was denied', '$pmmessage', 'new', FROM_UNIXTIME($time)); ");
				echo "Success";
			}
			else
			{
				echo "Failed: Unable to delete moderation item from database.";
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