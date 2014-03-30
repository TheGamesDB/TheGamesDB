<?php

	#####################################################
	## OTHER
	#####################################################

	if (isset($function) && $function == 'Retrieve API Key') {
		## Prepare values
		$projectname = mysql_real_escape_string($projectname);
		$projectwebsite = mysql_real_escape_string($projectwebsite);
		$userid = mysql_real_escape_string($user->id);
		$apikey = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));

		## Insert them
		$query = "INSERT INTO apiusers (apikey, projectname, projectwebsite, userid) VALUES ('$apikey', '$projectname', '$projectwebsite', $userid)";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$tab = "userinfo";
	}


	if (isset($function) && $function == 'Delete Banner') {
		## Get the banner info (also verifies username again)
		$bannerid = mysql_real_escape_string($bannerid);
		if ($adminuserlevel == 'ADMINISTRATOR') {
			$query = "SELECT * FROM banners WHERE id=$bannerid";
		} else {
			$query = "SELECT * FROM banners WHERE id=$bannerid AND userid=$user->id";
		}
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$deletebanner = mysql_fetch_object($result);
		$message = 'Image was successfully deleted.';

		if ($deletebanner->id) {
			## Delete SQL record
			$query = "DELETE FROM banners WHERE id=$deletebanner->id";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Delete ratings
			$query = "DELETE FROM ratings WHERE itemtype='banner' AND itemid=$deletebanner->id";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Delete file
			if(file_exists("banners/$deletebanner->filename")) { unlink("banners/$deletebanner->filename"); }
			if(file_exists("banners/_cache/$deletebanner->filename")) { unlink("banners/_cache/$deletebanner->filename"); }
			if(file_exists("banners/_platformviewcache/$deletebanner->filename")) { unlink("banners/_platformviewcache/$deletebanner->filename"); }
			if(file_exists("banners/_gameviewcache/$deletebanner->filename")) { unlink("banners/_gameviewcache/$deletebanner->filename"); }
			if(file_exists("banners/_favcache/_banner-view/$deletebanner->filename")) { unlink("banners/_favcache/_banner-view/$deletebanner->filename"); }
			if(file_exists("banners/_favcache/_boxart-view/$deletebanner->filename")) { unlink("banners/_favcache/_boxart-view/$deletebanner->filename"); }
			if(file_exists("banners/_favcache/_tile-view/$deletebanner->filename")) { unlink("banners/_favcache/_tile-view/$deletebanner->filename"); }

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$deletebanner->filename')";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			if ($seriesid) {
				seriesupdate($seriesid);
			} else {
				seriesupdate($id);
			}
		}
	}

	if (isset($function) && $function == 'Delete Banner Admin') {
		## Get the banner info (also verifies username again)
		$bannerid = mysql_real_escape_string($bannerid);
		$query = "SELECT * FROM banners WHERE id=$bannerid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$deletebanner = mysql_fetch_object($result);
		$message = 'Image was successfully deleted.';

		if ($deletebanner->id) {
			## Delete SQL record
			$query = "DELETE FROM banners WHERE id=$deletebanner->id";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());


			## Delete file
			unlink("banners/$deletebanner->filename");

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$deletebanner->filename')";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			if ($seriesid) {
				seriesupdate($seriesid);
			} else {
				seriesupdate($id);
			}
		}
	}


	## This function marks a series as a favorite for a user
	if (isset($function) && $function == 'ToggleFavorite') {
		## Explode the favorites into an array
		if ($user->favorites) {
			$userfavorites = explode(",", $user->favorites);
		} else {
			$userfavorites = array();
		}

		## Check if the show is in their favorites list.  If it is, remove it
		if (in_array($id, $userfavorites, 1)) {
			$temparray = array("$id");
			$userfavorites = array_diff($userfavorites, $temparray);
		}
		## Otherwise, add it in
		else {
			array_push($userfavorites, "$id");
		}
		$userfavorites = array_unique($userfavorites);

		## Update the database
		$favorites = implode(",", $userfavorites);
		$query = "UPDATE users SET favorites = '$favorites' WHERE id=$_SESSION[userid]";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$tab = 'game';
		$user->favorites = $favorites;
	}


	## This function sets the user rating for this series
	if (isset($function) && $function == "UserRating") {
		## Check for an existing rating
		$type = mysql_real_escape_string($type);
		$itemid = mysql_real_escape_string($itemid);
		$rating = mysql_real_escape_string($rating);

		$query = "SELECT id FROM ratings WHERE itemtype='$type' AND itemid=$itemid AND userid=$_SESSION[userid] LIMIT 1";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$db = mysql_fetch_object($result);


		## If we've found a valid user, replace the rating
		if ($db->id) {
			$query = "UPDATE ratings SET rating=$rating WHERE id=$db->id";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		}


		## Otherwise, insert a new record
		else {
			$query = "INSERT INTO ratings (itemtype, itemid, userid, rating) VALUES ('$type', $itemid, $_SESSION[userid], $rating)";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		}

		## Update the XML and set the proper tab
		if ($type == "series") {
			$tab = "series";
			seriesupdate($id);
		} elseif ($type == "episode") {
			$tab = "episode";
			seriesupdate($seriesid);
			$id = $itemid;
		} elseif ($type == "banner") {
			if ($tab == "series") {
				seriesupdate($id);
			} elseif ($tab == "season") {
				seriesupdate($seasonid);
			}
		}
	}

	## Sends a DMCA takedown request to site amdins email.
	if (isset($function) && $function == "Submit Takedown Request") {
		//Checks that form was filled in
		if ($workname == null) {
			$errmsg .="You must enter an 'Infringed work name'.<br/>";
		}
		if ($link == null) {
			$errmsg .="You must enter a Direct Link to infringement'.<br/>";
		}
		if ($copyown == null) {
			$errmsg .="You must indicate the copywrite owner.<br/>";
		}
		if ($byname == null) {
			$errmsg .="You must enter a company name.<br/>";
		}
		if ($byemail == null) {
			$errmsg .="You must enter an email address.<br/>";
		}
		if ($agree1 != "yes" || $agree2 != "yes") {
			$errmsg .= "You must tick both tick-boxes.<br/>";
		}

		//Creates and sends email
		if ($errmsg == null) {
			$body = "DMCA Takedown request.\r\n\r\n";
			$body = $body . "Infrindged Work Name: " . $workname . "\r\n";
			$body = $body . "Direct Link to Infringment: " . $link . "\r\n";
			$body = $body . "Copywrite Owner: " . $copyown . "\r\n";
			$body = $body . "Name / Company: " . $byname . "\r\n";
			$body = $body . "E-mail: " . $byemail . "\r\n";
			$body = $body . "Other Info / General Remarks: " . $byremarks . "\r\n";

			require("libphp-phpmailer/class.phpmailer.php");
			$mail = new PHPMailer();
			$mail->From = "DMCA@thetvdb.com";
			$mail->FromName = "TheTVDB.com";
			$mail->Host = $mail_server;
			$mail->SMTPAuth = true;
			$mail->Username = $mail_username;
			$mail->Password = $mail_password;
			$mail->Mailer = "smtp";
			$mail->AddAddress("scott@thetvdb.com", "TheTVDB.com");
			$mail->Subject = "DMCA Takedown Notice";
			$mail->Body = $body;
			$mail->Send();

			$errmsg = "Takedown Request Recieved. Please allow 5 days for processing.";
		}
	}

?>