<?php

	#####################################################
	## REGISTRATION AND PASSWORD FUNCTIONS
	#####################################################
    
	if ($function == 'Register') {

		$tab = 'register';

        require_once('./extentions/recaptcha/recaptchalib.php');
        
        $recaptcha_resp = null;
        
        if ($_POST["recaptcha_response_field"]) {
                $recaptcha_resp = recaptcha_check_answer ($recaptcha_privatekey,
                                                $_SERVER["REMOTE_ADDR"],
                                                $_POST["recaptcha_challenge_field"],
                                                $_POST["recaptcha_response_field"]);
        }

        ## If Captcha OK
        if ($recaptcha_resp->is_valid) {

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
					$message = '<p style="font-size: x-small !important;"><strong><em>Thank you for registering with TheGamesDB!</em></strong><p>You will receive an email confirmation with your account information shortly.  Please proceed to the <a href="' . $baseurl . '/login/">Login</a> screen and review our terms and conditions.  If you have any questions, please visit our forums.  We hope you enjoy your stay!</p>';
					
					## Email it to the user
		
					$from = "TheGamesDB.net <$mail_username>";
					
					$host = $mail_server;
					
					$to = $username . '<' . $email . '>';
					
					$subject = "[TheGamesDB.net] Your Account Information";
					
					$emailmessage = "
					<html>
						<head>
							<title>TheGamesDB.net</title>
						</head>
						<body>
							<table width=\"802\" align=\"center\" border=\"0\">
								<img src=\"http://thegamesdb.net/email/email-header.jpg\" />
								<div style=\"background-color: #333333; color: #ffffff; padding: 15px; border: 1px solid #000;\">
									<h2>Thank you for registering with TheGamesDB.net.</h2>
									<p>We are proud that you have decided to join our growing community, our focus is on providing a freely accesible wealth of content, and as an entirely open database we rely on users such as yourself for artwork and content contributions.</p>
									<p>You may now log in to the site by visiting: <a href=\"http://thegamesdb.net\" style=\"color: orange;\">http://thegamesdb.net</a></p>
									<hr />
									<p>If you have any questions, or would just like to say hello, come visit us on the <a href=\"http://forums.thegamesdb.net\" style=\"color: orange;\">forums</a>.</p>
									<p><i>TheGamesDB.net Admins</i></p>
								</div>
							</table>
						</body>
					</html>
					";
					
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
					$headers .= "From: $from\n" .
					"Reply-To: $from\n" .
					"Return-path: $from\n";
					
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
        } else {
            $errormessage = 'The reCpatcha was entered incorrectly, please try again.';
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
			
			$from = "TheGamesDB.net <$mail_username>";
			
			$host = $mail_server;
			
			$to = $db->username . '<' . $db->emailaddress . '>';
			
			$subject = "[TheGamesDB.net] Password Reset";
			
			$emailmessage = "
			<html>
				<head>
					<title>TheGamesDB.net</title>
				</head>
				<body>
					<table width=\"802\" align=\"center\" border=\"0\">
						<img src=\"http://thegamesdb.net/email/email-header.jpg\" />
						<div style=\"background-color: #333333; color: #ffffff; padding: 15px; border: 1px solid #000;\">
							<h2>TheGamesDB.net Password Reset.</h2>
							<p>This is an automated message.</p>
							<p>Your password for TheGamesDB.net has been reset.</p>
							<h3 style=\"text-align: center;\">Here is your new login information:</h3>
							<p style=\"text-align: center;\">Username: $db->username<br />Password: $newpass</p>
							<p>You may now log in to the site using your new credentials by visiting: <a href=\"http://thegamesdb.net\" style=\"color: orange;\">http://thegamesdb.net</a></p>
							<p><i>For security reasons, we advise you change your password immediately after logging in on this occasion.</i></p>
							<hr />
							<p>If you have any questions, or are still having issues logging in, please speak to us on the <a href=\"http://forums.thegamesdb.net\" style=\"color: orange;\">forums</a>.</p>
							<p><i>TheGamesDB.net Admins</i></p>
						</div>
					</table>
				</body>
			</html>
			";
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			$headers .= "From: $from\n" .
			"Reply-To: $from\n" .
			"Return-path: $from\n";
			
			mail($to, $subject, wordwrap($emailmessage, 70), $headers);

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
	
?>