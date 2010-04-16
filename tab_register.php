<div class="section">
<h1>Register</h1>
<form action="<?=$fullurl?>" method="POST">
<div id="red"><?=$errormessage?></div>
<table cellspacing="2" cellpadding="2" border="0" align="center">
<tr>
	<td><b>Username</b></td>
	<td><input type="text" name="username" value="<?=$username?>"></td>
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
	<td><input type="text" name="email" value="<?=$email?>"> (will only be used to send password reset information)</td>
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
	<td></td>
	<td><input type="submit" name="function" value="Register"></td>
</tr>
</table>

</form>
</div>