<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");


	// Script to Process Submission of Reported Object (Image/Game)
	//	-------------------------------------
	// Parameters:
	//		$reportType
	//		$reportID
	//		$reportUserID
	//		$reportReason
	//		$reportAdditional
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	// Check if we're logged in
	if ($loggedin = 1)
	{
		// Check if we have all the needed information to report
		if (isset($reportType) && isset($reportID) && isset($reportUserID) && isset($reportReason))
		{
			// Check if the report was already reported
			$existingResult = mysql_query("SELECT id FROM moderation_reported WHERE reporttype = $reportType AND reportid = $reportID");
			if(mysql_num_rows($existingResult) == 0)
			{
				// Check if the thing we want to report exists.
				$checkExistQuery;

				if ($reportType == "image")
					$checkExistQuery = "SELECT * FROM banners WHERE id = $reportID LIMIT 1";

				if ($reportType == "game")
					$checkExistQuery = "SELECT * FROM games WHERE id = $reportID LIMIT 1";

				if($checkExistQuery && mysql_fetch_object(mysql_query($checkExistQuery)))
				{
					$reportReason = htmlentities($reportReason, ENT_QUOTES);
					$reportAdditional = htmlentities($reportAdditional, ENT_QUOTES);
					$dateadded = date("Y-m-d H:i:s");

					$insertquery = "INSERT INTO moderation_reported(reporttype, reportid, userid, reason, additional, dateadded) ".
								   "VALUES ($reportType, $reportID, $reportUserID, '$reportReason', '$reportAdditional', '$dateadded')";
				
					// Now report it
					if ( mysql_query($insertquery) )
					{
						echo "Success";
					}				
				}
				else
				{
					echo "Error: Unable to find the existing $reportType in the database.";
				}
			}
			else
			{
				echo "Error: This $reportType has already been reported to the site adminstrators. Sorry for any inconvenience.";
			}
		}
		else
		{
			echo "Error: You must provide an ID, User ID, and a reason for reporting the image/game.";
		}
	}
	else
	{
		echo "Error: You must be logged in to report.";
	}

?>