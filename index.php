<?php
## Connect to the database
include("include.php");

## Other Includes
include("extentions/wideimage/WideImage.php"); ## Image Manipulation Library

## Start session
session_start();
$time = time();

//$message = null;

#####################################################
## COOKIE STUFF - AUTOMATIC LOGIN
#####################################################
## Check if the id and pass match a user
if ($cookieid && $cookiepass) {
    $cookieid = mysql_real_escape_string($cookieid);
    $cookiepass = mysql_real_escape_string($cookiepass);
    $query = "SELECT * FROM users WHERE id=$cookieid AND userpass='$cookiepass'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $cookieuser = mysql_fetch_object($result);
    if ($cookieuser->id) {
        $_SESSION['userid'] = $cookieuser->id;
        $_SESSION['password'] = $cookieuser->userpass;
        $_SESSION['userlevel'] = $cookieuser->userlevel;
        $loggedin = 1;
    } else {
        unset($_SESSION['userid']);
        unset($_SESSION['password']);
        unset($_SESSION['userlevel']);
        $loggedin = 0;
    }
}



#####################################################
## LOGIN FUNCTIONS
#####################################################
$loggedin = 0; ## Just in case
## If they're attempting to log in
if ($function == 'Log In') {
    ## Verify their credentials
    $username = mysql_real_escape_string($username);
    $password = mysql_real_escape_string($password);
    $query = "SELECT * FROM users WHERE username='$username' AND userpass=PASSWORD('$password')";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $user = mysql_fetch_object($result);
    if ($user->lastupdatedby_admin) {
        $query = "SELECT * FROM users WHERE id=$user->lastupdatedby_admin";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $adminuser = mysql_fetch_object($result);
    }

    ## If their info isn't found, let them know
    if (!isset($user->id)) {
        $errormessage = 'Incorrect login info.';
        $loggedin = 0;
    }

    ## If their is deactivated let them know
    elseif ($user->active == 0) {
        $errormessage = 'Your account is de-activated. If you believe this has happened in error contact <a href="mailto:' . $adminuser->emailaddress . '">' . $adminuser->username . '</a>';
        $loggedin = 0;
    }
    ## Otherwise, store their session variables
    else {
        $_SESSION['userid'] = $user->id;
        $_SESSION['password'] = $user->userpass;
        $_SESSION['userlevel'] = $user->userlevel;
        $loggedin = 1;
        if ($user->banneragreement == 1) {
            $tab = 'mainmenu';
        } else {
            $tab = 'agreement';
        }
    }


    ## If they're logged in at this point, store a cookie
    if ($loggedin == 1 && $setcookie == 'on') {
        setcookie('cookieid', $user->id, time() + 86400 * 365);
        setcookie('cookiepass', $user->userpass, time() + 86400 * 365);
    }
}

## If they're attempting to log out
else if ($function == 'Log Out') {
    unset($_SESSION['userid']);
    unset($_SESSION['password']);
    unset($_SESSION['userlevel']);
    setcookie('cookieid', "", 0);
    setcookie('cookiepass', "", 0);
    $loggedin = 0;
    $tab = 'mainmenu';
}

## If they're already logged in
else if (isset($_SESSION['userid'])) {
    $loggedin_userid = $_SESSION['userid'];
    $loggedin_password = $_SESSION['password'];

    ## Verify their credentials
    $loggedin_userid = mysql_real_escape_string($loggedin_userid);
    $loggedin_password = mysql_real_escape_string($loggedin_password);
    $query = "SELECT * FROM users WHERE id=$loggedin_userid AND userpass='$loggedin_password'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $user = mysql_fetch_object($result);

    ## If their info isn't found, remove session variables
    if (!isset($user->id)) {
        unset($_SESSION['userid']);
        unset($_SESSION['password']);
        unset($_SESSION['userlevel']);
        $loggedin = 0;
    }

    ## Otherwise, mark them as logged in
    else {
        $loggedin = 1;
    }
}

## If they're already logged out
else {
    $loggedin = 0;
}

## Administrator and SuperAdmin variable
global $adminuserlevel;
$adminuserlevel = '';
if ($_SESSION['userlevel'] == 'ADMINISTRATOR' OR $_SESSION['userlevel'] == 'SUPERADMIN') {
    $adminuserlevel = 'ADMINISTRATOR';
}

// Logged in Redirect List
$secureArea = array(
    //'addgame'
);
if (!$loggedin && in_array($tab, $secureArea)) {
    //header("Location:index.php");
	$tab = "mainmenu";
	$errormessage = "You must be logged in to access that area. <a href=\"$baseurl/?tab=login\">Login</a>";
}

#####################################################
## Language stuff
#####################################################
## Get list of languages and store array
global $languages;
global $lid;
$query = "SELECT * FROM languages ORDER BY name";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($lang = mysql_fetch_object($result)) {
    $languages[$lang->id] = $lang->name;
}

## Set the default language
if (!isset($lid)) {
    if ($user->languageid) {
        $lid = $user->languageid;  ## user preferred language
    } else {
        $lid = 1;  ## English
    }
}

#####################################################
## MAIN MENU FUNCTIONS
#####################################################
if ($function == 'Add Game') {
	## Get Platform POSTDATA
	//$selectedPlatform = $_POST['Platform'];
	
	
    ## Check for exact matches for seriesname
    $GameTitle = mysql_real_escape_string($GameTitle);
    $GameTitle = ucfirst($GameTitle);
    $query = "SELECT * FROM games WHERE GameTitle='$GameTitle' AND Platform='$cleanPlatform'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    ## Insert if it doesnt exist already
    if (mysql_num_rows($result) == 0) {
        $query = "INSERT INTO games (GameTitle, Platform, created, lastupdated) VALUES ('$GameTitle', '$cleanPlatform', $time, NULL)";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $id = mysql_insert_id();
        // TODO: trace this back and change the name
        //seriesupdate($id); ## Update the XML data
        // Add Audit
        $sql = "INSERT INTO audits values(NULL, {$_SESSION['userid']}, 'created', $id, NULL)";
        mysql_query($sql);

        $URL = "$baseurl/game/$id/";
        header("Location: $URL");
		echo $selectedPlatform;
    } else {
        $errormessage = "Sorry, \"$GameTitle\" Already Exists For That Platform.";
    }
}

// Function to auto-redirect to game page if only one result is found
if ($function == "Search")
{
	$string = mysql_real_escape_string($string);
	
	$searchQuery = mysql_query("SELECT g.id FROM games as g WHERE g.GameTitle = '$string'");
	if (mysql_num_rows($searchQuery) == 1)
	{
		$searchResult = mysql_fetch_object($searchQuery);
		$tab = "game";
		$id  = $searchResult->id;
	}
	else
	{
		$searchQuery = "SELECT g.id FROM games as g WHERE MATCH(g.GameTitle) AGAINST ('$string')";
		$arr = array();
		preg_match('/[0-9]+/', $string, $arr);
		foreach($arr as $numeric)
		{
				$searchQuery .= " AND g.GameTitle LIKE '%$numeric%'";
		}
		
		$searchQuery = mysql_query($searchQuery);
		
		if (mysql_num_rows($searchQuery) == 1)
		{
			$searchResult = mysql_fetch_object($searchQuery);
			$tab = "game";
			$id  = $searchResult->id;
		}
	}
}

// Function to update last search/favorites view type in users db table
if ($updateview == "yes")
{
	if ($loggedin == 1)
	{
		if (!empty($searchview))
		{
			$mode = $searchview;
		}
		elseif (!empty($favoritesview))
		{
			$mode = $favoritesview;
		}
		mysql_query(" UPDATE users SET favorites_displaymode = '$mode' WHERE id = '$user->id' ");
		$user->favorites_displaymode = $mode;
	}
}

