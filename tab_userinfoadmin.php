<?php
	if ($adminuserlevel == 'ADMINISTRATOR')  { 

	if ($_SESSION['userlevel'] == 'SUPERADMIN')  {
	  $query = "SELECT * FROM users WHERE id=$id";
	}
	else {
	  $query = "SELECT * FROM users WHERE id=$id AND userlevel = 'USER'";
	}
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$users	= mysql_fetch_object($result);


	if ($users->lastupdatedby_admin)  { 
	$query	= "SELECT * FROM users WHERE id=$users->lastupdatedby_admin";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$adminuser	= mysql_fetch_object($result);
	}
?>

<div class="section">
<?php if ($users->username)  { ?>

<h1>User Information | <?=$users->username?></h1>
<form action="<?=$fullurl?>" method="POST">
<div id="red"><?=$errormessage?></div>
<table cellspacing="2" cellpadding="2" border="0" align="center">
<tr>
	<td><b>Username</b></td>
	<td><input type="text" name="username" value="<?=$users->username?>"></td>
	<td><a href="?tab=artistbannersadmin&amp;id=<?=$users->id?>"><?=$users->username?>'s Banners</a></td>
</tr>
<tr>
	<td><b>Password</b></td>
	<td><input type="password" name="userpass1"></td>
	<td>
	<?php
	if ($users->lastupdatedby_admin)  {
		echo "Last Updated By $adminuser->username";		 
	}
	?>
	</td>
</tr>
<tr>
	<td><b>Re-Enter Password</b></td>
	<td><input type="password" name="userpass2"></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><b>Email Address</b></td>
	<td><input type="text" name="email" value="<?=$users->emailaddress?>"></td>
	<td>&nbsp;</td>
</tr>
<?php if ($_SESSION['userlevel'] == 'SUPERADMIN')  { ?>
<tr>
	<td><b>User Level</b></td>
	<td>
	  <select name="form_userlevel" size="1">
		<option value="USER" <?php if ($users->userlevel == 'USER') { print 'selected'; } ?>>USER</option>
		<option value="ADMINISTRATOR" <?php if ($users->userlevel == 'ADMINISTRATOR') { print 'selected'; } ?>>ADMINISTRATOR</option>
		<option value="SUPERADMIN" <?php if ($users->userlevel == 'SUPERADMIN') { print 'selected'; } ?>>SUPERADMIN</option>
	  </select>
	</td>
	<td>&nbsp;</td>
</tr>
<?php } ?>
<tr>
	<td><b>Preferred Language</b></td>
	<td>
		<select name="languageid" size="1">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($users->languageid == $langid)  {
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
	<td>&nbsp;</td>
</tr>
<tr>
	<td><b>Account Identifier</b></td>
	<td><input type="text" name="form_uniqueid" value="<?=$users->uniqueid?>" readonly></td>
</tr>
<tr>
	<td><b>Banner Limit</b></td>
	<td><input type="text" name="bannerlimit" value="<?=$users->bannerlimit?>"></td>
	<td>Set to 0 to dis-allow uploads</td>
</tr>
<tr>
	<td><b>Account Active</b></td>
	<td>
	  <select name="form_active" size="1">
		<option value="1" <?php if ($users->active == 1) { print 'selected'; } ?>>Yes</option>
		<option value="0" <?php if ($users->active == 0) { print 'selected'; } ?>>No</option>
	  </select>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input type="hidden" name="id" value="<?=$id?>"></td>
	<td><input type="submit" name="function" value="Admin Update User"></td>
	<td>&nbsp;</td>
</tr>
</table>

</form>
<?php } else {ECHO "Either you have a requested a non-existent user or one whose userlevel is his above your ability to edit."; } ?>
</div>
<?php
	} //
	else  {
?>
		<div class="section">
		<h1>Administrators Only</h1>
		</div>
<?php
	}
?>
