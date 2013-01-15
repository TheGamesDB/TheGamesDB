<?php

	## Start session
	session_start();

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
    	    //remove in case just one of the 2 is present
    	    setcookie('cookiepass','', -1);
    	    setcookie('cookieid', '', -1);
		}
	} else {
	    //remove in case just one of the 2 is present
	    setcookie('cookiepass','', -1);
	    setcookie('cookieid', '', -1);
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

?>