// Function to share page via email
if($function == "Share via Email")
{
	// Check that captcha is completed and matches
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['captcha']) && $_POST['captcha'] == $_SESSION['captcha'])
	{
		##Make userinput safe
		$fromname = mysql_real_escape_string($fromname);
		$fromaddress = mysql_real_escape_string($fromaddress);
		$toaddress = mysql_real_escape_string($toaddress);
		$url = mysql_real_escape_string($url);
		
		## Email it to the user
		$from = "$fromname <$fromaddress>";
		$host = $mail_server;
		$to = "'$toaddress <$toaddress>";
		$subject = "TheGamesDB.net - $fromname has shared a link with you";
		if($messagecontent != false)
		{
			$quote = "Message From Your Friend:\n\"$messagecontent\"\n\n";
		}
		$emailmessage = "TheGamesDB.net \n\n$fromname visited thegamesdb.net and wanted to share a link with you \n\n$quote\nYour Link Details:\n$urlsubject: $url \n\nWe hope you enjoy your visit with us, \n\nTheGamesDB.net Crew.";
		$headers = 'From: ' . $from;
		mail($to, $subject, wordwrap($emailmessage, 70), $headers);
		
		// Display success message and finish up session
		$message = "Message Sent to $toaddress!";
		unset($_SESSION['captcha']); /* this line makes session free, we recommend you to keep it */
	} 
	elseif($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['captcha']))
	{
		$errormessage = "Message was not sent, captcha didn't pass...<br />Please try again and remember to complete the captcha!";
	}
}

if ($function == 'Send PM') {
	$toQuery = mysql_query(" SELECT id FROM users WHERE username = '$pmto' LIMIT 1");
	$to = mysql_fetch_object($toQuery);

	$pmmessage = htmlspecialchars($pmmessage, ENT_QUOTES);
	
	if(mysql_query(" INSERT INTO messages (`from`, `to`, `subject`, `message`, `status`, `timestamp`) VALUES ('$user->id', '$to->id', '$pmsubject', '$pmmessage', 'new', FROM_UNIXTIME($time)); ") or die(mysql_error()))
	{
		$message = "PM Sent to \"$pmto\" Successfully";
	}
	else
	{
		$errormessage = "Oops! There was a problem sending your message,<br />Please try again...";
	}
}

if ($function == 'Delete PM') {
	if(mysql_query(" DELETE FROM messages WHERE messages.id = $pmid AND messages.to = '$user->id' "))
	{
		$message = "Your message was deleted.";
	}
	else
	{
		$errormessage = "There was a problem deleting your message,<br />Please try again...";
	}
}

if ($function == "Generate Platform Alias's") {
	if($aliasResult = mysql_query(" SELECT p.id, p.name, p.alias FROM platforms AS p WHERE p.alias IS NULL OR p.alias = '' "))
	{
		$successflag = true;
		while($alias = mysql_fetch_object($aliasResult))
		{
			$platformName = trim($alias->name);
			$platformName = strtolower($platformName);
			$platformName = str_ireplace(" ", "-", $platformName);
			$platformAlias = preg_replace("/[^a-z0-9\-]/", "", $platformName);
			if(!mysql_query(" UPDATE platforms SET alias = '$platformAlias' WHERE id = '$alias->id' "))
			{
				$successflag = false;
			}
		}
		
		if($successflag == true)
		{
			$message = "Missing Platform Alias's Generated Successfully";
		}
		else
		{
			$errormessage = "There was a problem generating the Platform Alias's,<br />please carefully check the list and try again.";
		}
	}
	else {
		$errormessage = "There was a problem generating the Platform Alias's,<br />please carefully check the list and try again.";
	}
}


/*
 * Game Functions
 */

if ($function == 'Save Game') {
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

    ## Join the fields and run the query
    $updatestring = implode(', ', $updates);
    $newshowid = mysql_real_escape_string($newshowid);
    $query = "UPDATE games SET $updatestring WHERE id=$newshowid";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    // Add Audit
    if (!empty($updatestring)) {
        $sql = "INSERT INTO audits values(NULL, {$_SESSION['userid']}, 'updated', $id, NULL)";
        mysql_query($sql);
    }
    $message .= 'Game saved.';

    $id = $newshowid;
    //$tab = 'game-edit';
	header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
	exit;
}

if ($function == 'Upload Game Banner') {
	$message = null;
	$errormessage = null;
	
    ## Check if the image is the right size
    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
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
            if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

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
    } else {
        $errormessage = 'Game banners MUST be 760px wide by 140px tall';
    }
	$message .= "Banner sucessfully added.";
	
	header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
	exit;
}

if ($function == 'Delete Game' && $adminuserlevel == 'ADMINISTRATOR') {
    ## Prepare SQL
    $id = mysql_real_escape_string($id);
    $query = "DELETE FROM games WHERE id=$id";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

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

if ($function == 'Upload Box Art') {
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
		while (file_exists("banners/boxart/original/$cover_side/$id-$fileid.jpg") || file_exists("banners/boxart/original/$cover_side/$id-$fileid.png")) {
			$fileid++;
		}
		
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
		if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {
			## Insert database record
			$id = mysql_real_escape_string($id);
			$colors = mysql_real_escape_string($colors);
			$query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('boxart', $id, $user->id, $time, '$filename', 1, '$resolution')";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			seriesupdate($id);
		}

		$message .= "Box art sucessfully added.";
		$tab = 'game-edit';
	}
}

if ($function == 'Upload Fan Art') {
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
        while (file_exists("banners/fanart/original/$id-$fileid.jpg")) {
            $fileid++;
        }
        $filename = "fanart/original/$id-$fileid.jpg";
        if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

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
	
	header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
	exit;
}

