<?php

	/*
	 * Platform Functions
	 */

	if ($function == 'Save Platform') {
		$message = null;
		$errormessage = null;
		
		$updates = array();
		foreach ($_POST AS $key => $value) {
			if ($key != 'function' && $key != 'platformid' && $key != 'alias') {
				$value = rtrim($value);
				$value = ltrim($value);
				if ($value) {
					$key = mysql_real_escape_string($key);
					$value = strip_tags($value, '');
					$value = mysql_real_escape_string($value);
					$value = htmlspecialchars($value, ENT_QUOTES);
					array_push($updates, "$key='$value'");
				} else {
					array_push($updates, "$key=NULL");
				}
			}
		}

		$alias = trim($alias);
		$alias = strtolower($alias);
		$alias = str_ireplace(" ", "-", $alias);
		$alias = preg_replace("/[^a-z0-9\-]/", "", $alias);
		
		if($aliasResult = mysql_query(" SELECT p.id FROM platforms AS p WHERE p.alias = '$alias' AND p.id != $platformid "))
		{
			if(mysql_num_rows($aliasResult) == 0)
			{
				array_push($updates, "alias='$alias'");
			}
			else
			{
				$errormessage = "Alias ($alias) already exists... please choose another.";
			}
		}
		
		## Join the fields and run the query
		$updatestring = implode(', ', $updates);
		$query = "UPDATE platforms SET $updatestring WHERE id=$platformid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());

		// Add Audit
		if (!empty($updatestring)) {
			//$sql = "INSERT INTO audits values(NULL, {$_SESSION['userid']}, 'updated', $id, NULL)";
			//mysql_query($sql);
		}
		$message .= 'Platform Saved.';

		$id = $platformid;
		$tab = 'platform-edit';
		
		header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
		exit;
	}

	if ($function == 'Upload Platform Icon') {
		$message = null;
		$errormessage = null;
		
		$id = mysql_real_escape_string($id);
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['iconfile']['tmp_name']);
		$resolution = $image_width . 'x' . $image_height;
		
		if ($image_type == 3)
		{
			$errormessage = "";
		}
		else
		{
			$errormessage = "Your image MUST be in PNG format.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "") 
		{	
			if(!empty($platformAlias))
			{
				$fileid = $platformAlias . "-" . time();
			}
			else
			{
				$fileid = $platformId . "-" . time();
			}
			
			$filename = "$fileid.png";
			
			$dimensions = array(16, 24, 32, 48);
			
			$prevIconQuery = mysql_query(" SELECT icon FROM platforms WHERE id = $platformId LIMIT 1 ");
			$prevIconResults = mysql_fetch_object($prevIconQuery);
			$prevIconFilename = $prevIconResults->icon;
			
			if($prevIconFilename != "console_default.png")
			{
				foreach($dimensions AS $dim)
				{
					unlink("images/common/consoles/png$dim/$prevIconFilename");
				}
			}
			
			include_once('simpleimage.php');
			
			foreach($dimensions AS $dim)
			{
				$image = new SimpleImage();
				$image->load($_FILES['iconfile']['tmp_name']);
				$image->resize($dim, $dim);
				$image->save("images/common/consoles/png$dim/$filename");
				$image = null;
			}

			if ($errormessage == false) {
				## Insert database record
				$query = " UPDATE platforms SET icon = '$filename' WHERE id = $platformId ";
				if($result = mysql_query($query))
				{
					$message .= "Platform Icon Sucessfully Updated.";
				}
				else
				{
					$errormessage = "There was a problem whilst updating the database entry for this platform icon.";
				}
			}

			$tab = 'platform-edit';
			
			header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
			exit;
		}
	}

	if ($function == 'Upload Platform Box Art') {
		$message = null;
		$errormessage = null;
		
		$id = mysql_real_escape_string($id);
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
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
			$fileid = 1;
			while (file_exists("banners/platform/boxart/$id-$fileid.jpg") || file_exists("banners/platform/boxart/$id-$fileid.png")) {
				$fileid++;
			}
			
			## See if image is jpeg format
			if($image_type == 2)
			{
				$filename = "platform/boxart/$id-$fileid.jpg";
			}
			## or see if image is png format
			elseif($image_type == 3)
			{
				$filename = "platform/boxart/$id-$fileid.png";
			}
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {
				## Insert database record
				$id = mysql_real_escape_string($id);
				$colors = mysql_real_escape_string($colors);
				$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('platform-boxart', $id, $user->id, $time, '$filename', 1, '$resolution')";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			}

			$message .= "Platform Art Sucessfully Added.";
			$tab = 'platform-edit';
			
			header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
			exit;
		}
	}

	if ($function == 'Upload Platform Fan Art') {
		$message = null;
		$errormessage = null;
		
		$id = mysql_real_escape_string($id);

		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
		$resolution = $image_width . 'x' . $image_height;
		if ($resolution != '1920x1080' && $resolution != '1280x720') {
			$errormessage .= "Your image is not a valid fan art resolution.<br>";
		}
		if ($image_type != 2) {
			$errormessage .= "Your image MUST be in JPG format.<br>";
		}
		if (($resolution == '1920x1080' && filesize($_FILES['bannerfile']['tmp_name']) / 1024 > 2000) || ($resolution == '1280x720' && filesize($_FILES['bannerfile']['tmp_name']) / 1024 > 600)) {
			$errormessage .= "Your image exceeds the size restrictions.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "") {

			## Generate the new filename
			$fileid = 1;
			while (file_exists("banners/platform/fanart/$id-$fileid.jpg")) {
				$fileid++;
			}
			$filename = "platform/fanart/$id-$fileid.jpg";
			
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

				## Calculate the colors
				$colors = imagecolors("banners/$filename");

				## Insert database record
				$id = mysql_real_escape_string($id);
				$colors = mysql_real_escape_string($colors);
				$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution, colors) VALUES ('platform-fanart', $id, $user->id, $time, '$filename', 1, '$resolution', '$colors')";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			}

			$message = "Fan art successfully added";
		}
		$tab = 'platform-edit';
		
		header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
		exit;
	}

	if ($function == 'Upload Platform Banner') {
		$message = null;
		$errormessage = null;
		
		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
		if ($image_width == 760 && $image_height == 140) {
			if ($image_type == '2' || $image_type == '3') { ## Check if it's a JPEG or png
				if ($image_type == '2') { ## If it's a JPEG name the extesion accordingly
					## Generate the new filename
						if (file_exists("banners/platform/banners/$id-1.jpg") || file_exists("banners/platform/banners/$id-1.png")) {
							$filekey = 2;
							while (file_exists("banners/$id-$filekey.jpg") || file_exists("banners/$id-$filekey.png")) {
								$filekey++;
							}
							$filename = "platform/banners/$id-$filekey.jpg";
						} else {
							$filename = "platform/banners/$id-1.jpg";
						}
				}
				elseif ($image_type == '3') { ## If it's a PNG name the extesion accordingly
					## Generate the new filename
						if (file_exists("banners/$id.jpg") || file_exists("banners/$id.png")) {
							$filekey = 2;
							while (file_exists("banners/$id-$filekey.jpg") || file_exists("banners/$id-$filekey.png")) {
								$filekey++;
							}
							$filename = "platform/banners/$id-$filekey.png";
						} else {
							$filename = "platform/banners/$id-1.png";
						}
				}
				
				## Rename/move the file
				if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

					## Insert database record
					$id = mysql_real_escape_string($id);
					$subkey = mysql_real_escape_string($subkey);
					$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename) VALUES ('platform-banner', $id, $user->id, $time, '$filename')";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				}
			} else {
				$errormessage = 'Game banners MUST be in either JPG or PNG format.';
			}
		} else {
			$errormessage = 'Game banners MUST be 760px wide by 140px tall';
		}
		$message .= "Banner sucessfully added.";
		
		header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
		exit;
	}

	if ($function == 'Upload Controller Art') {
		$message = null;
		$errormessage = null;
		
		## Get image Dimensions, Format Type & Attributes
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['controllerartfile']['tmp_name']);
		
		## Check if the image is the right size
		if ($image_width == 300 && $image_height == 300) {
			
			## Check if it's a PNG format image
			if ($image_type == '3') {
				
				## Generate the new filename
				if (file_exists("banners/platform/controllerart/$id.png"))
				{
					unlink("banners/platform/controllerart/$id.png");
				}

				## Rename/move the file
				if (move_uploaded_file($_FILES['controllerartfile']['tmp_name'], "banners/platform/controllerart/$id.png")) {

					## Insert database record
					$id = mysql_real_escape_string($id);
					$query = "UPDATE platforms SET controller = '$id.png' WHERE id = $id";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					
					$message .= "Controller art sucessfully added.";
				}

			}
			else
			{
				$errormessage = 'Controller art MUST be in PNG format.';
			}
		} else {
			$errormessage = 'Controller art MUST be 300px wide by 300px tall';
		}
		
		header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
		exit;
	}

	if ($function == 'Upload Console Art') {
		$message = null;
		$errormessage = null;
		
		## Get image Dimensions, Format Type & Attributes
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['consoleartfile']['tmp_name']);
		
		## Check if the image is the right size
		if ($image_width == 300 && $image_height == 300) {
			
			## Check if it's a PNG format image
			if ($image_type == '3') {
				
				## Generate the new filename
				if (file_exists("banners/platform/consoleart/$id.png"))
				{
					unlink("banners/platform/consoleart/$id.png");
				}

				## Rename/move the file
				if (move_uploaded_file($_FILES['consoleartfile']['tmp_name'], "banners/platform/consoleart/$id.png")) {

					## Insert database record
					$id = mysql_real_escape_string($id);
					$query = "UPDATE platforms SET console = '$id.png' WHERE id = $id";
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					
					$message .= "Console art sucessfully added.";
				}

			}
			else
			{
				$errormessage = 'Console art MUST be in PNG format.';
			}
		} else {
			$errormessage = 'Console art MUST be 300px wide by 300px tall';
		}
		
		header("Location: $baseurl/platform-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
		exit;
	}


	if ($function == 'Delete Controller Art') {
		if ($adminuserlevel == 'ADMINISTRATOR')
		{
			if(unlink("banners/platform/controllerart/$id.png"))
			{		
				$query = "UPDATE platforms SET controller = NULL WHERE id = $id";
				if($result = mysql_query($query))
				{
					$message .= "Controller art sucessfully deleted.";
				}
			}
		}
	}

	if ($function == 'Delete Console Art') {
		if ($adminuserlevel == 'ADMINISTRATOR')
		{
			if(unlink("banners/platform/consoleart/$id.png"))
			{		
				$query = "UPDATE platforms SET console = NULL WHERE id = $id";
				if($result = mysql_query($query))
				{
					$message .= "Console art sucessfully deleted.";
				}
			}
		}
	}
	
?>