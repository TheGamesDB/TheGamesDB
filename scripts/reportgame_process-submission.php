<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");


	// Script to Process Submission of Reported Game
	//	-------------------------------------
	// Parameters:
	//		$reportGameID
	//		$reportUserID
	//		$reportReason
	//		$reportAdditional
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1)
	{
		if (isset($reportGameID) && isset($reportUserID) && isset($reportReason))
		{
			$existingResult = mysql_query("SELECT id FROM moderation_reportedgame WHERE gameid = $reportGameID");
			if(mysql_num_rows($existingResult) == 0)
			{
				if(mysql_fetch_object(mysql_query("SELECT * FROM game WHERE id = $reportGameID LIMIT 1"))
				{
					$reportReason = htmlentities($reportReason, ENT_QUOTES);
					$reportAdditional = htmlentities($reportAdditional, ENT_QUOTES);
					$dateadded = date("Y-m-d H:i:s");
				
					if ( mysql_query("INSERT INTO moderation_reportedgame(gameid, userid, reason, additional, dateadded) VALUES ($reportGameID, $reportUserID, '$reportReason', '$reportAdditional', '$dateadded')") )
					{
						echo "Success";
					}
				
				}
				else
				{
					echo "Error: Unable to find the existing game in the database.";
				}
			}
			else
			{
				echo "Error: This game has already been reported to the site adminstrators. Sorry for any inconvenience.";
			}
		}
		else
		{
			echo "Error: You must provide a Game ID, User ID, and a reason for reporting the game.";
		}
	}
	else
	{
		echo "Error: You must be logged in to report a game.";
	}

?>