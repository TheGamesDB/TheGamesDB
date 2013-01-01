<?php

	// Script to Process Submission of Reported Image
	//	-------------------------------------
	// Parameters:
	//		$reportImageID
	//		$reportUserID
	//		$reportReason
	//		$reportAdditional
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1)
	{
		if (isset($reportImageID) && isset($reportUserID) && isset($reportReason))
		{
			$existingResult = mysql_query("SELECT id FROM moderation_reported WHERE bannerid = $reportImageID");
			if(mysql_num_rows($existingResult) == 0)
			{
				if( $reportimage = mysql_fetch_object(mysql_query("SELECT * FROM banners WHERE id = $reportImageID LIMIT 1")) )
				{
					$reportReason = htmlentities($reportReason, ENT_QUOTES);
					$reportAdditional = htmlentities($reportAdditional, ENT_QUOTES);
					$dateadded = date("Y-m-d H:i:s");
				
					if ( mysql_query("INSERT INTO moderation_reported (bannerid, userid, reason, additional, dateadded) VALUES ($reportImageID, $reportUserID, '$reportReason', '$reportAdditional', '$dateadded')") )
					{
						echo "Success";
					}
				
				}
				else
				{
					echo "Error: Unable to find the existing image in the database.";
				}
			}
			else
			{
				echo "Error: This image has already been reported to the site adminstrators. Sorry for any inconvenience.";
			}
		}
		else
		{
			echo "Error: You must provide an Image ID, User ID and a Reason for Reporting the Image.";
		}
	}
	else
	{
		echo "Error: You must be logged in to report an image.";
	}

?>