<?php
	## Connect to the database
	include("include.php");	

	## Start session
	session_start();
	$time = time();


	#####################################################
	## COOKIE STUFF - AUTOMATIC LOGIN
	#####################################################
	## Check if the id and pass match a user
	if ($cookieid && $cookiepass)  {
		$cookieid	= mysql_real_escape_string($cookieid);
		$cookiepass	= mysql_real_escape_string($cookiepass);
		$query		= "SELECT * FROM users WHERE id=$cookieid AND userpass='$cookiepass'";
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
		$cookieuser	= mysql_fetch_object($result);
		if ($cookieuser->id)  {
			$_SESSION['userid'] = $cookieuser->id;
			$_SESSION['password'] = $cookieuser->userpass;
			$_SESSION['userlevel'] = $cookieuser->userlevel;
			$loggedin = 1;
		}
		else  {
			unset($_SESSION['userid']);
			unset($_SESSION['password']);
			unset($_SESSION['userlevel']);
			$loggedin = 0;
		}
	}



	#####################################################
	## LOGIN FUNCTIONS
	#####################################################
	$loggedin = 0;	## Just in case
	## If they're attempting to log in
	if ($function == 'Log In')  {
		## Verify their credentials
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		$query	= "SELECT * FROM users WHERE username='$username' AND userpass=PASSWORD('$password')";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$user	= mysql_fetch_object($result);
	if ($user->lastupdatedby_admin)  { 
		$query	= "SELECT * FROM users WHERE id=$user->lastupdatedby_admin";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$adminuser	= mysql_fetch_object($result);
	}

		## If their info isn't found, let them know
		if (!isset($user->id))  {
			$errormessage = 'Incorrect login info.';
			$loggedin = 0;
		}

		## If their is deactivated let them know
		elseif ($user->active == 0)  {
			$errormessage = 'Your account is de-activated. If you believe this has happened in error contact <a href="mailto:'.$adminuser->emailaddress.'">'.$adminuser->username.'</a>';
			$loggedin = 0;
		}
		## Otherwise, store their session variables
		else  {
			$_SESSION['userid'] = $user->id;
			$_SESSION['password'] = $user->userpass;
			$_SESSION['userlevel'] = $user->userlevel;
			$loggedin = 1;
			if ($user->banneragreement == 1) {
				$tab = 'mainmenu';
			}
			else {
				$tab = 'agreement';
			}
		}


		## If they're logged in at this point, store a cookie
		if ($loggedin == 1 && $setcookie == 'on')  {
			setcookie('cookieid', $user->id, time()+86400*365);
			setcookie('cookiepass', $user->userpass, time()+86400*365);
		}
	}

	## If they're attempting to log out
	else if ($function == 'Log Out')  {
		unset($_SESSION['userid']);
		unset($_SESSION['password']);
		unset($_SESSION['userlevel']);
		setcookie('cookieid', "", 0);
		setcookie('cookiepass', "", 0);
		$loggedin = 0;
		$tab = 'mainmenu';
	}

	## If they're already logged in
	else if (isset($_SESSION['userid']))  {
		$loggedin_userid = $_SESSION['userid'];
		$loggedin_password = $_SESSION['password'];

		## Verify their credentials
		$loggedin_userid = mysql_real_escape_string($loggedin_userid);
		$loggedin_password = mysql_real_escape_string($loggedin_password);
		$query	= "SELECT * FROM users WHERE id=$loggedin_userid AND userpass='$loggedin_password'";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$user	= mysql_fetch_object($result);

		## If their info isn't found, remove session variables
		if (!isset($user->id))  {
			unset($_SESSION['userid']);
			unset($_SESSION['password']);
			unset($_SESSION['userlevel']);
			$loggedin = 0;
		}

		## Otherwise, mark them as logged in
		else  {
			$loggedin = 1;
		}
	}

	## If they're already logged out
	else  {
		$loggedin = 0;
	}

	## Administrator and SuperAdmin variable
	global $adminuserlevel;
	$adminuserlevel = '';
	if ($_SESSION['userlevel'] == 'ADMINISTRATOR' OR $_SESSION['userlevel'] == 'SUPERADMIN')  {
		$adminuserlevel = 'ADMINISTRATOR';
	}

	#####################################################
	## Language stuff
	#####################################################

	## Get list of languages and store array
	global $languages;
	global $lid;
	$query	= "SELECT * FROM languages ORDER BY name";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while($lang = mysql_fetch_object($result))  {
		$languages[$lang->id] = $lang->name;
	}

	## Set the default language
	if (!isset($lid))  {
		if ($user->languageid)  {
			$lid = $user->languageid;  ## user preferred language
		}
		else  {
			$lid = 1;  ## English
		}
	}

	#####################################################
	## MAIN MENU FUNCTIONS
	#####################################################
	if ($function == 'Add Game')  {
		## Check for exact matches for seriesname
		$GameTitle = mysql_real_escape_string($GameTitle);
		$query	= "SELECT * FROM games WHERE GameTitle='$GameTitle'";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Insert if it doesnt exist already
		if (mysql_num_rows($result) == 0)  {
			$query	= "INSERT INTO games (GameTitle, lastupdated) VALUES ('$GameTitle', $time)";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$id = mysql_insert_id();
                        // TODO: trace this back and change the name
			seriesupdate($id); ## Update the XML data

			$query	= "INSERT INTO translation_seriesname (seriesid, languageid, translation) VALUES ($id, $lid, '$GameTitle')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			
			$URL = "/?tab=game&id=$id";
            header ("Location: $URL");
		}
		else {
			$errormessage = 'Game Already Exists.';
		}
	}


	#####################################################
	## SERIES FUNCTIONS
	#####################################################
	if ($function == 'Save Series')  {
        $updates = array();
        foreach ($_POST AS $key => $value)  {
            if ($key != 'function' && $key != 'button' && $key != 'newshowid' && $key != 'comments' && $key != 'email' && !strstr($key, 'GameTitle_') && !strstr($key, 'Overview_')&& $key != 'comments' && $key != 'requestcomments'  && $key != 'requestreason')  {
                $value = rtrim($value);
                $value = ltrim($value);
                if ($value)  {
                    if ($key == 'FirstAired')  {
                        if (($timestamp = strtotime($value)) === false) {
                            continue;
                        } else {
                            $value = date ('Y-m-d', $timestamp);
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
        $GameTitle = ltrim($_POST["GameTitle_$lid"]);
        $GameTitle = rtrim($GameTitle);
        if ($GameTitle)  {
            $GameTitle = mysql_real_escape_string($GameTitle);
            array_push($updates, "GameTitle='$GameTitle'");
        }
        else  {
            array_push($updates, "GameTitle=NULL");
        }
        $Overview = ltrim($_POST["Overview_$lid"]);
        $Overview = rtrim($Overview);
        if ($Overview)  {
            $Overview = mysql_real_escape_string($Overview);
            array_push($updates, "Overview='$Overview'");
        }
        else  {
            array_push($updates, "Overview=NULL");
        }

        ## Join the fields and run the query
        $updatestring = implode(', ', $updates);
        $newshowid = mysql_real_escape_string($newshowid);
        $query = "UPDATE games SET $updatestring WHERE id=$newshowid";
        $result	= mysql_query($query) or die('Query failed: ' . mysql_error());

            ## Update translations of GameTitle
            $query = "DELETE FROM translation_seriesname WHERE seriesid=$newshowid";
            $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
            foreach ($languages AS $langid => $langname)  {
                    $value = mysql_real_escape_string($_POST["GameTitle_$langid"]);
                    if ($value != '')  {
                            $query = "INSERT INTO translation_seriesname (translation, seriesid, languageid) VALUES ('$value', $newshowid, $langid)";
                            $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
                    }
            }

            ## Update translations of Series Overview
            $query = "DELETE FROM translation_seriesoverview WHERE seriesid=$newshowid";
            $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
            include('langDetect.php');
            foreach ($languages AS $langid => $langname)  {
                $value = mysql_real_escape_string($_POST["Overview_$langid"]);
                if ($value != '')  {
                    $obj = new LangDetect($value, 1);
                    $langdetect = $obj->Analyze();
                    if (ucfirst($langdetect) == $langid || $langid == 9 || $langid == 10){ ##Doesn't check Norwegian or Danish languages as they are similar and get misdetected too much
                        $query = "INSERT INTO translation_seriesoverview (translation, seriesid, languageid) VALUES ('$value', $newshowid, $langid)";
                        $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
                    } else {
                        $result = mysql_query("SELECT * FROM languages WHERE id = ".ucfirst($langdetect)) or die('Query failed get seasons: ' . mysql_error());
                        $detectedlang = mysql_fetch_object($result)->name;
                        $result = mysql_query("SELECT * FROM languages WHERE id = $langid") or die('Query failed get seasons: ' . mysql_error());
                        $enteredlang = mysql_fetch_object($result)->name;
                        $errormessage .= "You attempted to enter an $detectedlang overview into the $enteredlang overview field. Please pick the correct language and try again. If the language detected is wrong please come to the forums and let us know.<br/>";
                    }
                }
            }

            seriesupdate($newshowid); ## Update the XML data
            $errormessage .= 'Series info saved.';

            $id = $newshowid;
            $tab = 'game';
	}

	if ($function == 'Add Season')  {
		$seasonint = intval($Season);

			## Check for exact matches for seriesid/season
			$id = mysql_real_escape_string($id);
			$seasonint = mysql_real_escape_string($seasonint);
			$query	= "SELECT * FROM tvseasons WHERE seriesid=$id AND season=$seasonint";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Insert if it doesnt exist already
			if (mysql_num_rows($result) == 0)  {
				$query	= "INSERT INTO tvseasons (seriesid, season) VALUES ($id, $seasonint)";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			}

			## Store the seriesid for the XML updater
			seriesupdate($id);
	}

	if ($function == 'Upload Series Banner')  {
		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr)	= getimagesize($_FILES['bannerfile']['tmp_name']);
		if ($image_width == 758 && $image_height == 140)  {
		  if ($image_type == '2')  { ## Check if it's a JPEG
			## Generate the new filename
			if ($subkey == 'graphical')  {
				if (file_exists("banners/$subkey/$id-g.jpg"))  {
					$filekey = 2;
					while (file_exists("banners/$subkey/$id-g$filekey.jpg"))  {
						$filekey++;
					}
					$filename = "$subkey/$id-g$filekey.jpg";
				}
				else  {
					$filename = "$subkey/$id-g.jpg";
				}
			}
			else  {
				if (file_exists("banners/$subkey/$id.jpg"))  {
					$filekey = 2;
					while (file_exists("banners/$subkey/$id-$filekey.jpg"))  {
						$filekey++;
					}
					$filename = "$subkey/$id-$filekey.jpg";
				}
				else  {
					$filename = "$subkey/$id.jpg";
				}
			}
			if ($subkey == 'blank') { $languageid = '0';} 
			## Rename/move the file
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

				## Insert database record
				$id = mysql_real_escape_string($id);
				$subkey = mysql_real_escape_string($subkey);
				$query	= "INSERT INTO banners (keytype, keyvalue, userid, subkey, dateadded, filename, languageid) VALUES ('series', $id, $user->id, '$subkey', $time, '$filename', '$languageid')";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Reset the missing banner count
				$query	= "UPDATE games SET bannerrequest=0 WHERE id=$id";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Store the seriesid for the XML updater
				seriesupdate($id);
			}
		  }
		  else  {
			  $errormessage = 'Series banners MUST be JPEG';
  		  }
		}
		else  {
			$errormessage = 'Series banners MUST be 758px wide by 140px tall';
		}
	}

	if ($function == 'Delete Series' && $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "DELETE FROM games WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$query = "DELETE FROM translation_seriesname WHERE seriesid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$query = "DELETE FROM translation_seriesoverview WHERE seriesid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Store the seriesid for the XML updater
		seriesupdate($newshowid);
		$query = "INSERT INTO deletions (path) VALUES ('data/series/$id')";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$errormessage = 'Series deleted.';
		$id = $newshowid;
		$tab = 'mainmenu';

	}

	if ($function == 'Upload Fan Art')  {
		$id = mysql_real_escape_string($id);

		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($_FILES['bannerfile']['tmp_name']);
		$resolution = $image_width . 'x' . $image_height;
		if ($resolution != '1920x1080' && $resolution != '1280x720')  {
			$errormessage .= "Your image is not a valid fan art resolution.<br>";
		}
		if ($image_type != 2)  {
			$errormessage .= "Your image MUST be a jpg.<br>";
		}
		if (($resolution == '1920x1080' && filesize($_FILES['bannerfile']['tmp_name']) / 1024 > 800) || ($resolution == '1280x720' && filesize($_FILES['bannerfile']['tmp_name']) / 1024 > 600))  {
			$errormessage .= "Your image exceeds the size restrictions.<br>";
		}

		## No errors, so we can process it
		if ($errormessage == "")  {

			## Generate the new filename
			$fileid = 1;
			while (file_exists("banners/fanart/original/$id-$fileid.jpg"))  {
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
				$query	= "INSERT INTO banners (keytype, keyvalue, userid, dateadded, filename, languageid, resolution, colors) VALUES ('fanart', $id, $user->id, $time, '$filename', 7, '$resolution', '$colors')";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Store the seriesid for the XML updater
				seriesupdate($id);
			}
		}
		$tab = 'series';
		$errormessage = "Fan art added";
	}

	if ($function == 'Request TV.com Update')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$requestcomments = mysql_real_escape_string($requestcomments);		
		$query = "UPDATE games SET forceupdate=1, updateID=$user->id, requestcomment='$requestcomments'  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$errormessage = 'Series update requested.';
	}
	if (($function == 'Force TV.com Update' || $function == 'Approve TV.com Update') && $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET forceupdate=2 WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$errormessage = 'Series update scheduled.';
	}
	if ($function == 'Deny TV.com Update' && $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET forceupdate=0, updateID=0, requestcomment='' WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$errormessage = 'Series update denied.';
		mail ($email, 'Denial of TV.com Update Request for '.$seriesname, "Your original request was: ".stripslashes($requestreason)."\n\nThe reason for denial was:" .stripslashes($comments), "From: ".$user->emailaddress);
	}
	if ($function == 'Lock Series')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET locked='yes', lockedby=$user->id  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if ($function == 'UnLock Series')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE games SET locked='no', lockedby=''  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}


	#####################################################
	## SEASON FUNCTIONS
	#####################################################
	if ($function == 'Add Episode')  {
		$episodeint = intval($EpisodeNumber);

		## Check for exact matches for season/episodenumber
		$seasonid = mysql_real_escape_string($seasonid);
		$episodeint = mysql_real_escape_string($episodeint);
		$query	= "SELECT * FROM tvepisodes WHERE seasonid=$seasonid AND EpisodeNumber=$episodeint";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Insert if it doesnt exist already
		if (mysql_num_rows($result) == 0)  {
			$EpisodeName = mysql_real_escape_string($EpisodeName);
			$seriesid = mysql_real_escape_string($seriesid);

			$query	= "INSERT INTO tvepisodes (seriesid, seasonid, EpisodeNumber, EpisodeName, lastupdated, lastupdatedby) VALUES ($seriesid, $seasonid, $episodeint, '$EpisodeName', $time, $user->id)";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$id = mysql_insert_id();

			$query	= "INSERT INTO translation_episodename (episodeid, languageid, translation) VALUES ($id, 7, '$EpisodeName')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			seriesupdate($seriesid); ## Update the XML data
		}
	}

	if ($function == 'Upload Season Banner')  {
		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr)	= getimagesize($_FILES['bannerfile']['tmp_name']);
		if ($image_width == 400 && $image_height == 578 && $keytype == "season" || $image_width == 758 && $image_height == 140 && $keytype == "seasonwide")  {
		  if ($image_type == '2')  { ## Check if it's a JPEG
			if ($keytype == "season") {$folder = "seasons";}
			elseif ($keytype == "seasonwide") {$folder = "seasonswide";}
			else {}
			## Generate the new filename
				if (file_exists("banners/$folder/$seriesid-$season.jpg"))  {
					$filekey = 2;
					while (file_exists("banners/$folder/$seriesid-$season-$filekey.jpg"))  {
						$filekey++;
					}
					$filename = "$folder/$seriesid-$season-$filekey.jpg";
				}
				else  {
					$filename = "$folder/$seriesid-$season.jpg";
				}

			## Rename/move the file
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

				## Insert database record
				$seriesid = mysql_real_escape_string($seriesid);
				$season = mysql_real_escape_string($season);
				$query	= "INSERT INTO banners (keytype, keyvalue, userid, subkey, dateadded, filename, languageid) VALUES ('$keytype', $seriesid, $user->id, '$season', $time, '$filename', '$languageid')";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Reset the missing banner count
				$seasonid = mysql_real_escape_string($seasonid);	
				$query	= "UPDATE tvseasons SET bannerrequest=0 WHERE id=$seasonid";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Store the seriesid for the XML updater
				seriesupdate($seriesid);
			}
		  }
		  else  {
			  $errormessage = 'Season banners MUST be JPEG';
  		  }
		}
		else  {
			if ($keytype == "season") {$errormessage = 'DVD Cover Style Season banners MUST be 400x578';}
			elseif ($keytype == "seasonwide") {$errormessage = 'Wide Style Season banners MUST be 758x140';}
			else {}
			//$errormessage = 'Season banners MUST be 400x578 or 758x140';
		}
	}
	if ($function == 'Delete Season' && $adminuserlevel == 'ADMINISTRATOR')  {

		$seasonid = mysql_real_escape_string($seasonid);

		## DELETE EPISODES
		$query = "SELECT * FROM tvepisodes WHERE seasonid=$seasonid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($db = mysql_fetch_object($result)) {

			## Delete Episode Image File
			IF ($db->filename) {
				unlink("banners/$db->filename");
				unlink("banners/_cache/$db->filename");
			}
			$query = "DELETE FROM tvepisodes WHERE id=$db->id";
			$results = mysql_query($query) or die('Query failed: ' . mysql_error());

			$query = "DELETE FROM translation_episodename WHERE episodeid=$db->id";
			$results = mysql_query($query) or die('Query failed: ' . mysql_error());

			$query = "DELETE FROM translation_episodeoverview WHERE episodeid=$db->id";
			$results = mysql_query($query) or die('Query failed: ' . mysql_error());
	
			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('data/episodes/$db->id')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		 }

		## Delete The Season Banners
		$query	= "SELECT * FROM tvseasons WHERE id=$seasonid";
		$result = mysql_query($query) or die('Query failed get seasons: ' . mysql_error());
		$season = mysql_fetch_object($result);
		$query	= "SELECT * FROM banners WHERE (keytype='season' OR keytype='seasonwide') AND keyvalue=$seriesid AND subkey='$season->season'";
		$result = mysql_query($query) or die('Query failed getbanners: ' . mysql_error());
		while ($deletebanner = mysql_fetch_object($result)) {
			$query	= "DELETE FROM banners WHERE id=$deletebanner->id";
			$results = mysql_query($query) or die('Query failed deletebanner: ' . mysql_error());
			## Delete file
			unlink("banners/$deletebanner->filename");
			unlink("banners/_cache/$deletebanner->filename");

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$deletebanner->filename')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		}
		
		## Delete The Season
		$query = "DELETE FROM tvseasons WHERE id=$seasonid";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$errormessage = 'Season deleted.';
		seriesupdate($seriesid); ## Update the XML data
		$id = $seriesid;
		$tab = 'series';

	}
	if ($function == 'Lock Season')  {
		## Prepare SQL
		$id = mysql_real_escape_string($seasonid);
		$query = "UPDATE tvseasons SET locked='yes', lockedby=$user->id  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if ($function == 'UnLock Season')  {
		## Prepare SQL
		$id = mysql_real_escape_string($seasonid);
		$query = "UPDATE tvseasons SET locked='no', lockedby=''  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if ($function == 'Renumber Episodes' && $renum2 > 0 && $renum1 < 999999)  {
		## Prepare SQL
		$id = mysql_real_escape_string($seasonid);
		$showid = mysql_real_escape_string($seriesid);
		$shift = mysql_real_escape_string($renum1.$renum2);
		$query = "UPDATE tvepisodes SET episodenumber=episodenumber$shift WHERE seasonid=$id and seriesid=$showid";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());		
		seriesupdate($showid); ## Update the XML data
	}
	#####################################################
	## EPISODE FUNCTIONS
	#####################################################
	if ($function == 'Save Episode')  {
		## Prepare SQL
		$updates = array();

		## Separate the airsbefore info, if set
		if ($_POST["airsbefore"])  {
			print "\n<!-- SAZ: $_POST[airsbefore] --->\n";
			$airsbefore = explode("|", $_POST["airsbefore"]);
			$_POST["airsbefore_season"] = $airsbefore[0];
			$_POST["airsbefore_episode"] = $airsbefore[1];
			unset($_POST["airsbefore"]);

		}


		## Loop through each passed value
		foreach ($_POST AS $key => $value)  {
			if ($key != 'function' && $key != 'button' && !strstr($key, 'EpisodeName_') && !strstr($key, 'Overview_'))  {
				$value = rtrim($value);
				$value = ltrim($value);
				if ($value)  {
					if ($key == 'FirstAired')  {
						if (($timestamp = strtotime($value)) === false) {
							continue;
						}
						else  {
							$value = date ('Y-m-d', $timestamp);
						}
					}
					$key = mysql_real_escape_string($key);
					$value = strip_tags($value, '');
					$value = mysql_real_escape_string($value);
					array_push($updates, "$key='$value'");
				}
				else  {
					array_push($updates, "$key=NULL");
				}
			}
		}

		## To keep things simple, we set EpisodeName and Overview to the English for now
		$EpisodeName = ltrim($_POST["EpisodeName_7"]);
		$EpisodeName = rtrim($EpisodeName);
		if ($EpisodeName)  {
			$EpisodeName = mysql_real_escape_string($EpisodeName);
			array_push($updates, "EpisodeName='$EpisodeName'");
		}
		else  {
			array_push($updates, "EpisodeName=NULL");
		}
		$Overview = ltrim($_POST["Overview_7"]);
		$Overview = rtrim($Overview);
		if ($Overview)  {
			$Overview = mysql_real_escape_string($Overview);
			array_push($updates, "Overview='$Overview'");
		}
		else  {
			array_push($updates, "Overview=NULL");
		}

		array_push($updates, "lastupdated=$time");
		array_push($updates, "lastupdatedby=$user->id");
		$updatestring = implode(', ', $updates);
		$id = mysql_real_escape_string($id);
		$query = "UPDATE tvepisodes SET $updatestring WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Update translations of EpisodeName
		$query = "DELETE FROM translation_episodename WHERE episodeid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		foreach ($languages AS $langid => $langname)  {
			$value = mysql_real_escape_string($_POST["EpisodeName_$langid"]);
			if ($value != '')  {
				$query = "INSERT INTO translation_episodename (translation, episodeid, languageid) VALUES ('$value', $id, $langid)";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			}
		}

		## Update translations of episode Overview
		$query = "DELETE FROM translation_episodeoverview WHERE episodeid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		foreach ($languages AS $langid => $langname)  {
			$value = mysql_real_escape_string($_POST["Overview_$langid"]);
			if ($value != '')  {
				$query = "INSERT INTO translation_episodeoverview (translation, episodeid, languageid) VALUES ('$value', $id, $langid)";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			}
		}

		seriesupdate($seriesid); ## Update the XML data
		$errormessage = 'Episode info saved.';
	}

	if ($function == 'Delete Episode' AND $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "DELETE FROM tvepisodes WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$query = "DELETE FROM translation_episodename WHERE episodeid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$query = "DELETE FROM translation_episodeoverview WHERE episodeid=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Store deletion record
		seriesupdate($seriesid); ## Update the XML data
		$query = "INSERT INTO deletions (path) VALUES ('episodes/$id')";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		$errormessage = 'Episode deleted.';
		$tab = 'season';
	}

	## Move Episode To A Different Season
	if ($function == 'Move Episode' AND $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$updates = array();
		array_push($updates, "seasonid=$seasonid");
		array_push($updates, "lastupdated=$time");
		array_push($updates, "lastupdatedby=$user->id");
		$updatestring = implode(', ', $updates);
		$id = mysql_real_escape_string($id);
		$query = "UPDATE tvepisodes SET $updatestring WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		seriesupdate($seriesid); ## Update the XML data
		$errormessage = 'Episode info saved.';
	}

	## Change A Series Banner's Language
	if ($function == 'Change Language' AND $adminuserlevel == 'ADMINISTRATOR')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE banners SET languageid=$languageid WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$errormessage = 'Banner Language Changed.';
	}
	
	function uploadEpBanner($id, $seriesid, $time, $user){
		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr)	= getimagesize($_FILES['bannerfile']['tmp_name']);
		if ($image_width <= 400 && $image_height <= 300 && $image_width >= 280 && $image_height >= 150)  {
			
			## Checks the aspect ratio of the image and flags it
			$imgaspect = $image_width / $image_height;			
			if ($imgaspect > 1.31 and $imgaspect < 1.35){$episodeflag = 1;} ##4:3 Image
			else if ($imgaspect > 1.739 and $imgaspect < 1.82){$episodeflag = 2;} ##16:9 Image
			else {$episodeflag = 3;} ##Invalid Aspect ratio
			
		  if ($image_type == '2')  { ## Check if it's a JPEG
			## Generate the new filename
				if (file_exists("banners/episodes/$seriesid-$id.jpg"))  {
					return 'Only one episode image is allowed.';
				}
				else  {
					$filename = "episodes/$seriesid-$id.jpg";
				}
			## Rename/move the file
			if (move_uploaded_file($_FILES['bannerfile']['tmp_name'], "banners/$filename")) {

				## Insert database record
				$id = mysql_real_escape_string($id);
				$query = "UPDATE tvepisodes SET filename='$filename', lastupdated=$time, thumb_author=$user->id, EpImgFlag=$episodeflag WHERE id=$id";
				$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

				## Store the seriesid for the XML updater
				seriesupdate($seriesid);
			}
		  }
		  else  {
			  return 'Episode banners MUST be JPEG';
  		  }
		}
		else  {
			return 'Episode banners must be no more than 400px wide and 300px tall, and no less than 280px wide and 150px tall.';
		}
	}

	if ($function == 'Upload Episode Banner')  {
	  $errormessage = uploadEpBanner($id, $seriesid, $time, $user);
	}
	
  function deleteEpBanner($id, $seriesid, $time){
  	## Get the banner info (also verifies username again)
		$id = mysql_real_escape_string($id);
			## Delete SQL record
			## Get this episode's information
			$id = mysql_real_escape_string($id);
			$query	= "SELECT * FROM tvepisodes WHERE id=$id";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$episodes = mysql_fetch_object($result);

			$query	= "UPDATE tvepisodes SET filename='', thumb_author='', lastupdated=$time, EpImgFlag=null WHERE id=$id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			seriesupdate($seriesid);

			## Delete file
			unlink("banners/$episodes->filename");

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$episodes->filename')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			return 'Banner was successfully deleted.';
  }
	
	if ($function == 'Delete Episode Banner')  {
 		$errormessage = deleteEpBanner($id, $seriesid, $time);
	}
	
	if ($function == 'Lock Episode')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE tvepisodes SET locked='yes', lockedby=$user->id  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if ($function == 'UnLock Episode')  {
		## Prepare SQL
		$id = mysql_real_escape_string($id);
		$query = "UPDATE tvepisodes SET locked='no', lockedby=''  WHERE id=$id";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	if ($function == 'Replace Episode Banner')  {
		
		## Check if the image is the right size
		list($image_width, $image_height, $image_type, $image_attr)	= getimagesize($_FILES['bannerfile']['tmp_name']);
		$imgaspect = $image_width / $image_height;		
		if ($image_width <= 400 && $image_height <= 300 && $image_width >= 300 && $image_height >= 170 && $image_type == '2' && (($imgaspect > 1.31 && $imgaspect < 1.35) || ($imgaspect > 1.739 and $imgaspect < 1.82)))  {		
			deleteEpBanner($id, $seriesid, $time);
			uploadEpBanner($id, $seriesid, $time, $user);
			$errormessage = 'Banner Replaced.';
		}
		else {
			$errormessage = 'Please check image, does not meet criteria to replace exsisting image.';
		}
	}

	#####################################################
	## REGISTRATION AND PASSWORD FUNCTIONS
	#####################################################
	if ($function == 'Register')  {
		## Check for exact matches for username
		$username = mysql_real_escape_string($username);
		$userpass1 = mysql_real_escape_string($userpass1);
		$userpass2 = mysql_real_escape_string($userpass2);
		$email = mysql_real_escape_string($email);
		$languageid = mysql_real_escape_string($languageid);
		$uniqueid = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
		$query	= "SELECT * FROM users WHERE username='$username'";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

		## Insert if it doesnt exist already
		if (mysql_num_rows($result) == 0)  {
			if ($userpass1 == $userpass2 && $userpass1 != '')  {
				if ($email)  {
					$query	= "INSERT INTO users (username, userpass, emailaddress, languageid, uniqueid) VALUES ('$username', PASSWORD('$userpass1'), '$email', $languageid, '$uniqueid')";
					$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
					$tab = 'mainmenu';
				}
				else  {
					$errormessage = 'Email address is required.';
				}
			}
			else  {
				$errormessage = 'Passwords do not match or are below the minimum required length.';
			}
		}
		else  {
			$errormessage = 'Username already exists.  Please try another.';
		}
	}


	if ($function == 'Reset Password')  {
		## Get their email address and username
		$email = mysql_real_escape_string($email);
		$query	= "SELECT * FROM users WHERE emailaddress='$email'";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$db	= mysql_fetch_object($result);

		## If we found a match
		if ($db->id)  {
			## Generate a random password
			$newpass = genpassword(8);

			## Set it in the database
			$newpass = mysql_real_escape_string($newpass);
			$query	= "UPDATE users SET userpass=PASSWORD('$newpass') WHERE id='$db->id'";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Email it to the user
			require("libphp-phpmailer/class.phpmailer.php");
			$mail = new PHPMailer();
			$mail->From     = $mail_username;
			$mail->FromName = "TheTVDB.com";
			$mail->Host     = $mail_server;
			$mail->SMTPAuth = true;
			$mail->Username = $mail_username;
			$mail->Password = $mail_password;
			$mail->Mailer   = "smtp";
			$mail->AddAddress($db->emailaddress, $db->username);
			$mail->Subject = "Your account information";
			$mail->Body    = "This is an automated message.\n\nYour online TV database password has been reset.  Here is your new login information:\nusername: $db->username\npassword: $newpass\n\nIf you have any questions, please let me know.\nScott Zsori\nSite Administrator\n";
			$mail->Send();

			$errormessage = 'Login information has been sent.';
		}
		else  {
			$errormessage = 'That address cannot be found.';
		}
	}


	if ($function == 'Update User Information')  {
		$user->languageid = $languageid;

		## Update password and email address
		if ($userpass1 == $userpass2 && $userpass1 != '' && $email != '')  {
			$userpass1 = mysql_real_escape_string($userpass1);
			$userpass2 = mysql_real_escape_string($userpass2);
			$email = mysql_real_escape_string($email);
			$languageid = mysql_real_escape_string($languageid);
			$favorites_displaymode = mysql_real_escape_string($favorites_displaymode);
			$query	= "UPDATE users SET userpass=PASSWORD('$userpass1'), emailaddress='$email', languageid=$languageid, favorites_displaymode='$favorites_displaymode' WHERE id=$user->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$errormessage = 'Account was successfully updated.';
		}
		## Error.. passwords were entered, but don't match
		else if ($userpass1 || $userpass2)  {
			$errormessage = 'Passwords do not match.';
		}
		## Update email address
		else if ($email) {
			$email = mysql_real_escape_string($email);
			$languageid = mysql_real_escape_string($languageid);
			$favorites_displaymode = mysql_real_escape_string($favorites_displaymode);
			$query	= "UPDATE users SET emailaddress='$email', languageid=$languageid, favorites_displaymode='$favorites_displaymode' WHERE id=$user->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$errormessage = 'Account was successfully updated (no password change).';
		}
		## Error... empty emailaddress
		else  {
			$errormessage = 'Naughty naughty... an email address is required.';
		}
	}


		## Administrator's User Update Form
	if ($function == 'Admin Update User')  {
		## Prepare the fields
		$form_userlevel = mysql_real_escape_string($form_userlevel);
		$languageid = mysql_real_escape_string($languageid);
		$bannerlimit = mysql_real_escape_string($bannerlimit);
		$form_active = mysql_real_escape_string($form_active);

		## Update password and all other fields
		if ($userpass1 == $userpass2 && $userpass1 != '' && $email != '' && $username != '')  {
			$username = mysql_real_escape_string($username);
			$userpass1 = mysql_real_escape_string($userpass1);
			$userpass2 = mysql_real_escape_string($userpass2);
			$email = mysql_real_escape_string($email);
			$query	= "UPDATE users SET username='$username', userpass=PASSWORD('$userpass1'), emailaddress='$email', userlevel='$form_userlevel', languageid='$languageid', bannerlimit='$bannerlimit', active='$form_active', lastupdatedby_admin='$user->id' WHERE id='$id'";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$errormessage = 'Account was successfully updated.';
		}
		## Error.. passwords were entered, but don't match
		else if ($userpass1 || $userpass2)  {
			$errormessage = 'Passwords do not match.';
		}
		## Update all fields except password
		else if ($email != '' && $username != '') {
			$username = mysql_real_escape_string($username);
			$email = mysql_real_escape_string($email);
			$query	= "UPDATE users SET username='$username', emailaddress='$email', userlevel='$form_userlevel', languageid='$languageid', bannerlimit='$bannerlimit', active='$form_active', lastupdatedby_admin='$user->id' WHERE id=$id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$errormessage = 'Account was successfully updated (no password change).';
		}
		## Error... empty emailaddress
		else  {
			$errormessage = 'Naughty naughty... an email address is required.';
		}
		$errormessage = $userlevel;
	}


	if ($function == 'Terms Agreement')  {
		if ($agreecheck) {
			$query	= "UPDATE users SET banneragreement=1 WHERE id=$user->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
			$errormessage = 'Thankyou for agreeing to the site terms you may now upload banners';
			$tab= 'mainmenu';
		}
	}


	#####################################################
	## OTHER
	#####################################################

	if ($function == 'Retrieve API Key')  {
		## Prepare values
		$projectname	= mysql_real_escape_string($projectname);
		$projectwebsite	= mysql_real_escape_string($projectwebsite);
		$userid		= mysql_real_escape_string($user->id);
		$apikey		= strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));

		## Insert them
		$query	= "INSERT INTO apiusers (apikey, projectname, projectwebsite, userid) VALUES ('$apikey', '$projectname', '$projectwebsite', $userid)";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$tab	= "userinfo";
	}


	if ($function == 'Delete Banner')  {
		## Get the banner info (also verifies username again)
		$bannerid = mysql_real_escape_string($bannerid);
		if ($adminuserlevel == 'ADMINISTRATOR')  {
			$query	= "SELECT * FROM banners WHERE id=$bannerid";
		}
		else  {
			$query	= "SELECT * FROM banners WHERE id=$bannerid AND userid=$user->id";
		}
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
		$deletebanner	= mysql_fetch_object($result);
		$errormessage = 'Banner was successfully deleted.';

		if ($deletebanner->id)  {
			## Delete SQL record
			$query	= "DELETE FROM banners WHERE id=$deletebanner->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Delete ratings
			$query	= "DELETE FROM ratings WHERE itemtype='banner' AND itemid=$deletebanner->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Delete file
			unlink("banners/$deletebanner->filename");
			unlink("banners/_cache/$deletebanner->filename");

			## Delete vignette for fan art
			if ($deletebanner->keytype == "fanart")  {
				$vignettefilename = str_replace("original", "vignette", $deletebanner->filename);
				unlink("banners/$vignettefilename");
			}

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$deletebanner->filename')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			if ($seriesid)  {
				seriesupdate($seriesid);
			}
			else  {
				seriesupdate($id);
			}
		}
	}

	if ($function == 'Delete Banner Admin')  {
		## Get the banner info (also verifies username again)
		$bannerid	= mysql_real_escape_string($bannerid);
		$query		= "SELECT * FROM banners WHERE id=$bannerid";
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
		$deletebanner	= mysql_fetch_object($result);
		$errormessage	= 'Banner was successfully deleted.';

		if ($deletebanner->id)  {
			## Delete SQL record
			$query	= "DELETE FROM banners WHERE id=$deletebanner->id";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());


			## Delete file
			unlink("banners/$deletebanner->filename");

			## Store deletion record
			$query = "INSERT INTO deletions (path) VALUES ('banners/$deletebanner->filename')";
			$result	= mysql_query($query) or die('Query failed: ' . mysql_error());

			## Store the seriesid for the XML updater
			if ($seriesid)  {
				seriesupdate($seriesid);
			}
			else  {
				seriesupdate($id);
			}
		}
	}


	## This function marks a series as a favorite for a user
	if ($function == 'ToggleFavorite')  {
		## Explode the favorites into an array
		if ($user->favorites)  {
			$userfavorites = explode(",", $user->favorites);
		}
		else  {
			$userfavorites = array();
		}

		## Check if the show is in their favorites list.  If it is, remove it
		if (in_array($id, $userfavorites, 1))  {
			$temparray = array("$id");
			$userfavorites = array_diff($userfavorites, $temparray);
		}
		## Otherwise, add it in
		else  {
			array_push($userfavorites, "$id");
		}
		$userfavorites = array_unique($userfavorites);

		## Update the database
		$favorites = implode(",", $userfavorites);
		$query	= "UPDATE users SET favorites = '$favorites' WHERE id=$_SESSION[userid]";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		$tab = 'series';
		$user->favorites = $favorites;
	}


	## This function sets the user rating for this series
	if ($function == "UserRating")  {
                ## Check for an existing rating
		$type = mysql_real_escape_string($type);
		$itemid = mysql_real_escape_string($itemid);
		$rating = mysql_real_escape_string($rating);

                $query = "SELECT id FROM ratings WHERE itemtype='$type' AND itemid=$itemid AND userid=$_SESSION[userid] LIMIT 1";
                $result = mysql_query($query) or die('Query failed: ' . mysql_error());
                $db = mysql_fetch_object($result);


                ## If we've found a valid user, replace the rating
                if ($db->id)  {
                        $query  = "UPDATE ratings SET rating=$rating WHERE id=$db->id";
                        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
                }


                ## Otherwise, insert a new record
                else  {
                        $query  = "INSERT INTO ratings (itemtype, itemid, userid, rating) VALUES ('$type', $itemid, $_SESSION[userid], $rating)";
                        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
		}

		## Update the XML and set the proper tab
		if ($type == "series")  {
			$tab = "series";
			seriesupdate($id);
		}
		elseif ($type == "episode")  {
			$tab = "episode";
			seriesupdate($seriesid);
			$id = $itemid;
		}
		elseif ($type == "banner")  {
			if ($tab == "series")  {
				seriesupdate($id);
			}
			elseif ($tab == "season")  {
				seriesupdate($seasonid);
			}
		}
	}
	
	## Sends a DMCA takedown request to site amdins email.
	if ($function == "Submit Takedown Request")  {
		//Checks that form was filled in
		if($workname == null){$errmsg .="You must enter an 'Infringed work name'.<br/>";}
		if($link == null){$errmsg .="You must enter a Direct Link to infringement'.<br/>";}
		if($copyown == null){$errmsg .="You must indicate the copywrite owner.<br/>";}
		if($byname == null){$errmsg .="You must enter a company name.<br/>";}
		if($byemail == null){$errmsg .="You must enter an email address.<br/>";}
		if($agree1 != "yes" || $agree2 != "yes"){$errmsg .= "You must tick both tick-boxes.<br/>";}

		//Creates and sends email
		if($errmsg == null){
			$body ="DMCA Takedown request.\r\n\r\n";
			$body = $body."Infrindged Work Name: ".$workname."\r\n";
			$body = $body."Direct Link to Infringment: ".$link."\r\n";
			$body = $body."Copywrite Owner: ".$copyown."\r\n";
			$body = $body."Name / Company: ".$byname."\r\n";
			$body = $body."E-mail: ".$byemail."\r\n";
			$body = $body."Other Info / General Remarks: ".$byremarks."\r\n";
	
		require("libphp-phpmailer/class.phpmailer.php");
			$mail = new PHPMailer();
			$mail->From     = "DMCA@thetvdb.com";
			$mail->FromName = "TheTVDB.com";
			$mail->Host     = $mail_server;
			$mail->SMTPAuth = true;
			$mail->Username = $mail_username;
			$mail->Password = $mail_password;
			$mail->Mailer   = "smtp";
			$mail->AddAddress("scott@thetvdb.com", "TheTVDB.com");
			$mail->Subject = "DMCA Takedown Notice";
			$mail->Body    = $body;
			$mail->Send();
	
		$errmsg = "Takedown Request Recieved. Please allow 5 days for processing.";
		}
	}

	## Default tab
	if ($tab == '')  {
		$tab = 'mainmenu';
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<?php	## Redirect if no javascript
	if ($tab != "nojs")  {
		print "<noscript><meta http-equiv=\"refresh\" content=\"0; url=index.php?tab=nojs\"/></noscript>\n";
	}
?>
<?php
	if ($tab == 'series' or $tab == 'seasonall') {
		titlegenerator($id, 'Null', 'Null', $tab, $lid);
	}
	elseif ($tab == 'season')
	{
		titlegenerator($seriesid, $seasonid, 'Null', $tab, $lid);
	}
	elseif ($tab == 'episode')
	{
		titlegenerator($seriesid, $seasonid, $id, $tab, $lid);
	}
	else {
		print "<title>Online TV Database - An open directory of television shows for HTPC software</title>";
	}
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseurl;?>/default.css">
	<link rel="stylesheet" href="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/COLOURloversColorPicker.css" type="text/css" media="all" />
	<script type="text/JavaScript" src="http://colourlovers.com.s3.amazonaws.com/COLOURloversColorPicker/js/COLOURloversColorPicker.js"></script>
	<script LANGUAGE="JavaScript" type="text/javascript">
	<!--
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
		<?php	## Make a hide statement for each language
			foreach ($languages AS $langid => $langname)  {
				print "document.seriesform.SeriesName_" . $langid . ".style.display='none';\n";
			}
		?>
		// Then, display the one we want
		var objectname = eval("document.seriesform.SeriesName_" + id);
		objectname.style.display='inline';
	}
	function ShowSeriesOverview(id) {
		// First, hide all of the series overviews
		<?php	## Make a hide statement for each language
			foreach ($languages AS $langid => $langname)  {
				print "document.seriesform.Overview_" . $langid . ".style.display='none';\n";
			}
		?>
		// Then, display the one we want
		var objectname = eval("document.seriesform.Overview_" + id);
		objectname.style.display='inline';
	}
	function ShowEpisodeName(id) {
		// First, hide all of the series names
		<?php	## Make a hide statement for each language
			foreach ($languages AS $langid => $langname)  {
				print "document.episodeform.EpisodeName_" . $langid . ".style.display='none';\n";
			}
		?>
		// Then, display the one we want
		var objectname = eval("document.episodeform.EpisodeName_" + id);
		objectname.style.display='inline';
	}
	function ShowEpisodeOverview(id) {
		// First, hide all of the series overviews
		<?php	## Make a hide statement for each language
			foreach ($languages AS $langid => $langname)  {
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
				thisimage.src = '/images/star_on.gif';
			}
			else  {
				var thisimage = eval("document.images.userrating" + i);
				thisimage.src = '/images/star_off.gif';
			}
		}
	}
	// User ratings (turns stars on and off)
	function UserRating2(prefix,rating)  {
		for (i=1; i<=10; i++)  {
			if (i <= rating)  {
				var thisimage = eval("document.images." + prefix + i);
				thisimage.src = '/images/star_on.gif';
			}
			else  {
				var thisimage = eval("document.images." + prefix + i);
				thisimage.src = '/images/star_off.gif';
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
	
	<script type="text/javascript" src="<?php echo $baseurl;?>/xfade2.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl;?>/niftycube.js"></script>
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


<table width="960" cellspacing="0" cellpadding="0" border="0" align="center">

	<tr><td id="ad">
<script type="text/javascript"><!--
google_ad_client = "pub-6312794981679696";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_ad_type = "text_image";
//2007-08-23: TVDB
google_ad_channel = "2794138175";
google_color_border = "ffffff";
google_color_bg = "001D2D";
google_color_link = "B6D415";
google_color_text = "DDDDDD";
google_color_url = "B6D415";
google_ui_features = "rc:10";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	</td></tr>

<?php	## If not logged in
	if ($loggedin != 0)  {
		$headerimage = 'header2.png';
	}
	else  {
		$headerimage = 'header.png';
	}
?>
<tr><td>
	<form method="get" id="searchbox" target="_top">
		<input type="text" name="string" id="search" value="search" onFocus="this.value=''">
		<input type="hidden" name="tab" value="listseries">
		<input type="hidden" name="function" value="Search">
	</form>
	<img src="<?php echo $baseurl;?>/images/<?=$headerimage?>" alt="Television show database" width="762" height="190" border="0" style="z-index: 0" usemap="#headernav">
	<map name="headernav">
		<area shape="rect" coords="11,11,545,140" name="Home" href="/index.php" alt="Home">
		<area shape="rect" coords="620,11,740,28" name="Home" href="/index.php" alt="Home">
		<area shape="rect" coords="620,30,740,46" name="About Us" href="/index.php?tab=aboutus" alt="About Us">
		<area shape="rect" coords="620,48,740,64" name="Wiki" href="http://thetvdb.com/wiki" alt="Wiki">
		<area shape="rect" coords="620,66,740,82" name="Forums" href="http://forums.thetvdb.com" alt="Forum">
		<area shape="rect" coords="620,84,740,100" name="Login" href="/?tab=advancedsearch" alt="Advanced Search">

		<?php	## If logged in
		if ($loggedin == 0)  {
			print '<area shape="rect" coords="620,102,740,118" name="Login" href="/index.php?tab=login" alt="Login">';
		}
		else  {
			print '<area shape="rect" coords="620,102,740,118" name="Account" href="/index.php?tab=userinfo" alt="Account">';
			print '<area shape="rect" coords="620,120,740,136" name="Logout" href="/?function=Log+Out" alt="Log Out">';
		}
		?>

		<area shape="rect" coords="8,166,28,184" name="Television Shows: #" href="/index.php?tab=listseries&amp;letter=OTHER" alt="Other">
		<area shape="rect" coords="28,166,48,184" name="Television Shows: A" href="/index.php?tab=listseries&amp;letter=A" alt="A">
		<area shape="rect" coords="48,166,68,184" name="Television Shows: B" href="/index.php?tab=listseries&amp;letter=B" alt="B">
		<area shape="rect" coords="68,166,88,184" name="Television Shows: C" href="/index.php?tab=listseries&amp;letter=C" alt="C">
		<area shape="rect" coords="88,166,110,184" name="Television Shows: D" href="/index.php?tab=listseries&amp;letter=D" alt="D">
		<area shape="rect" coords="110,166,130,184" name="Television Shows: E" href="/index.php?tab=listseries&amp;letter=E" alt="E">
		<area shape="rect" coords="128,166,148,184" name="Television Shows: F" href="/index.php?tab=listseries&amp;letter=F" alt="F">
		<area shape="rect" coords="148,166,170,184" name="Television Shows: G" href="/index.php?tab=listseries&amp;letter=G" alt="G">
		<area shape="rect" coords="170,166,190,184" name="Television Shows: H" href="/index.php?tab=listseries&amp;letter=H" alt="H">
		<area shape="rect" coords="190,166,206,184" name="Television Shows: I" href="/index.php?tab=listseries&amp;letter=I" alt="I">
		<area shape="rect" coords="206,166,224,184" name="Television Shows: J" href="/index.php?tab=listseries&amp;letter=J" alt="J">
		<area shape="rect" coords="224,166,244,184" name="Television Shows: K" href="/index.php?tab=listseries&amp;letter=K" alt="K">
		<area shape="rect" coords="244,166,262,184" name="Television Shows: L" href="/index.php?tab=listseries&amp;letter=L" alt="L">
		<area shape="rect" coords="262,166,286,184" name="Television Shows: M" href="/index.php?tab=listseries&amp;letter=M" alt="M">
		<area shape="rect" coords="286,166,306,184" name="Television Shows: N" href="/index.php?tab=listseries&amp;letter=N" alt="N">
		<area shape="rect" coords="306,166,328,184" name="Television Shows: O" href="/index.php?tab=listseries&amp;letter=O" alt="O">
		<area shape="rect" coords="328,166,348,184" name="Television Shows: P" href="/index.php?tab=listseries&amp;letter=P" alt="P">
		<area shape="rect" coords="348,166,370,184" name="Television Shows: Q" href="/index.php?tab=listseries&amp;letter=Q" alt="Q">
		<area shape="rect" coords="370,166,390,184" name="Television Shows: R" href="/index.php?tab=listseries&amp;letter=R" alt="R">
		<area shape="rect" coords="390,166,409,184" name="Television Shows: S" href="/index.php?tab=listseries&amp;letter=S" alt="S">
		<area shape="rect" coords="409,166,429,184" name="Television Shows: T" href="/index.php?tab=listseries&amp;letter=T" alt="T">
		<area shape="rect" coords="429,166,449,184" name="Television Shows: U" href="/index.php?tab=listseries&amp;letter=U" alt="U">
		<area shape="rect" coords="449,166,469,184" name="Television Shows: V" href="/index.php?tab=listseries&amp;letter=V" alt="V">
		<area shape="rect" coords="469,166,493,184" name="Television Shows: W" href="/index.php?tab=listseries&amp;letter=W" alt="W">
		<area shape="rect" coords="493,166,513,184" name="Television Shows: X" href="/index.php?tab=listseries&amp;letter=X" alt="X">
		<area shape="rect" coords="513,166,533,184" name="Television Shows: Y" href="/index.php?tab=listseries&amp;letter=Y" alt="Y">
		<area shape="rect" coords="533,166,552,184" name="Television Shows: Z" href="/index.php?tab=listseries&amp;letter=Z" alt="Z">
	</map>
</td></tr>
<tr><td>
	<?php	## Include the tab
		include("tab_$tab.php");
	?>
</td></tr>

<tr><td>
	<div class="footer">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="100%">
						Original database website by Josh Walters (walts81) | Original banner site by Richard Skarsten (t0ffluss)<br>
						Redesign and programming by <a href="mailto:<?php echo cryptemail('scott-tvdb@zsori.com');?>">Scott Zsori</a> and Paul Taylor<br>
						Forum generously hosted by <a href="http://www.maelstromsolutions.com" target="_blank">Maelstrom Technology Solutions</a><br>
						This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/us/">Creative Commons Attribution 3.0 United States License</a>.
					</td>
					<td>
					<a rel="license" href="http://creativecommons.org/licenses/by/3.0/us/"><img alt="Creative Commons License" style="border-width:0" src="http://creativecommons.org/images/public/somerights20.png"/></a>
					</td>
				</tr>
			</table>


	</div>
</td></tr>


	<tr><td id="ad"><br>
<script type="text/javascript"><!--
google_ad_client = "pub-6312794981679696";
//Leaderboard Ad
google_ad_slot = "6493885064";
google_ad_width = 728;
google_ad_height = 90;
//--></script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	</td></tr>
</table>
</body>
</html>