if ($function == 'Upload Screenshot') {
	$message = null;
	$errormessage = null;
	
    $id = mysql_real_escape_string($id);

    ## Check if the image is the right size
    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
    $resolution = $image_width . 'x' . $image_height;
    if ($image_type != 2) {
        $errormessage .= "Your image MUST be in JPG format.<br>";
    }
    if ((filesize($_FILES['bannerfile']['tmp_name']) / 1024 > 2000)) {
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
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {
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
	
	header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
	exit;
}
	
if ($function == 'Upload Clear Logo') {
	$message = null;
	$errormessage = null;
	
	## Get image Dimensions, Format Type & Attributes
	list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['clearlogofile']['tmp_name']);
	
	## Check if the image is the right size
	if ($image_width == 400 && $image_height <= 250) {
		
		$resolution = $image_width . "x" . $image_height;
		
		## Check if it's a PNG format image
		if ($image_type == '3') {
			
			 ## Check if this game already has a ClearLOGO uploaded
			if(file_exists("banners/clearlogo/$id.png"))
			{
				$errormessage = "This game already has a ClearLOGO uploaded.<br>Please delete the current image before attempting to upload another.";
			}
			else
			{
				## Rename/move the file
				if (move_uploaded_file($_FILES['clearlogofile']['tmp_name'], "banners/clearlogo/$id.png"))
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
		$errormessage = 'ClearLOGO\'s MUST be 400 pixels wide by a maximum of 250px tall';
	}
	
	header("Location: $baseurl/game-edit/$id/?message=" . urlencode($message) . "&errormessage=" . urlencode($errormessage)); 
	exit;
}

if ($function == 'Lock Game') {
    ## Prepare SQL
    $id = mysql_real_escape_string($id);
    $query = "UPDATE games SET locked='yes', lockedby=$user->id  WHERE id=$id";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
}
if ($function == 'UnLock Game') {
    ## Prepare SQL
    $id = mysql_real_escape_string($id);
    $query = "UPDATE games SET locked='no', lockedby=''  WHERE id=$id";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
}





## Change A Series Banner's Language
if ($function == 'Change Language' AND $adminuserlevel == 'ADMINISTRATOR') {
    ## Prepare SQL
    $id = mysql_real_escape_string($id);
    $query = "UPDATE banners SET languageid=$languageid WHERE id=$id";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $message = 'Banner Language Changed.';
}


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

/*
 * Comments Functions
 */

function check_input($value)
{
// Stripslashes
if (get_magic_quotes_gpc())
  {
  $value = stripslashes($value);
  }
// Quote if not a number
if (!is_numeric($value))
  {
  $value = "'" . mysql_real_escape_string($value) . "'";
  }
return $value;
}
 
if ($function == 'Add Game Comment') {
	$comment = htmlspecialchars($comment, ENT_QUOTES);
	$userid = check_input($userid);
	$gameid = check_input($gameid);
	$commentQuery = mysql_query(" INSERT INTO comments (userid, gameid, comment, timestamp) VALUES ('$userid', '$gameid', '$comment', FROM_UNIXTIME($time)) ") or die('Query failed: ' . mysql_error());
}

if ($function == 'Delete Game Comment') {
	$commentQuery = mysql_query(" DELETE FROM comments WHERE id = $commentid ") or die('Query failed: ' . mysql_error());
}


#####################################################
## REGISTRATION AND PASSWORD FUNCTIONS
#####################################################
if ($function == 'Register') {
    ## Check for exact matches for username
    $username = mysql_real_escape_string($username);
    $userpass1 = mysql_real_escape_string($userpass1);
    $userpass2 = mysql_real_escape_string($userpass2);
    $email = mysql_real_escape_string($email);
    $languageid = mysql_real_escape_string($languageid);
    $uniqueid = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    ## Insert if it doesnt exist already
    if (mysql_num_rows($result) == 0) {
        if ($userpass1 == $userpass2 && $userpass1 != '') {
            if ($email) {
                $query = "INSERT INTO users (username, userpass, emailaddress, languageid, uniqueid) VALUES ('$username', PASSWORD('$userpass1'), '$email', $languageid, '$uniqueid')";
                $result = mysql_query($query) or die('Query failed: ' . mysql_error());
                $tab = 'mainmenu';
                $message = '<p style=\"font-size: x-small !important;\"><strong><em>Thank you for registering with TheGamesDB!</em></strong><p>You will receive an email confirmation with your account information shortly.  Please proceed to the <a href=\"$baseurl/?tab=login\">Login</a> screen and review our terms and conditions.  If you have any questions, please visit our forums.  We hope you enjoy your stay!</p>';
				
				## Email it to the user
				$from = "TheGamesDB <$mail_username>";
				$host = $mail_server;
				$to = $username . '<' . $email . '>';
				$subject = "Thank you for registering with TheGamesDB.net";
				$emailmessage = "Thank you for registering with TheGamesDB.net.\n\nHere is your new login information:\nusername: $username\npassword: $userpass1\n\nIf you have forgotten your password you can reset it by visiting: http://www.thegamesdb.net/?tab=password\n\nIf you have any questions, please let us know.\n\nTheGamesDB Crew.";
				$headers = 'From: ' . $from;
				mail($to, $subject, wordwrap($emailmessage, 70), $headers);
            } else {
                $errormessage = 'Email address is required.';
            }
        } else {
            $errormessage = 'Passwords do not match or are below the minimum required length.';
        }
    } else {
        $errormessage = 'Username already exists.  Please try another.';
    }
}


if ($function == 'Reset Password') {
    ## Get their email address and username
    $email = mysql_real_escape_string($email);
    $query = "SELECT emailaddress, username, id FROM users WHERE emailaddress='$email'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $db = mysql_fetch_object($result);

    ## If we found a match
    if ($db->id) {
        ## Generate a random password
        $newpass = genpassword(8);

        ## Set it in the database
        $newpass = mysql_real_escape_string($newpass);
        $query = "UPDATE users SET userpass=PASSWORD('$newpass') WHERE id='$db->id'";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        ## Email it to the user
        $from = "TheGamesDB <$mail_username>";
        $host = $mail_server;
        $to = $db->username . '<' . $db->emailaddress . '>';
        $subject = "Your account information";
        $message = "This is an automated message.\n\nYour GamesDB password has been reset.\n\nHere is your new login information:\nusername: $db->username\npassword: $newpass\n\nIf you have any questions, please let us know.\n\nTheGamesDB Crew\n";
        $headers = 'From: ' . $from;
        mail($to, $subject, wordwrap($message, 70), $headers);

        $message = 'Login information has been sent.';
    } else {
        $errormessage = 'That address cannot be found.';
    }
}


if ($function == 'Update User Information') {
    $user->languageid = $languageid;

    ## Update password and email address
    if ($userpass1 == $userpass2 && $userpass1 != '' && $email != '') {
        $userpass1 = mysql_real_escape_string($userpass1);
        $userpass2 = mysql_real_escape_string($userpass2);
        $email = mysql_real_escape_string($email);
        $languageid = mysql_real_escape_string($languageid);
        $favorites_displaymode = mysql_real_escape_string($favorites_displaymode);
        $query = "UPDATE users SET userpass=PASSWORD('$userpass1'), emailaddress='$email', languageid=$languageid, favorites_displaymode='$favorites_displaymode' WHERE id=$user->id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $message = 'Account was successfully updated.';
    }
    ## Error.. passwords were entered, but don't match
    else if ($userpass1 || $userpass2) {
        $errormessage = 'Passwords do not match.';
    }
    ## Update email address
    else if ($email) {
        $email = mysql_real_escape_string($email);
        $languageid = mysql_real_escape_string($languageid);
        $favorites_displaymode = mysql_real_escape_string($favorites_displaymode);
        $query = "UPDATE users SET emailaddress='$email', languageid=$languageid, favorites_displaymode='$favorites_displaymode' WHERE id=$user->id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $message = 'Account was successfully updated (no password change).';
    }
    ## Error... empty emailaddress
    else {
        $errormessage = 'Naughty naughty... an email address is required.';
    }
}

## Update Users Image
if ($function == 'Update User Image') {
	if($_FILES['userimage']['error'] == 0)
	{
		$existingfiles = glob("banners/users/" . $user->id . "*.jpg");
		foreach ($existingfiles as $userfile)
		{
			unlink($userfile);
		}
		$filename = $_FILES['userimage']['name'];
		$image = WideImage::load($_FILES['userimage']['tmp_name']);
		$resized = $image->resize(64, 64);
		$resized->saveToFile("banners/users/" . $user->id . "-" . date("YmdHis") . ".jpg");
		$message = "Successfully Uploaded User Image";
	}
	else
	{
		$errormessage = "There was a problem uploading the image. Try again or use a different image.";
	}
}

## Administrator's User Update Form
if ($function == 'Admin Update User') {
    ## Prepare the fields
    $form_userlevel = mysql_real_escape_string($form_userlevel);
    $languageid = mysql_real_escape_string($languageid);
    $bannerlimit = mysql_real_escape_string($bannerlimit);
    $form_active = mysql_real_escape_string($form_active);

    ## Update password and all other fields
    if ($userpass1 == $userpass2 && $userpass1 != '' && $email != '' && $username != '') {
        $username = mysql_real_escape_string($username);
        $userpass1 = mysql_real_escape_string($userpass1);
        $userpass2 = mysql_real_escape_string($userpass2);
        $email = mysql_real_escape_string($email);
        $query = "UPDATE users SET username='$username', userpass=PASSWORD('$userpass1'), emailaddress='$email', userlevel='$form_userlevel', languageid='$languageid', bannerlimit='$bannerlimit', active='$form_active', lastupdatedby_admin='$user->id' WHERE id='$id'";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $message = 'Account was successfully updated.';
    }
    ## Error.. passwords were entered, but don't match
    else if ($userpass1 || $userpass2) {
        $errormessage = 'Passwords do not match.';
    }
    ## Update all fields except password
    else if ($email != '' && $username != '') {
        $username = mysql_real_escape_string($username);
        $email = mysql_real_escape_string($email);
        $query = "UPDATE users SET username='$username', emailaddress='$email', userlevel='$form_userlevel', languageid='$languageid', bannerlimit='$bannerlimit', active='$form_active', lastupdatedby_admin='$user->id' WHERE id=$id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $message = 'Account was successfully updated (no password change).';
    }
    ## Error... empty emailaddress
    else {
        $errormessage = 'Naughty naughty... an email address is required.';
    }
    $errormessage = $userlevel;
}


if ($function == 'Terms Agreement') {
    if ($agreecheck) {
        $query = "UPDATE users SET banneragreement=1 WHERE id=$user->id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $message = 'Thank you for agreeing to the site terms. You may now upload banners';
        $tab = 'mainmenu';
    }
}

#####################################################
## ADMIN CONTROL PANEL FUNCTIONS
#####################################################
if ($function == 'Update Site News') {
	if ($adminuserlevel == 'ADMINISTRATOR') {
		$sitenewsfile = fopen("sitenews.php", "w");
		if($sitenewsfile != false) {
			$sitenewswrite = fwrite($sitenewsfile, $sitenews);
			if($sitenewswrite != false)
			{
				$message = 'Site News Has Been Saved Successfully';
			}
			else
			{
				$errormessage = 'There was a problem saving the site news';
			}
		}
		fclose($sitenewsfile);
	}
	else {
		$errormessage = 'You must be logged in as an admin to make that change';
	}
}

if ($function == 'Add Platform') {
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
        $errormessage = "Sorry, \"$PlatformTitle\" Already Exists in Platforms.";
    }
}

if ($function == "Add New Publisher") {
	if ($adminuserlevel == 'ADMINISTRATOR') {
	
		if(empty($_FILES["publisherlogo"]["name"]))
		{
			$errormessage = "You cannot add a new publisher without selecting a logo.";
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
					if(mysql_query(" INSERT INTO pubdev (keywords, logo) VALUES ('$publisherKeywords', '$logofilename') ") or die (mysql_error()))
					{
						$message = "Pulisher/Developer Added Sucessfully";
					}
					else
					{
						$errormessage = "There was a problem updating Pulisher/Developer Keywords, please try again...";
					}
				}
				else
				{
					$errormessage = "There was a problem uploading the new Publisher/Developer logo, please try again...";
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
		$errormessage = 'You must be logged in as an admin to make that change';
	}
}

if($function == "Update Publisher")
{
	if ($adminuserlevel == 'ADMINISTRATOR') {
	
		if(empty($_FILES["publisherlogo"]["name"]))
		{
			if(mysql_query(" UPDATE pubdev SET keywords='$publisherKeywords' WHERE id=$publisherID "))
			{
				$message = "Publisher keywords updated successfully.";
			}
			else
			{
				$errormessage="There was a problem updating the keywords in the database. Please try again.";
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
					if(mysql_query(" UPDATE pubdev SET keywords='$publisherKeywords', logo='$logofilename' WHERE id=$publisherID "))
					{
						$message = "Publisher keywords and logo updated successfully.";
					}
					else
					{
						$errormessage="There was a problem updating the database. Please try again.";
					}
				}
				else
				{
					$errormessage = "There was a problem saving the logo on the server. Please try again.";
				}
			}
			else
			{
				$errormessage = "The image MUST be in JPG, PNG or GIF format";
			}
		}
		
	}
	else
	{
		$errormessage = 'You must be logged in as an admin to make that change';
	}
}


#####################################################
## OTHER
#####################################################

if ($function == 'Retrieve API Key') {
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


if ($function == 'Delete Banner') {
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

if ($function == 'Delete Banner Admin') {
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
if ($function == 'ToggleFavorite') {
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
if ($function == "UserRating") {
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
if ($function == "Submit Takedown Request") {
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

if ($tab != "login" && isset($redirect))
{
	header("Location: $baseurl$redirect");
	exit;
}

## Default tab
if ($tab == "") {
    $tab = 'mainmenu';
}
?>

<?php
if($tab != "mainmenu")
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php
## Redirect if no javascript
        if ($tab != "nojs") {
            print "<noscript><meta http-equiv=\"refresh\" content=\"0; url=$baseurl/nojs/\"/></noscript>\n";
        }
        ?>
        <title>TheGamesDB.net - An open, online database for video game fans</title>
		
		<meta name="robots" content="index, follow" />
		<meta name="keywords" content="thegamesdb, the games db, games, database, meta, metadata, api, video, youtube, trailers, wallpapers, fanart, cover art, box art, fan art, open, source, game, search, forum, directory" />
		<meta name="language" content="en-US" />
		<meta name="description" content="TheGamesDB is an open, online database for video game fans. We are driven by a strong community to provide the best place to find information, covers, backdrops screenshots and videos for games, both modern and classic." />
		
		<link rel="shortcut icon" href="<?= $baseurl ?>/favicon.ico" />
		
        <link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/standard.css" />
		
		<?php if ($tab == "game" || $tab == "game-edit" || $tab == "platform" || $tab == "platform-edit" || $tab == "messages" || $tab == "message" || $tab == "favorites" || $tab == "listseries" || $tab == "listplatform" || $tab == "addgame" || $tab == "login" || $tab == "register" || $tab == "password" || $tab == "userinfo" || $tab == "api" || $tab == "showcase" || $tab == "nojs" || $tab == "recentgames" || $tab == "recentaddedgames" || $tab == "topratedgames" || $tab == "platforms" || $tab == "topratedplatforms" || $tab == "recentbanners" || $tab == "stats" || $tab == "bannerartists" || $tab == "adminstats" || $tab == "terms" || $tab == "agreement" || $tab == "admincp" || $tab == "updatepub" || $tab == "addpub") { $newlayout = true; ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/gamenew.css" />
		<?php } ?>
		
        <link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/js/ckeditor/assets/output_xhtml.css" />
        <link rel="stylesheet" href="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/COLOURloversColorPicker.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-ui/css/trontastic/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />

        <script type="text/JavaScript" src="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/js/COLOURloversColorPicker.js"></script>
        <script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/JavaScript" src="<?php echo $baseurl; ?>/js/jquery-ui/js/jquery-ui-1.8.14.custom.min.js"></script>
		
		<!-- Start AnythingSlider Include -->
        <link rel="stylesheet" href="<?php echo $baseurl; ?>/js/anythingslider/css/anythingslider.css" type="text/css" media="all" />
		<script src="<?php echo $baseurl; ?>/js/anythingslider/js/jquery.anythingslider.js" type="text/javascript"></script>
		<!-- End AnythingSlider Include -->
		
		<!-- Start FaceBox Include -->
        <link rel="stylesheet" href="<?php echo $baseurl; ?>/js/facebox/facebox.css" type="text/css" media="all" />
		<script src="<?php echo $baseurl; ?>/js/facebox/facebox.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
			   $('a[rel*=facebox]').facebox() 
			}) 
		</script>
		<!-- End FaceBox Include -->
		
		<!-- Start ShadowBox Include -->
        <link rel="stylesheet" href="<?php echo $baseurl; ?>/js/shadowbox/shadowbox.css" type="text/css" media="all" />
		<script src="<?php echo $baseurl; ?>/js/shadowbox/shadowbox.js" type="text/javascript"></script>
		<script type="text/javascript">
			Shadowbox.init({ overlayOpacity: 0.85 });
		</script>
		<!-- End ShadowBox Include -->
		
		<!-- Start Cufon Include -->
		<script src="<?php echo $baseurl; ?>/js/cufon/cufon-yui.js" type="text/javascript"></script>
		<script src="<?php echo $baseurl; ?>/js/cufon/arcade.font.js" type="text/javascript"></script>
		<script type="text/javascript">
			Cufon.replace('.arcade');
		</script>
		<!-- End Cufon Include -->
		
		
		<!-- Start jQuery Image Dropdown Include -->
		<link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/js/jqdropdown/dd.css" />
		<script src="<?php echo $baseurl; ?>/js/jqdropdown/js/jquery.dd.js" type="text/javascript"></script>
		<!-- End jQuery Image Dropdown Include -->
		
		<!-- Start xFade2 Include -->
		<?php if($tab == "game") { ?>
		<script src="<?php echo $baseurl; ?>/js/xfade2/xfade2.js" type="text/javascript"></script>
		<?php } ?>
		<!-- End xFade2 Include -->
		
		<!-- Start jQuery Enabled CKEditor & CKFinder Include -->
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/ckeditor/adapters/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/ckfinder/ckfinder.js"></script>
		<!-- End jQuery Enabled CKEditor & CKFinder Include -->

		<!-- Start Game View Page Scripts -->
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/jqflip/jquery.flip.min.js"></script>
		
		<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/nivo-slider/themes/default/default.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/nivo-slider/nivo-slider.css" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/nivo-slider/jquery.nivo.slider.pack.js"></script>
		<!-- End Game View Page Scripts -->		
		
		<!-- Start Platform View Page Scripts -->
		<link type="text/css" rel="stylesheet" href="<?php echo $baseurl; ?>/js/theatre/theatre.css" />
		<script type="text/javascript" src="<?php echo $baseurl; ?>/js/theatre/jquery.theatre-1.0.js"></script>
		<!-- End Platform View Page Scripts -->
		
		<!-- Start jQuery Snow Script -->
		<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-snowfall/styles.css" type="text/css" media="all" />
		<script src="<?php echo $baseurl; ?>/js/jquery-snowfall/snowfall.min.jquery.js" type="text/javascript"></script>
		<!-- End jQuery Snow Script -->
		
		<?php
			## Connect to the database
			include("js/core-js.php");
		?>
		
    </head>
    <body>
		
		<div id="frontHeader" style="height: 78px; position: absolute; top: 0px; left: 0px; width: 100%; z-index: 300; background: url(/images/bg_bannerws-thin.png) repeat-x center center; box-shadow: 0px 0px 6px 0px #000;">
			<div id="frontBanner" style="width: 880px; margin: auto;">
				<p style="position: absolute; top: 10px; right: 15px; font-family:Arial; font-size:10pt; margin: 0px; padding: 0px;">
					<?php if ($loggedin) {
						$msgQuery = mysql_query(" SELECT id FROM messages WHERE status = 'new' AND messages.to = '$user->id' ");
						$msgCount = mysql_num_rows($msgQuery);
					?><a href="<?= $baseurl ?>/messages/">Messages</a> <?php if($msgCount > 0) { echo"<span style=\"color: Chartreuse;\">($msgCount)</span>"; } else { echo "($msgCount)"; } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/favorites/">Favorites</a> <span>(<?php if($user->favorites != ""){ echo count(explode(",", $user->favorites)); } else{ echo "0"; } ?>) <span style="color: #ccc;">|</span> <?php if ($adminuserlevel == 'ADMINISTRATOR') { ?> <a href="<?= $baseurl ?>/admincp/">Admin Control Panel</a> <?php } else { ?><a href="<?= $baseurl ?>/userinfo/">My User Info</a><?php } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/?function=Log Out">Logout</a>
					<?php } else { ?>
						<a href="<?= $baseurl ?>/login/?redirect=<?= urlencode($_SERVER["REQUEST_URI"]) ?>">Login</a> <span style="color: #ccc;">|</span> New to the site? <a href="<?= $baseurl ?>/register/">Register here!</a>
					<?php } ?>
				</p>
				<a href="<?php echo $baseurl; ?>/" title="An open database of video games">
					<img src="<?php echo $baseurl; ?>/images/bannerws-thin-glass.png" style="border-width: 0px" />
				</a>
			</div>
		</div>
		
		<div id="nav" style="position: absolute; top: 78px; left: 0px; width: 100%;">
			<div style="width: 1000px; margin: 0px auto;">
				<form id="search" action="<?= $baseurl ?>/search/">
					<input class="left autosearch" type="text" name="string" style="color: #333; margin-left: 40px; margin-top: 5px; width: 190px;" />
					<input type="hidden" name="function" value="Search" />
					<input class="left"type="submit" value="Search" style="margin-top: 4px; margin-left: 4px; height: 24px;" />
				</form>
				<ul>
					<li id="nav_donation" class="tab"><a href="<?= $baseurl ?>/donation/"></a></li>
					<li id="nav_forum" class="tab"><a target="_blank" href="http://forums.thegamesdb.net"></a></li>
					<li id="nav_stats" class="tab"><a href="<?= $baseurl ?>/stats/"></a></li>
				<?php if ($loggedin): ?>
						<li id="nav_submit" class="tab"><a href="<?= $baseurl ?>/addgame/"></a></li>
				<?php endif; ?>
				</ul>
			</div>
		</div>
		
		<div id="navMain">
		
			<!-- GAMES NAV ITEM -->
			<?php if ($tab == "game" || $tab == "game-edit" || $tab == "listseries" || $tab == "recentgames" || $tab == "recentaddedgames" || $tab == "topratedgames" || $tab == "addgame") { $subnav = "games"; ?><div class="active"><?php } else { ?><div><?php } ?><a href="<?= $baseurl ?>/topratedgames/">Games</a></div>

			<!-- PLATFORMS NAV ITEM -->
			<?php if ($tab == "platform" || $tab == "platform-edit" || $tab == "platforms" || $tab == "listplatform" || $tab == "topratedplatforms") { $subnav = "platforms"; ?><div class="active"><?php } else { ?><div><?php } ?><a href="<?= $baseurl ?>/platforms/">Platforms</a></div>

			<!-- STATS NAV ITEM -->
			<?php if ($tab == "stats" || $tab == "adminstats" || $tab == "userlist" || $tab == "bannerartists" || $tab == "recentbanners") { $subnav = "stats"; ?><div class="active"><?php } else { ?><div><?php } ?><a href="<?= $baseurl ?>/stats/">Stats</a></div>

			<!-- FORUMS NAV ITEM -->
			<div><a href="http://forums.thegamesdb.net">Forums</a></div>
			
			<!-- ADD NEW GAME NAV ITEM -->
			<a href="<?= $baseurl ?>/addgame/" style="position: absolute; padding: 3px 8px 4px 3px; margin: 3px 4px 4px 20px; border: 1px solid #eee; border-radius: 6px; background-color: #333; color: #eee; font-size: 14px; text-decoration: none; font-weight: bold;"><img src="<?= $baseurl ?>/images/common/icons/star_14.png" style="margin: 0px 5px; 0px 0px; padding: 0px; vertical-align: middle;" />Add New Game</a>

			<!-- SEARCH NAV ITEM -->
			<div style="text-align: left; position: relative; float: right; height: 18px; width: 200px; padding: 2px 3px; margin: 3px 50px; border: 1px solid #999; border-radius: 6px; background-color: #eee; ">
				<form action="<?= $baseurl ?>/search/" id="searchForm" style="width: 300px; display: inline;">
					<img src="<?= $baseurl ?>/images/common/icons/search_18.png" style="margin: 0px 5px; padding: 0px; vertical-align: middle;" onclick="if($('#navSearch').val() != '') { $('#searchForm').submit(); } else { alert('Please enter something to search for before pressing search!'); }" /><input class="autosearch" type="text" name="string" id="navSearch" style="height: 18px; width: 170px; border: 0px; padding: 0px; margin: 0px auto; background-color: #eee;" />
					<input type="hidden" name="function" value="Search" />
				</form>
			</div>
			<div id="autocompleteContainer" style="clear: right; color: #ffffff !important; position: relative; float: right; height: 200px; width: 206px; font-size: 12px;"></div>
			
		</div>
		
		<?php
			if ($subnav == "games")
			{
		?>
			<div id="navSubGames" class="navSub">
				<span class="navSubLinks">
					<a href="<?=$baseurl ?>/topratedgames/">Top Rated Games</a>
					<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
					<a href="<?=$baseurl ?>/recentaddedgames/">Recently Added Games</a>
					<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
					<a href="<?=$baseurl ?>/recentgames/">Recently Updated Games</a>
				</span>
			</div>
		<?php
			}
		?>
		
		<?php
			if ($subnav == "platforms")
			{
		?>
			<div id="navSubPlatforms" class="navSub">
				<span class="navSubLinks">
					<a href="<?=$baseurl ?>/platforms/">All Platforms</a>
					<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
					<a href="<?=$baseurl ?>/topratedplatforms/">Top 10 Rated Platforms</a>
				</span>
			</div>
		<?php
			}
		?>
		
		<?php
			if ($subnav == "stats")
			{
		?>
			<!--<div id="navSubStats" class="navSub">
			
			</div>-->
		<?php
			}
		?>

		<div style=" display: none; position: absolute; top: 113px; background: url(<?php echo $baseurl; ?>/images/bg_banner-shadow.png) repeat-x center center; height: 15px; width: 100%; z-index: 200; opacity: 0.5;"></div>

		<div id="tinyHeader" style="position: fixed; width: 100%; left: 0px; top: 0px; height: 50px; z-index: 299;">			
			<div style="width: 100%; height: 35px; background: #000;">
				<div style="width: 1000px; margin: auto; background: #000 url(<?php echo $baseurl; ?>/images/header-tiny.png) no-repeat center left;">
					<form action="<?= $baseurl ?>/search/" style="width: 300px; display: inline;">
						<input class="left autosearch" type="text" name="string" style="color: #333; margin-left: 40px; margin-top: 5px; width: 190px;" />
						<input type="hidden" name="function" value="Search" />
						<input class="left"type="submit" value="Search" style="margin-top: 4px; margin-left: 4px; height: 24px;" />
					</form>
					<a href="<?php echo $baseurl; ?>/" style="margin-left: 50px;"><img src="<?php echo $baseurl; ?>/images/tiny-logo.png" alt="TheGamesDB.net" /></a>
					<p style="position: absolute; top: 10px; right: 15px; font-family:Arial; font-size:10pt; margin: 0px; padding: 0px;">
					<?php if ($loggedin) {
						?><a href="<?= $baseurl ?>/messages/">Messages</a> <?php if($msgCount > 0) { echo"<span style=\"color: Chartreuse;\">($msgCount)</span>"; } else { echo "($msgCount)"; } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/favorites/">Favorites</a> <span>(<?php if($user->favorites != ""){ echo count(explode(",", $user->favorites)); } else{ echo "0"; } ?>) <span style="color: #ccc;">|</span> <?php if ($adminuserlevel == 'ADMINISTRATOR') { ?> <a href="<?= $baseurl ?>/admincp/">Admin Control Panel</a> <?php } else { ?><a href="<?= $baseurl ?>/userinfo/">My User Info</a><?php } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/?function=Log Out">Logout</a>
					<?php } else { ?>
						<a href="<?= $baseurl ?>/login/">Login</a> <span style="color: #ccc;">|</span> New to the site? <a href="<?= $baseurl ?>/register/">Register here!</a>
					<?php } ?>
				</p>
				</div>
			</div>
			<div style="background: url(<?php echo $baseurl; ?>/images/bg_banner-shadow.png) repeat-x center center; height: 15px; width: 100%; z-index: 299; opacity: 0.5;"></div>
		</div>
		
        <div id="main">

			<div id="content">
                <?php if(!$newlayout) { ?>
				<?php if($errormessage): ?>
				<div class="error"><?= $errormessage ?></div>
				<?php endif; ?>
				<?php if($message): ?>
				<div class="message"><?= $message ?></div>
				<?php endif; ?>
                <?php } ?>
				
				<!-- Start Include Page (Tab) Content -->
				<?php
					include("tab_$tab.php");
				?>
				<!-- End Include Page (Tab) Content -->
				
			</div>

		</div>
		</div>
		
		
		<div id="footer" style="position: fixed; width: 100%; bottom: 0px; z-index: 200; text-align: center;">
			<div id="footerbarShadow" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar-shadow.png) repeat-x center center; height: 15px; opacity: 0.5"></div>
			<div id="footerbar" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar.png) repeat-x center center; height: 30px;">
				<div id="Terms" style="padding-top: 5px; padding-left: 25px; float: left; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
					<a href="<?=$baseurl?>/terms/" style="color: #333;">Terms &amp; Conditions</a>
				</div>
				
				<div id="theTeam" style="padding-top: 5px; padding-right: 25px; float: right; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
					<a rel="facebox" href="#credits" style="color: #333;">TheGamesDB Team</a>
				</div>
				
				<div style="padding-top: 4px;">
					<a href="http://www.facebook.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png" alt="Visit us on Facebook" title="Visit us on Facebook" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png')" /></a>
					<a href="http://twitter.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png" alt="Visit us on Twitter" title="Visit us on Twitter" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png')" /></a>
					<a href="https://plus.google.com/116977810662942577082/posts" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/google_dark.png" alt="Visit us on Google Plus" title="Visit us on Google Plus"  onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_dark.png')" /></a>
				</div>
			</div>
		</div>
		
		<div id="credits" style="display: none;">
		<div style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; text-shadow: 0px 2px 6px #666;">
			<h1>The Team</h1>
			<p>Here at TheGamesDB.net we have a small but very passionate and dedicated team.</p>
			<p>We are always striving to find ways to improve this site to provide our users with the best experience possible.</p>
			<p>&nbsp;</p>
			<p><strong>Owner:</strong> Scott Brant <em>(smidley)</em></p>
			<p><strong>Coding &amp; Design:</strong> Alex Nazaruk <em>(flexage)</em></p>
			<p><strong>Coding &amp; Design:</strong> Matt McLaughlin</p>
			<p>&nbsp;</p>
			<p>We would also like to give a big thanks to all our contributers, without your involvement this site wouldn't be as good as it is today.</p>
		</div>
		</div>

		<script type="text/javascript">
		$(function() {
			var availableTags = [
				<?php
					if($titlesResult = mysql_query(" SELECT DISTINCT GameTitle FROM games ORDER BY GameTitle ASC; "))
					{
						while($titlesObj = mysql_fetch_object($titlesResult))
						{
							echo " \"$titlesObj->GameTitle\",\n";
						}
					}
				?>
			];
			$( ".autosearch" ).autocomplete({
				source: availableTags,
				position: { offset: "-30 3" },
				appendTo: '#autocompleteContainer',
				select: function(event, ui) { this.form.submit(); }
			});
		});
	</script>
		
			<script type="text/javascript">

				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-16803563-1']);
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();

			</script>
		
		<script type="text/javascript">
			// jQuery Snow Script Instance
			// $('#main').snowfall({ flakeCount : 100, maxSpeed : 10, round: true, shadow: true, minSize: 2, maxSize: 4 });
		</script>
		
		<!-- Start Force instant run of cufon to circumvent IE delay -->
		<script type="text/javascript"> Cufon.now(); </script>
		<!-- End Force instant run of cufon to circumvent IE delay -->
		
		
    </body>
<?php
}
else
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
   xml:lang="en-gb" lang="en-gb" >
   
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" content="thegamesdb, the games db, games, database, meta, metadata, api, video, youtube, trailers, wallpapers, fanart, cover art, box art, fan art, open, source, game, search, forum," />
	<meta name="language" content="en-US" />
	<meta name="description" content="TheGamesDB is an open, online database for video game fans. We are driven by a strong community to provide the best place to find information, covers, backdrops screenshots and videos for games, both modern and classic." />
  
	<title>TheGamesDB.net - An open, online database for video game fans</title>
	
	<link rel="shortcut icon" href="<?= $baseurl ?>/favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo $baseurl; ?>/js/fullscreenslider/css/style.css"/>
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-ui/css/trontastic/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />
	<script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/jquery.tmpl.min.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/fullscreenslider/js/script.js"></script>
	<script type="text/JavaScript" src="<?php echo $baseurl; ?>/js/jquery-ui/js/jquery-ui-1.8.14.custom.min.js"></script>

	<!-- Start FaceBox Include -->
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/facebox/facebox.css" type="text/css" media="all" />
	<script src="<?php echo $baseurl; ?>/js/facebox/facebox.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
		   $('a[rel*=facebox]').facebox() 
		}) 
	</script>
	<!-- End FaceBox Include -->
	
	<!-- Start jQuery Snow Script -->
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-snowfall/styles.css" type="text/css" media="all" />
	<script src="<?php echo $baseurl; ?>/js/jquery-snowfall/snowfall.min.jquery.js" type="text/javascript"></script>
	<!-- End jQuery Snow Script -->
	
	<style type="text/css">
		body {
			background:#111111 url(<?php echo $baseurl; ?>/images/bg-main-background.jpg) repeat-x top center;
		}
		#frontHeader{
			color: #fff;
		}
		#frontHeader a{
			color: orange;
		}
		#frontnav a {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:link {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:visited {
			color: #fff;
			text-decoration: none;
		}
		#frontnav a:hover {
			color: #fff;
			text-decoration: underline;
		}
		.error { opacity: 0.7; font: bold 16px Helvetica, Arial, Sans-serif; text-shadow: 0px 2px 6px #333; color: red; width: 70%; margin: auto; margin-bottom: 20px; border: 2px solid #666; border-radius: 7px; padding: 15px; text-align: center; background: url(<?php echo $baseurl; ?>/images/common/bg_orange.png) repeat-x center center;}
		.message { opacity: 0.7; font: bold 16px Helvetica, Arial, Sans-serif; text-shadow: 0px 2px 6px #333; color: #fff; width: 70%; margin: auto; margin-bottom: 20px; border: 2px solid #666; border-radius: 7px; padding: 15px; text-align: center; background: url(<?php echo $baseurl; ?>/images/common/bg_orange.png) repeat-x center center;}
	</style>
	
	<?php
		$sql = "SELECT g.GameTitle, p.name, p.id AS platformid, p.icon, g.id, b.filename FROM games AS g, banners AS b, platforms AS p, ratings AS r WHERE r.itemid = b.id AND g.id = b.keyvalue AND r.itemtype = 'banner' AND b.keytype = 'fanart' AND g.platform = p.id GROUP BY g.GameTitle, p.name, g.id, b.filename    HAVING AVG(r.rating) = 10 ORDER BY RAND() LIMIT 6";
		$result = mysql_query($sql);
		if ($result !== FALSE) {
		$rows = mysql_num_rows($result);
	?>
	<script type="text/javascript">
		var photos = [
			<?php
				$colours = array("orange", "blue", "purple", "green", "red", "yellow");
				$colourCount = 0;
				$gameRowCount = 0;
				$imageUrls = array();
				
				// Include JPEG Reducer Class
                include('simpleimage50.php');
				
				while ($game = mysql_fetch_object($result)) {

					// Get Game Rating
					$ratingquery	= "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='game' AND itemid=$game->id";
					$ratingresult = mysql_query($ratingquery) or die('Query failed: ' . mysql_error());
					$rating = mysql_fetch_object($ratingresult);					
					
					if($gameRowCount != $rows - 1) 
					{
							// Recompress Fanart to 50% Jpeg Quality and save to front page image cache
							if(!file_exists("banners/_frontcache/$game->filename"))
							{
									$image = new SimpleImage();
									$image->load("banners/$game->filename");
									$image->save("banners/_frontcache/$game->filename");
							}
							
							$imageUrls[] = "banners/_frontcache/$game->filename";
					?>
							{
									"title" : "<?=$game->GameTitle?>",
									"cssclass" : "<?=$colours[$colourCount]?>",
									"image" : "banners/_frontcache/<?=$game->filename?>",
									"text" : "<?=$game->name?>",
									"icon" : "<?= $game->icon; ?>",
									"platformid" : "<?= $game->platformid; ?>",
									"rating" : "<?php for ($i = 2; $i <= 10; $i = $i + 2) {	if ($i <= $rating->average) { print '<img src=\'images/game/star_on.png\' width=15 height=15 border=0>'; }	else if ($rating->average > $i - 2 && $rating->average < $i) { print '<img src=\'images/game/star_half.png\' width=15 height=15 border=0>'; } else {	print '<img src=\'images/game/star_off.png\' width=15 height=15 border=0>'; } } ?>",
									"url" : '<?= $baseurl; ?>/game/<?=$game->id?>/',
									"urltext" : 'View Game'
							},
					<?php
							if($colourCount != 5)
							{
									$colourCount++;
							}
							else
							{
									$colourCount = 0;
							}
							$gameRowCount++;
					}
					else
					{
							// Recompress Fanart to 50% Jpeg Quality and save to front page image cache
							if(!file_exists("banners/_frontcache/$game->filename"))
							{
									$image = new SimpleImage();
									$image->load("banners/$game->filename");
									$image->save("banners/_frontcache/$game->filename");
							}
						
							$imageUrls[] = "banners/_frontcache/$game->filename";
					?>
							{
									"title" : "<?=$game->GameTitle?>",
									"cssclass" : "<?=$colours[$colourCount]?>",
									"image" : "banners/_frontcache/<?=$game->filename?>",
									"text" : "<?=$game->name?>",
									"icon" : "<?= $game->icon; ?>",
									"rating" : "<?php for ($i = 2; $i <= 10; $i = $i + 2) {	if ($i <= $rating->average) { print '<img src=\'images/game/star_on.png\' width=15 height=15 border=0>'; }	else if ($rating->average > $i - 2 && $rating->average < $i) { print '<img src=\'images/game/star_half.png\' width=15 height=15 border=0>'; } else {	print '<img src=\'images/game/star_off.png\' width=15 height=15 border=0>'; } } ?>",
									"url" : '<?= $baseurl; ?>/game/<?=$game->id?>/',
									"urltext" : 'View Game'
							}
					<?php
							if($colourCount != 2)
							{
									$colourCount++;
							}
							else
							{
									$colourCount = 0;
							}
					}
				}
	?>
		];
	</script>
	<?php
		}
	?>
</head>
<body>

	<div id="frontHeader" style="height: 78px; position: absolute; top 0px; width: 100%; z-index: 300; background: url(/images/bg_bannerws-thin-glass-strips.png) repeat-x center center;">
		<div id="frontBanner" style="width: 880px; margin: auto;">
			<p style="position: absolute; top: 10px; right: 15px; font-family:Arial; font-size:10pt;">
				<?php if ($loggedin) {
					$msgQuery = mysql_query(" SELECT id FROM messages WHERE status = 'new' AND messages.to = '$user->id' ");
					$msgCount = mysql_num_rows($msgQuery);
					?><a href="<?= $baseurl ?>/messages/">Messages</a> <?php if($msgCount > 0) { echo"<span style=\"color: Chartreuse;\">($msgCount)</span>"; } else { echo "($msgCount)"; } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/favorites/">Favorites</a> (<?php if($user->favorites != ""){ echo count(explode(",", $user->favorites)); } else{ echo "0"; } ?>) <span style="color: #ccc;">|</span> <?php if ($adminuserlevel == 'ADMINISTRATOR') { ?> <a href="<?= $baseurl ?>/admincp/">Admin Control Panel</a> <?php } else { ?><a href="<?= $baseurl ?>/userinfo/">My User Info</a><?php } ?> <span style="color: #ccc;">|</span> <a href="<?= $baseurl ?>/?function=Log Out">Logout</a>
				<?php } else { ?>
					<a href="<?= $baseurl ?>/login/">Login</a> <span style="color: #ccc;">|</span> New to the site? <a href="<?= $baseurl ?>/register/">Register here!</a>
				<?php } ?>
			</p>
			<a href="<?php echo $baseurl; ?>/" title="An open database of video games">
				<img src="<?php echo $baseurl; ?>/images/bannerws-thin-glass.png" style="border-width: 0px" />
			</a>
		</div>
	</div>
	
	<div style="position: absolute; top: 78px; background: url(<?php echo $baseurl; ?>/images/bg_banner-shadow.png) repeat-x center center; height: 15px; width: 100%; z-index: 200;"></div>
	
	<div id="messages" style="position: absolute; top: 160px; width: 100%;">
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	</div>
	
	<div id="frontContentWrapper" style="position: absolute; top: 34%; width: 100%; height: 200px;  z-index: 200;">
	
		<div id="frontContent" style="opacity: 1; width: 600px; height: 160px; padding: 10px 30px; margin: auto; background: url(<?php echo $baseurl; ?>/images/bg_frontsearch.png) repeat-x center center; border-radius: 16px; border: 0px solid #333;">
		
			<h1 style="text-align: center; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size:26px; text-shadow: 0px 2px 6px #333; color:#fff; letter-spacing: 2px;">
			<?php
				$gamecountResult = mysql_query(" SELECT id FROM games ");
				$gamecount = mysql_num_rows($gamecountResult);
				echo number_format($gamecount) . " games and counting....";
			?>
			</h1>
			
			<div id="searchbox" style="padding: 16px 0px; text-align: center;">
				<form id="search" action="<?= $baseurl ?>/search/">
					<input id="frontGameSearch" name="string" type="text" style="height: 30px; padding: 0px; width: 440px; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 20px; text-shadow: 0px 2px 6px #666; color: #333; background: url(<?php echo $baseurl; ?>/images/common/bg_glass.png) no-repeat center center; color: #fff;  border: 1px solid #eee;" />
					<input type="submit" value="Search" style="height: 30px; width: 100px; vertical-align: 2px; padding: 0px; font-size: 18px; text-shadow: 0px 2px 6px #666; color: #fff; background: url(<?php echo $baseurl; ?>/images/common/bg_glass.png) no-repeat center center; border-radius: 6px; border: 1px solid #eee;" />
					<input type="hidden" name="function" value="Search" />
				</form>
			</div>
			
			<div id="frontnav" style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 20px; text-shadow: 0px 2px 6px #666; color: #fff;">
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<?=$baseur?>/topratedgames/">Games</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<?=$baseur?>/platforms/">Platforms</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<?=$baseur?>/stats/">Stats</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<?=$baseur?>/blog/">Blog</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="http://forums.thegamesdb.net" target="_blank">Forum</a></div>
				<div style="width: 76px; padding: 10px; float: left; text-align: center;"><a href="<?=$baseur?>/api/">API</a></div>
				<div style="clear: both;"></div>
			</div>
			
		</div>
		
	</div>
	
	<div id="navigationBoxes">
		<!-- Navigation boxes will get injected by jQuery -->	
	</div>

	<div id="pictureSlider">
		<!-- Pictures will be injected by jQuery -->
	</div>
	
	<div id="footer" style="position:absolute; width: 100%; bottom:0px; z-index: 200; text-align: center;">
		<div id="footerbarShadow" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar-shadow.png) repeat-x center center; height: 15px;"></div>
		<div id="footerbar" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar.png) repeat-x center center; height: 30px;">
		
			<div id="Terms" style="padding-top: 5px; padding-left: 25px; float: left; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a href="<?=$baseurl?>/terms/" style="color: #333;">Terms &amp; Conditions</a>
			</div>
			
			<div id="theTeam" style="padding-top: 5px; padding-right: 25px; float: right; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a rel="facebox" href="#credits" style="color: #333;">TheGamesDB Team</a>
			</div>
			
			<div style="padding-top: 4px;">
			<a href="http://www.facebook.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png" alt="Visit us on Facebook" title="Visit us on Facebook" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png')" /></a>
			<a href="http://twitter.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png" alt="Visit us on Twitter" title="Visit us on Twitter" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png')" /></a>
			<a href="https://plus.google.com/116977810662942577082/posts" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/google_dark.png" alt="Visit us on Google Plus" title="Visit us on Google Plus"  style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_dark.png')" /></a>
			<a href="<?= $baseurl; ?>/mailshare.php?urlsubject=<?= urlencode("TheGamesDB.net - Home"); ?>&url=<?= urlencode($baseurl); ?>" rel="facebox"><img src="<?= $baseurl ?>/images/common/icons/social/24/share_dark.png" alt="Share via Email" title="Share via Email" style="border: 0px;" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/share_dark.png')" /></a>
			</div>
			
		</div>
	</div>
	
	<div id="credits" style="display: none;">
	<div style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; text-shadow: 0px 2px 6px #666;">
		<h1>The Team</h1>
		<p>Here at TheGamesDB.net we have a small but very passionate and dedicated team.</p>
		<p>We are always striving to find ways to improve this site to provide our users with the best experience possible.</p>
		<p>&nbsp;</p>
		<p><strong>Owner:</strong> Scott Brant <em>(smidley)</em></p>
		<p><strong>Coding &amp; Design:</strong> Alex Nazaruk <em>(flexage)</em></p>
		<p><strong>Coding &amp; Design:</strong> Matt McLaughlin</p>
		<p>&nbsp;</p>
		<p>We would also like to give a big thanks to all our contributers, without your involvement this site wouldn't be as good as it is today.</p>
	</div>
	
	<div style="display:none;">
		<?php
			for($i = 0; $i < count($imageUrls); $i++)
			{
			?>
				<img src="<?=$imageUrls[$i]?>" />
			<?php
			}
			?>
	</div>
	
	<script type="text/javascript">
		$(function() {
			var availableTags = [
				<?php
					if($titlesResult = mysql_query(" SELECT DISTINCT GameTitle FROM games ORDER BY GameTitle ASC; "))
					{
						while($titlesObj = mysql_fetch_object($titlesResult))
						{
							echo " \"$titlesObj->GameTitle\",\n";
						}
					}
				?>
			];
			$( "#frontGameSearch" ).autocomplete({
				source: availableTags,
				select: function(event, ui) { $("#search").submit(); }
			});
		});
	</script>
	
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-16803563-1']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	
	<script type="text/javascript">
		// jQuery Snow Script Instance
		// $(document).snowfall({ flakeCount : 200, maxSpeed : 10, round: true, shadow: true, collection: '#footer', minSize: 2, maxSize: 4 });
	</script>
	
</body>
</html>
	
<?php
}
?>
</html>