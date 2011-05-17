<?php
## Connect to the database
include("include.php");

## Start session
session_start();
$time = time();

$message = null;

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
    'addgame'
);
if (!$loggedin && in_array($tab, $secureArea)) {
    header("Location:index.php");
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
    ## Check for exact matches for seriesname
    $GameTitle = mysql_real_escape_string($GameTitle);
    $query = "SELECT * FROM games WHERE GameTitle='$GameTitle'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    ## Insert if it doesnt exist already
    if (mysql_num_rows($result) == 0) {
        $query = "INSERT INTO games (GameTitle, lastupdated) VALUES ('$GameTitle', $time)";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $id = mysql_insert_id();
        // TODO: trace this back and change the name
        seriesupdate($id); ## Update the XML data
        // Add Audit
        $sql = "INSERT INTO audits values(NULL, {$_SESSION['userid']}, 'created', $id, NULL)";
        mysql_query($sql);

        $URL = "$baseurl/?tab=game&id=$id";
        header("Location: $URL");
    } else {
        $errormessage = 'Game Already Exists.';
    }
}

/*
 * Game Functions
 */

if ($function == 'Save Game') {
    $updates = array();
    foreach ($_POST AS $key => $value) {
        if ($key != 'function' && $key != 'button' && $key != 'newshowid' && $key != 'comments' && $key != 'email' && !strstr($key, 'GameTitle_') && !strstr($key, 'Overview_') && $key != 'comments' && $key != 'requestcomments' && $key != 'requestreason') {
            $value = rtrim($value);
            $value = ltrim($value);
            if ($value) {
                if ($key == 'FirstAired') {
                    if (($timestamp = strtotime($value)) === false) {
                        continue;
                    } else {
                        $value = date('Y-m-d', $timestamp);
                    }
                }
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
    $errormessage .= 'Game saved.';

    $id = $newshowid;
    $tab = 'game';
}

if ($function == 'Upload Game Banner') {
    ## Check if the image is the right size
    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
    if ($image_width == 760 && $image_height == 140) {
        if ($image_type == '2') { ## Check if it's a JPEG
            ## Generate the new filename
            if ($subkey == 'graphical') {
                if (file_exists("banners/$subkey/$id-g.jpg")) {
                    $filekey = 2;
                    while (file_exists("banners/$subkey/$id-g$filekey.jpg")) {
                        $filekey++;
                    }
                    $filename = "$subkey/$id-g$filekey.jpg";
                } else {
                    $filename = "$subkey/$id-g.jpg";
                }
            } else {
                if (file_exists("banners/$subkey/$id.jpg")) {
                    $filekey = 2;
                    while (file_exists("banners/$subkey/$id-$filekey.jpg")) {
                        $filekey++;
                    }
                    $filename = "$subkey/$id-$filekey.jpg";
                } else {
                    $filename = "$subkey/$id.jpg";
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
            $errormessage = 'Series banners MUST be JPEG';
        }
    } else {
        $errormessage = 'Series banners MUST be 760px wide by 140px tall';
    }
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

    $errormessage = 'Game deleted.';
    $id = $newshowid;
    $tab = 'mainmenu';
}

if ($function == 'Upload Box Art') {
    $id = mysql_real_escape_string($id);
    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
    $resolution = $image_width . 'x' . $image_height;

    $fileid = 1;
    while (file_exists("banners/boxart/original/$cover_side/$id-$fileid.jpg")) {
        $fileid++;
    }

    $filename = "boxart/original/$cover_side/$id-$fileid.jpg";
    if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {
        ## Insert database record
        $id = mysql_real_escape_string($id);
        $colors = mysql_real_escape_string($colors);
        $query = "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution) VALUES ('boxart', $id, $user->id, $time, '$filename', 1, '$resolution')";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        ## Store the seriesid for the XML updater
        seriesupdate($id);
    }

    $errormessage = "Box art added";
    $tab = 'game';
}

if ($function == 'Upload Fan Art') {
    $id = mysql_real_escape_string($id);

    ## Check if the image is the right size
    list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
    $resolution = $image_width . 'x' . $image_height;
    if ($resolution != '1920x1080' && $resolution != '1280x720') {
        $errormessage .= "Your image is not a valid fan art resolution.<br>";
    }
    if ($image_type != 2) {
        $errormessage .= "Your image MUST be a jpg.<br>";
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
        $vignettefilename = "fanart/vignette/$id-$fileid.jpg";
        if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

            ## Generate the vignette file
            vignette("banners/$filename", "banners/$vignettefilename");

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

        $errormessage = "Fan art added";
    }
    $tab = 'game';
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
    $errormessage = 'Banner Language Changed.';
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
                $message = '<p><em>Thank you for registering with TheGamesDB!</em><p>You will receive an email confirmation with your account information shortly.  Please proceed to the login screen and review our terms and conditions.  If you have any questions, please visit our forums.  We hope you enjoy your stay!</p>';
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
        $message = "This is an automated message.\n\nYour GamesDB password has been reset.  Here is your new login information:\nusername: $db->username\npassword: $newpass\n\nIf you have any questions, please let us know.\nTheGamesDB Crew\n";
        $headers = 'From: ' . $from;
        mail($to, $subject, wordwrap($message, 70), $headers);

        $errormessage = 'Login information has been sent.';
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
        $errormessage = 'Account was successfully updated.';
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
        $errormessage = 'Account was successfully updated (no password change).';
    }
    ## Error... empty emailaddress
    else {
        $errormessage = 'Naughty naughty... an email address is required.';
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
        $errormessage = 'Account was successfully updated.';
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
        $errormessage = 'Account was successfully updated (no password change).';
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
        $errormessage = 'Thankyou for agreeing to the site terms. You may now upload banners';
        $tab = 'mainmenu';
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
    $errormessage = 'Image was successfully deleted.';

    if ($deletebanner->id) {
        ## Delete SQL record
        $query = "DELETE FROM banners WHERE id=$deletebanner->id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        ## Delete ratings
        $query = "DELETE FROM ratings WHERE itemtype='banner' AND itemid=$deletebanner->id";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        ## Delete file
        unlink("banners/$deletebanner->filename");
        unlink("banners/_cache/$deletebanner->filename");

        ## Delete vignette for fan art
        if ($deletebanner->keytype == "fanart") {
            $vignettefilename = str_replace("original", "vignette", $deletebanner->filename);
            unlink("banners/$vignettefilename");
        }

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
    $errormessage = 'Image was successfully deleted.';

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

## Default tab
if ($tab == '') {
    $tab = 'mainmenu';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php
## Redirect if no javascript
        if ($tab != "nojs") {
            print "<noscript><meta http-equiv=\"refresh\" content=\"0; url=index.php?tab=nojs\"/></noscript>\n";
        }
        ?>
        <title>An open database of video games</title>
        <META name="description" content="An open database of video games" />
        <META name="keywords" content="video, games, database, open, dirctory, thegamesdb, api" />
        <link rel="stylesheet" type="text/css" href="/default.css" />
        <link rel="stylesheet" href="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/COLOURloversColorPicker.css" type="text/css" media="all" />
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/base/jquery-ui.css" type="text/css" media="all" />

        <script type="text/JavaScript" src="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/js/COLOURloversColorPicker.js"></script>
        <script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
        <script type="text/javascript">
            $('document').ready(function(){
                var index = 0;
                var images = $('#recent li');
                $($(images).get(index)).show();
                window.setInterval(function(){
                    $($(images).get(index)).fadeOut('slow', function(){
                        if(index == images.length - 1){
                            index = 0;
                        }else{
                            index++;
                        }

                        $($(images).get(index)).fadeIn('slow');
                    });
                }, 6000);
            });

            function confirmSubmit()  {
                var agree=confirm("Are you sure you wish to delete this?");
                if (agree)
                    return true ;
                else
                    return false ;
            }
            function deniedcommentClose() {
                document.getElementById("denied_popup").style.display = "none";
            }
            function requestcommentClose() {
                document.getElementById("request_popup").style.display = "none";
            }
            function TAlimit(s) {
                var maxlength = 255; // Change number to your max length.
                if (s.value.length > maxlength)
                    s.value = s.value.substring(0,maxlength);
            }
            function ShowSeriesName(id) {
                // First, hide all of the series names
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.seriesform.SeriesName_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.seriesform.SeriesName_" + id);
        objectname.style.display='inline';
    }
    function ShowSeriesOverview(id) {
        // First, hide all of the series overviews
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.seriesform.Overview_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.seriesform.Overview_" + id);
        objectname.style.display='inline';
    }
    function ShowEpisodeName(id) {
        // First, hide all of the series names
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.episodeform.EpisodeName_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.episodeform.EpisodeName_" + id);
        objectname.style.display='inline';
    }
    function ShowEpisodeOverview(id) {
        // First, hide all of the series overviews
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.episodeform.Overview_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.episodeform.Overview_" + id);
        objectname.style.display='inline';
    }
    var globalShowSeriesName = this.ShowSeriesName;
    var globalShowSeriesOverview = this.ShowSeriesOverview;
    var globalShowEpisodeName = this.ShowEpisodeName;
    var globalShowEpisodeOverview = this.ShowEpisodeOverview;

    // Function to open a popup and allow child to send data back
    function openChild(file,window, dimX, dimY) {
        childWindow=open(file,window,'resizable=1,location=0,status=0,scrollbars=1,width=' + dimX + ',height=' + dimY);
        if (childWindow.opener == null) childWindow.opener = self;
    }

    var checkobj

    // User ratings (turns stars on and off)
    function UserRating(rating)  {
        for (i=1; i<=10; i++)  {
            if (i <= rating)  {
                var thisimage = eval("document.images.userrating" + i);
                thisimage.src = '<?= $baseurl ?>/images/star_on.png';
            }
            else  {
                var thisimage = eval("document.images.userrating" + i);
                thisimage.src = '<?= $baseurl ?>/images/star_off.png';
            }
        }
    }
    // User ratings (turns stars on and off)
    function UserRating2(prefix,rating)  {
        for (i=1; i<=10; i++)  {
            if (i <= rating)  {
                var thisimage = eval("document.images." + prefix + i);
                thisimage.src = '<?= $baseurl ?>/images/star_on.png';
            }
            else  {
                var thisimage = eval("document.images." + prefix + i);
                thisimage.src = '<?= $baseurl ?>/images/star_off.png';
            }
        }
    }

    //Function to toggle an element
    function toggleDiv(divid){
        if(document.getElementById(divid).style.display == 'none'){
            document.getElementById(divid).style.display = 'block';
        }else{
            document.getElementById(divid).style.display = 'none';
        }
    }

    // Site Terms Agreement Function
    function agreesubmit(el){
        checkobj=el
        if (document.all||document.getElementById){
            for (i=0;i<checkobj.form.length;i++){  //hunt down submit button
                var tempobj=checkobj.form.elements[i]
                if(tempobj.type.toLowerCase()=="submit")
                    tempobj.disabled=!checkobj.checked
            }
        }
    }
    // Site Terms Agreement Function
    function defaultagree(el){
        if (!document.all&&!document.getElementById){
            if (window.checkobj&&checkobj.checked)
                return true
            else{
                alert("Please read/accept terms to submit form")
                return false
            }
        }
    }
    // -->
        </script>

        <script type="text/javascript" src="<?php echo $baseurl; ?>/niftycube.js"></script>
        <script type="text/javascript">
            window.onload=function(){
                Nifty("DIV.section","big");
                Nifty("DIV.footer","big");
                Nifty("DIV.titlesection","big");
            }
        </script>
        <script type="text/javascript">
            function hideElement (elementId) {
                var element;
                if (document.all)
                    element = document.all[elementId];
                else if (document.getElementById)
                    element = document.getElementById(elementId);
                if (element && element.style)
                    element.style.display = 'none';
            }
            function showElement (elementId) {
                var element;
                if (document.all)
                    element = document.all[elementId];
                else if (document.getElementById)
                    element = document.getElementById(elementId);
                if (element && element.style)
                    element.style.display = '';
            }
            function DisplayImporterRow (importerValue)  {
                if (importerValue == 'tv.com')
                    showElement('tvcom');
                else
                    hideElement('tvcom');
            }
        </script>
    </head>
    <body>
        <div id="main">
            <div id="header">
                <p>
                    <?php if ($loggedin): ?>
                        <a href="<?= $baseurl ?>/?function=Log Out">Logout</a>
                    <?php else: ?>
                            <a href="<?= $baseurl ?>/?tab=login">Login</a>. New to the site? <a href="<?= $baseurl ?>/?tab=register">Register here!</a>
                    <?php endif; ?>
                        </p>
                        <a href="<?= $baseurl ?>" title="An open database of video games">
                            <img src="<?= $baseurl ?>/images/bannerws.png" />
                        </a>
                        <div id="nav">
                            <form id="search" action="<?= $baseurl ?>/index.php">
                                <input class="left "type="text" name="string" id="search" value="search" onFocus="this.value=''" />
                                <input type="hidden" name="searchseriesid" id="searchseriesid" />
                                <input type="hidden" name="tab" value="listseries" />
                                <input type="hidden" name="function" value="Search" />

                            </form>
                            <ul>
                                <li id="nav_forum" class="tab"><a target="_blank" href="http://forums.thegamesdb.net"></a></li>
                        <?php if ($loggedin): ?>
                                <li id="nav_submit" class="tab"><a href="<?= $baseurl ?>/?tab=addgame"></a></li>
                        <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div id="content">
                        <div class="error"><?= $errormessage ?></div>
                        <?php if($message): ?>
                        <div class="message"><?= $message ?></div>
                        <?php endif; ?>
                <?php
                                include("tab_$tab.php");
                ?>
                            </div>

                            <div id="footer">

                                <left><font color=#FFFFFF>
								Owner: Scott Brant<br>
								Coding & Design: Matt McGlaughlin</left>
				<center><a href="http://thegamesdb.net/?tab=terms"><font color=#FFFFFF>Terms and Conditions</font></a></center>
								<br>
								<left>Licensing info and additional contributors are listed on our <a href=http://code.google.com/p/thegamesdb/><font color=#FFFFFF>development tracker.</font></a></left>
								<a href="http://twitter.com/thegamesdb" class="right tile" target="_blank" >
                                    <img src="<?= $baseurl ?>/images/twittertile.png" alt="Follow thegamesdb on Twitter"/>
                </a>
                <?php if($tab != 'mainmenu'): ?>
                <div class="ad">
                    <script type="text/javascript"><!--
                        google_ad_client = "ca-pub-9813914365177844";
                        /* Footer New */
                        google_ad_slot = "1296638344";
                        google_ad_width = 468;
                        google_ad_height = 60;
                        //-->
                    </script>
                    <script type="text/javascript"
                            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                    </script>
                </div>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
        </div>

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
    </body>
</html>