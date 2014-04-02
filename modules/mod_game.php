<?php

	/*
	 * Game Functions
	 */
	if (isset($function) && $function == 'Add Game') {
		## Get Platform POSTDATA
		//$selectedPlatform = $_POST['Platform'];


		## Check for exact matches for GameTitle
		$GameTitle = mysql_real_escape_string($GameTitle);
		$GameTitle = ucfirst($GameTitle);
		$query = "SELECT * FROM games WHERE GameTitle='$GameTitle' AND Platform='$cleanPlatform'";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		## Insert if it doesnt exist already
		if (mysql_num_rows($result) == 0) {
			$query = "INSERT INTO games (GameTitle, Platform, created, author, lastupdated) VALUES ('$GameTitle', '$cleanPlatform', $time, {$user->id}, NULL)";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$id = mysql_insert_id();

			if (!empty($id))
			{
				$dbGamesResult = mysql_query("SELECT `g`.*, `p`.`id` AS `PlatformId`, `p`.`name` AS `PlatformName`, `p`.`alias` AS `PlatformAlias`, `p`.`icon` AS `PlatformIcon` FROM `games` AS `g`, `platforms` AS `p` WHERE `g`.`Platform` = `p`.`id` AND `g`.`id` = $id");
				if($dbGamesRow = mysql_fetch_assoc($dbGamesResult))
				{
					$searchParams = array();
					$searchParams['body']  = $dbGamesRow;
					$searchParams['index'] = 'thegamesdb';
					$searchParams['type']  = 'game';
					$searchParams['id']    = $id;
					$elasticsearchInsertResult = $elasticsearchClient->index($searchParams);
				}
			}
			
			$URL = "$baseurl/game/$id/";
			header("Location: $URL");
			echo $selectedPlatform;
		} else {
			$errormessage .= "<strong>Whoops!</strong> The game \"$GameTitle\" was already found for this platform. We have taken you directly to it.";
			$existingRow = mysql_fetch_object($result);
			$id = $existingRow->id;
			$tab = 'game';
		}
	}

	if (isset($function) && $function == 'Save Game') {
		$message = null;
		$errormessage = null;

		$updates = array();
		foreach ($_POST AS $key => $value) {
			if ($key != 'function' && $key != 'button' && $key != 'newshowid' && $key != 'comments' && $key != 'email' && !strstr($key, 'GameTitle_') && !strstr($key, 'Overview_') && $key != 'comments' && $key != 'requestcomments' && $key != 'requestreason') {
				$value = rtrim($value);
				$value = ltrim($value);
				if ($value) {
					$key = mysql_real_escape_string($key);
					$value = strip_tags($value, '');
					$value = mysql_real_escape_string($value);
					array_push($updates, "$key='$value'");
				} else {
					array_push($updates, "$key=NULL");
				}
			}
		}
		array_push($updates, "lastupdated=$time");

		## To keep things simple, we set GameTitle and Overview to the English for now
		if ($adminuserlevel == 'ADMINISTRATOR') {
			$GameTitle = ltrim($_POST["GameTitle"]);
			$GameTitle = rtrim($GameTitle);
			if ($GameTitle) {
				$GameTitle = mysql_real_escape_string($GameTitle);
				array_push($updates, "GameTitle='$GameTitle'");
			} else {
				array_push($updates, "GameTitle=NULL");
			}
		}
		$Overview = trim($_POST["Overview"]);
		if ($Overview) {
			$Overview = mysql_real_escape_string($Overview);
			array_push($updates, "Overview='$Overview'");
		} else {
			array_push($updates, "Overview=NULL");
		}
		
		array_push($updates, "updatedby={$user->id}");

		## Join the fields and run the query
		$updatestring = implode(', ', $updates);
		$newshowid = mysql_real_escape_string($newshowid);
		$query = "UPDATE games SET $updatestring WHERE id=$newshowid";
		$id = $newshowid;
		if($result = mysql_query($query) or die('Query failed: ' . mysql_error()))
		{
			$dbGamesResult = mysql_query("SELECT `g`.*, `p`.`id` AS `PlatformId`, `p`.`name` AS `PlatformName`, `p`.`alias` AS `PlatformAlias`, `p`.`icon` AS `PlatformIcon` FROM `games` AS `g`, `platforms` AS `p` WHERE `g`.`Platform` = `p`.`id` AND `g`.`id` = $id");
			if($dbGamesRow = mysql_fetch_assoc($dbGamesResult))
			{
				$searchParams = array();
				$searchParams['body']  = $dbGamesRow;
				$searchParams['index'] = 'thegamesdb';
				$searchParams['type']  = 'game';
				$searchParams['id']    = $id;
				$elasticsearchInsertResult = $elasticsearchClient->index($searchParams);
			}
		}

		$message .= 'Game saved.';

		//$tab = 'game-edit';
		header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		exit;
	}

	if (isset($function) && $function == 'Upload Game Banner') {
		$message = null;
		$errormessage = null;
		$subkey = "graphical";

		if(isset($bannerfile))
		{
			$uploadedfile = $bannerfile;
		}
		else
		{
			$uploadedfile = $_FILES['bannerfile']['tmp_name'];
		}

		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($uploadedfile);
		if ($image_width == 760 && $image_height == 140) {
			if ($image_type == '2' || $image_type == '3') { ## Check if it's a JPEG or png
				if ($image_type == '2') { ## If it's a JPEG name the extesion accordingly
					## Generate the new filename
					if ($subkey == 'graphical') {
						if (file_exists("banners/$subkey/$id-g.jpg") || file_exists("banners/$subkey/$id-g.png")) {
							$filekey = 2;
							while (file_exists("banners/$subkey/$id-g$filekey.jpg") || file_exists("banners/$subkey/$id-g$filekey.png")) {
								$filekey++;
							}
							$filename = "$subkey/$id-g$filekey.jpg";
						} else {
							$filename = "$subkey/$id-g.jpg";
						}
					}
				}
				elseif ($image_type == '3') { ## If it's a PNG name the extesion accordingly
					## Generate the new filename
					if ($subkey == 'graphical') {
						if (file_exists("banners/$subkey/$id-g.jpg") || file_exists("banners/$subkey/$id-g.png")) {
							$filekey = 2;
							while (file_exists("banners/$subkey/$id-g$filekey.jpg") || file_exists("banners/$subkey/$id-g$filekey.png")) {
								$filekey++;
							}
							$filename = "$subkey/$id-g$filekey.png";
						} else {
							$filename = "$subkey/$id-g.png";
						}
					}
				}
				if ($subkey == 'blank') {
					$languageid = '0';
				}
				## Rename/move the file
				if(isset($bannerfile))
				{
					if(rename($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}
				else
				{
					if (move_uploaded_file($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}
				if ($moveSuccess == true)
				{
					## Insert database record
					$id = mysql_real_escape_string($id);
					$subkey = mysql_real_escape_string($subkey);
					$query = "INSERT INTO banners (keytype, keyvalue, userid, subkey, dateadded, filename, languageid) VALUES ('series', $id, $user->id, '$subkey', $time, '$filename', '$languageid')";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());

					## Reset the missing banner count
					$query = "UPDATE games SET bannerrequest=0 WHERE id=$id";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());

					## Store the seriesid for the XML updater
					seriesupdate($id);
				}
			} else {
				$errormessage = 'Game banners MUST be in either JPG or PNG format.';
			}
		}
		else {
			$errormessage = 'Game banners MUST be 760px wide by 140px tall';
		}
		$message .= "Banner sucessfully added.";

		$tab = "game-edit";

		//header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		//exit;
	}

	if (isset($function) && $function == 'Delete Game' && $adminuserlevel == 'ADMINISTRATOR') {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "DELETE FROM games WHERE id=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		$deleteParams = array();
		$deleteParams['index'] = 'thegamesdb';
		$deleteParams['type'] = 'game';
		$deleteParams['id'] = "$id";
		$esDeleteResult = $elasticsearchClient->delete($deleteParams);

		$query = "DELETE FROM translation_seriesname WHERE seriesid=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		$query = "DELETE FROM translation_seriesoverview WHERE seriesid=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		## Store the seriesid for the XML updater
		seriesupdate($newshowid);
		$query = "INSERT INTO deletions (path) VALUES ('data/series/$id')";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		$message = 'Game deleted.';
		$id = $newshowid;
		$tab = 'mainmenu';
	}

	if (isset($function) && $function == 'Upload Box Art') {
		$message = null;
		$errormessage = null;

		$id = mysql_real_escape_string($id);

		if(isset($bannerfile))
		{
			$uploadedfile = $bannerfile;
		}
		else
		{
			$uploadedfile = $_FILES['bannerfile']['tmp_name'];
		}
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($uploadedfile);
		$resolution = $image_width . 'x' . $image_height;

		if ($image_type == 2 || $image_type == 3)
		{
			$errormessage = "";
		}
		else
		{
			$errormessage = "Your image MUST be either in JPG or PNG format.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "")
		{
            $fileExists == false;
			$fileid = 1;
            
            for ($i = 1; $i <= 10; $i++)
            {
                if (file_exists("banners/boxart/original/$cover_side/$id-$i.jpg") || file_exists("banners/boxart/original/$cover_side/$id-$i.png"))
                {
                    $fileid = $i;
                    $fileExists = true;
                }
            }
            
            if ($fileExists == true)
            {
				// Name = GameID-Timestamp
				if($image_type == 2)
				{
					$modName = $id . "-" . time() . ".jpg";
				}
				## or see if image is png format
				elseif($image_type == 3)
				{
					$modName = $id . "-" . time() . ".png";
				}

				$filename = "$cover_side/$modName";

				// Move to relavant moderation queue folder (front or back)
				if (rename($uploadedfile, "moderationqueue/$filename"))
				{
					$dateadded = date("Y-m-d H:i:s");
					// Insert in DB table (moderation_uploads)
					$query = "INSERT INTO moderation_uploads (gameID, userID, imagekey, filename, resolution, dateadded) VALUES ($id, $user->id, '$cover_side', '$filename', '$resolution', '$dateadded')";
					if (mysql_query($query))
					{
						$errormessage = ucfirst($cover_side) . " Boxart already exists for this game, your upload has been added to the moderation queue.";
					}
					else
					{
						$errormessage = "Couldn't insert image into the moderation database, please try again. Query: $query";
					}
				}
				else
				{
					$errormessage = "Couldn't move uploaded file into moderation queue directory, please try again.";
				}
			}
			else
			{
				## See if image is jpeg format
				if($image_type == 2)
				{
					$filename = "boxart/original/$cover_side/$id-$fileid.jpg";
				}
				## or see if image is png format
				elseif($image_type == 3)
				{
					$filename = "boxart/original/$cover_side/$id-$fileid.png";
				}
				if(isset($bannerfile))
				{
					if(rename($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}
				else
				{
					if (move_uploaded_file($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}
				if ($moveSuccess == true)
				{
					## Insert database record
					$id = mysql_real_escape_string($id);
					$colors = mysql_real_escape_string($colors);
					$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('boxart', $id, $user->id, $time, '$filename', 1, '$resolution')";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());

					## Store the seriesid for the XML updater
					seriesupdate($id);

					$message .= "Box art sucessfully added.";
				}
				else
				{
					$errormessage = "Unable to move uploaded file from temp folder.";
				}

			}
			$tab = 'game-edit';
		}

		//header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		//exit;
	}

	if (isset($function) && $function == 'Upload Fan Art') {
		$message = null;
		$errormessage = null;

		$id = mysql_real_escape_string($id);

		if(isset($bannerfile))
		{
			$uploadedfile = $bannerfile;
		}
		else
		{
			$uploadedfile = $_FILES['bannerfile']['tmp_name'];
		}

		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($uploadedfile);
		$resolution = $image_width . 'x' . $image_height;
		if ($resolution != '1920x1080' && $resolution != '1280x720') {
			$errormessage .= "Your image is not a valid fan art resolution.<br>";
		}
		if ($image_type != 2) {
			$errormessage .= "Your image MUST be in JPG format.<br>";
		}
		if (($resolution == '1920x1080' && filesize($uploadedfile) / 1024 > 2000) || ($resolution == '1280x720' && filesize($uploadedfile) / 1024 > 600)) {
			$errormessage .= "Your image exceeds the size restrictions.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "") {

			## Generate the new filename
			$fileid = 1;
			while (file_exists("banners/fanart/original/$id-$fileid.jpg")) {
				$fileid++;
			}
			$filename = "fanart/original/$id-$fileid.jpg";

			if(isset($bannerfile))
			{
				if(rename($uploadedfile, "banners/$filename"))
				{
					$moveSuccess = true;
				}
			}
			else
			{
				if (move_uploaded_file($uploadedfile, "banners/$filename"))
				{
					$moveSuccess = true;
				}
			}

			if ($moveSuccess == true)
			{
				## Calculate the colors
				$colors = imagecolors("banners/$filename");

				## Insert database record
				$id = mysql_real_escape_string($id);
				$colors = mysql_real_escape_string($colors);
				$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution, colors) VALUES ('fanart', $id, $user->id, $time, '$filename', 1, '$resolution', '$colors')";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());

				## Store the seriesid for the XML updater
				seriesupdate($id);
			}

			$message = "Fan art successfully added";
		}
		$tab = 'game-edit';

		//header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		//exit;
	}

	if (isset($function) && $function == 'Upload Screenshot') {
		$message = null;
		$errormessage = null;

		$id = mysql_real_escape_string($id);

		if(isset($bannerfile))
		{
			$uploadedfile = $bannerfile;
		}
		else
		{
			$uploadedfile = $_FILES['bannerfile']['tmp_name'];
		}

		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($uploadedfile);
		$resolution = $image_width . 'x' . $image_height;
		if ($image_type != 2) {
			$errormessage .= "Your image MUST be in JPG format.<br>";
		}

		if ((filesize($uploadedfile) / 1024 > 2000)) {
			$errormessage .= "Your image exceeds the size restrictions.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "") {

			## Generate the new filename
			$fileid = 1;
			while (file_exists("banners/screenshots/$id-$fileid.jpg") && $errormessage == "") {
				if($fileid == 8) {
					$errormessage = "This game already has the maximum allowed number of screenshots.<br>Please delete an existing screenshot before attempting to upload another.";
				}
				$fileid++;
			}
			if ($errormessage == "") {
				$filename = "screenshots/$id-$fileid.jpg";
				if(isset($bannerfile))
				{
					if(rename($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}
				else
				{
					if (move_uploaded_file($uploadedfile, "banners/$filename"))
					{
						$moveSuccess = true;
					}
				}

				if ($moveSuccess == true)
				{
					## Insert database record
					$id = mysql_real_escape_string($id);
					$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid) VALUES ('screenshot', $id, $user->id, $time, '$filename', 1)";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());

					## Store the seriesid for the XML updater
					seriesupdate($id);
					$message = "Screenshot successfully added";
				}
			}

		}
		$tab = 'game-edit';

		//header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		//exit;
	}

	if (isset($function) && $function == 'Upload Clear Logo') {
		$message = null;
		$errormessage = null;

		if(isset($bannerfile))
		{
			$uploadedfile = $bannerfile;
		}
		else
		{
			$uploadedfile = $_FILES['bannerfile']['tmp_name'];
		}

		## Get image Dimensions, Format Type & Attributes
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($uploadedfile);

		## Check if the image is the right size
		if ($image_width == 400 && $image_height <= 300) {

			$resolution = $image_width . "x" . $image_height;

			## Check if it's a PNG format image
			if ($image_type == '3') {

				$filename = "clearlogo/$id.png";

				 ## Check if this game already has a ClearLOGO uploaded
				if(file_exists("banners/clearlogo/$id.png"))
				{
					// Move to relavant moderation queue folder (front or back)
					if (rename($uploadedfile, "moderationqueue/$filename"))
					{
						$dateadded = date("Y-m-d H:i:s");
						// Insert in DB table (moderation_uploads)
						$query = "INSERT INTO moderation_uploads (gameID, userID, imagekey, filename, resolution, dateadded) VALUES ($id, $user->id, 'clearlogo', '$filename', '$resolution', '$dateadded')";
						if (mysql_query($query))
						{
							$errormessage = "A ClearLOGO already exists for this game, your upload has been added to the moderation queue.";
						}
						else
						{
							$errormessage = "Couldn't insert image into the moderation database, please try again";
						}
					}
					else
					{
						$errormessage = "Couldn't move uploaded file into moderation queue directory, please try again.";
					}
				}
				else
				{
					## Rename/move the file
					if(isset($bannerfile))
					{
						if(rename($uploadedfile, "banners/$filename"))
						{
							$moveSuccess = true;
						}
					}
					else
					{
						if (move_uploaded_file($uploadedfile, "banners/$filename"))
						{
							$moveSuccess = true;
						}
					}

					if ($moveSuccess == true)
					{
						## Insert database record
						$id = mysql_real_escape_string($id);
						$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('clearlogo', $id, $user->id, $time, 'clearlogo/$id.png', 1, '$resolution')";
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());

						$message .= "ClearLOGO sucessfully added.";
					}
				}
			}
			else
			{
				$errormessage = 'ClearLOGO\'s MUST be in PNG format.';
			}
		}
		else
		{
			$errormessage = 'ClearLOGO\'s MUST be 400 pixels wide by a maximum of 300px tall.';
		}

		$tab = "game-edit";

		//header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage));
		//exit;
	}

	if (isset($function) && $function == 'Lock Game') {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET locked='yes', lockedby=$user->id  WHERE id=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if (isset($function) && $function == 'UnLock Game') {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET locked='no', lockedby=''  WHERE id=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}

	## Change A Series Banner's Language
	if (isset($function) && $function == 'Change Language' AND $adminuserlevel == 'ADMINISTRATOR') {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE banners SET languageid=$languageid WHERE id=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$message = 'Banner Language Changed.';
	}

?>
