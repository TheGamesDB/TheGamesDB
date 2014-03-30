<?php
	require_once('extentions/recaptcha/recaptchalib.php');
?>

<div id="gameHead">
	
	<?php
		if($errormessage)
		{
	?>
			<div class="error"><?php echo $errormessage ?></div>
	<?php
		}
		if($message) {
	?>
			<div class="message"><?php echo $message ?></div>
	<?php
		}
	?>

<div style="text-align: center; padding: 26px 0px;">
<h1>Register</h1>
<form action="<?= $baseurl ?>/" method="POST">
<p>&nbsp;</p>
<h3>Please fill in your details below to register for an account.</h3>
<table cellspacing="2" cellpadding="2" border="0" align="center" style="text-align: left;">
<tr>
	<td><b>Username</b></td>
	<td><input type="text" name="username" value="<?= $username ?>"></td>
</tr>
<tr>
	<td><b>Password</b></td>
	<td><input type="password" name="userpass1"></td>
</tr>
<tr>
	<td><b>Re-Enter Password</b></td>
	<td><input type="password" name="userpass2"></td>
</tr>
<tr>
	<td><b>Email Address</b></td>
	<td><input type="text" name="email" value="<?= $email ?>"></td>
</tr>
<tr>
	<td><b>Preferred Language</b></td>
	<td>
		<select name="languageid" size="1">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if (7 == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}
					print "<option value=\"$langid\" $selected>$langname</option>\n";
				}
			?>
			</select>
	</td>
</tr>
<tr>
    <td colspan="2">
    	<h3>Enter the words below</h3>
    	<?php echo recaptcha_get_html($recaptcha_publickey); ?>
    </td>
</tr>
<tr>
	<td colspan="2"><input type="submit" name="function" value="Register"></td>
</tr>
</table>

</form>
</div>

	</div>