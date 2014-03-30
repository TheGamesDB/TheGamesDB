<?php

	#####################################################
	## ADMIN CONTROL PANEL FUNCTIONS
	#####################################################
	if (isset($function) && $function == 'Update Site News') {
		if ($adminuserlevel == 'ADMINISTRATOR') {
			$sitenewsfile = fopen("sitenews.php", "w");
			if($sitenewsfile != false) {
				$sitenewswrite = fwrite($sitenewsfile, $sitenews);
				if($sitenewswrite != false)
				{
					$message = "Site news has been saved successfully";
				}
				else
				{
					$errormessage = "There was a problem saving the site news";
				}
			}
			fclose($sitenewsfile);
		}
		else {
			$errormessage = "You must be logged in as an admin to make that change.";
		}
	}

	if (isset($function) && $function == 'Add Platform') {
		if ($adminuserlevel == 'ADMINISTRATOR') {
			## Check for exact matches for platform name
			$PlatformTitle = mysql_real_escape_string($PlatformTitle);
			$PlatformTitle = ucfirst($PlatformTitle);
			$query = " SELECT * FROM platforms WHERE name='$PlatformTitle' ";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Insert if it doesnt exist already
			if (mysql_num_rows($result) == 0) {
				$query = "INSERT INTO platforms (name, icon) VALUES ('$PlatformTitle', 'console_default.png')";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$id = mysql_insert_id();

				// Add Audit
				//$sql = "INSERT INTO audits values(NULL, {$_SESSION['userid']}, 'created', $id, NULL)";
				//mysql_query($sql);

				$URL = "$baseurl/platform/$id/";
				header("Location: $URL");
			} else {
				$errormessage = "Sorry, \"$PlatformTitle\" already exists in platforms.";
			}
		}
		else {
			$errormessage = "You must be logged in as an admin to make that change.";
		}
	}

	if (isset($function) && $function == "Add New Publisher") {
		if ($adminuserlevel == 'ADMINISTRATOR') {
		
			if(empty($_FILES["publisherlogo"]["name"]))
			{
				if( mysql_query("INSERT INTO pubdev (keywords) VALUES ('$publisherKeywords')") or die ( mysql_error() ) )
				{
					$message = "Publisher/developer (without logo) added successfully.";
				}
				else
				{
					$errormessage = "There was a problem adding the publisher/developer. Please try again.";
				}
			}
			else
			{
				if($_FILES['publisherlogo']['error'] == 0)
				{
					$filenamecount = 0;
					while(file_exists("banners/publisher-logos/$filenamecount-" . $_FILES['publisherlogo']['name']))
					{
						$filenamecount++;
					}
					$logofilename = "$filenamecount-" . $_FILES['publisherlogo']['name'];
					
					if(move_uploaded_file($_FILES["publisherlogo"]["tmp_name"], "banners/publisher-logos/$logofilename"))
					{
						if(mysql_query("INSERT INTO pubdev (keywords, logo) VALUES ('$publisherKeywords', '$logofilename') ") or die (mysql_error()))
						{
							$message = "Publisher/developer added sucessfully";
						}
						else
						{
							$errormessage = "There was a problem adding the publisher/developer. Please try again.";
						}
					}
					else
					{
						$errormessage = "There was a problem uploading the new publisher/developer logo. Please try again.";
					}
				}
				else
				{
					$errormessage = "There was a problem uploading the image. Try again or use a different image.";
				}
			}
		
		}
		else
		{
			$errormessage = "You must be logged in as an admin to make that change.";
		}
	}

	if(isset($function) && $function == "Update Publisher")
	{
		if ($adminuserlevel == 'ADMINISTRATOR') {
		
			if(empty($_FILES["publisherlogo"]["name"]))
			{
				if(mysql_query("UPDATE pubdev SET keywords='$publisherKeywords' WHERE id=$publisherID"))
				{
					$message = "Publisher keywords updated successfully.";
				}
				else
				{
					$errormessage = "There was a problem updating the keywords in the database. Please try again.";
				}
			}
			else
			{
				if ($_FILES["publisherlogo"]["error"] > 0)
				{
					$errormessage = "There was a problem uploading the logo. Please try again.";
				}
				elseif ( 1 == 1)
				{
					$filenamecount = 0;
					while(file_exists("banners/publisher-logos/$filenamecount-" . $_FILES['publisherlogo']['name']))
					{
						$filenamecount++;
					}
					$logofilename = "$filenamecount-" . $_FILES['publisherlogo']['name'];
				
					if (move_uploaded_file($_FILES["publisherlogo"]["tmp_name"], "banners/publisher-logos/$logofilename"))
					{	
						if(mysql_query("UPDATE pubdev SET keywords='$publisherKeywords', logo='$logofilename' WHERE id=$publisherID"))
						{
							$message = "Publisher keywords and logo updated successfully.";
						}
						else
						{
							$errormessage = "There was a problem updating the database. Please try again.";
						}
					}
					else
					{
						$errormessage = "There was a problem saving the logo on the server. Please try again.";
					}
				}
				else
				{
					$errormessage = "The image MUST be in JPG, PNG or GIF format.";
				}
			}
			
		}
		else
		{
			$errormessage = "You must be logged in as an admin to make that change.";
		}
	}
	
?>