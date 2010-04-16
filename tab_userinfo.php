<div class="section">
<h1>User Information | <?=$user->username?></h1>
<form action="<?=$fullurl?>" method="POST">
<div id="red"><?=$errormessage?></div>
<table cellspacing="2" cellpadding="2" border="0">
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
	<td><input type="text" name="email" value="<?=$user->emailaddress?>"></td>
</tr>
<tr>
	<td><b>Preferred Language</b></td>
	<td>
		<select name="languageid" size="1">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($user->languageid == $langid)  {
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
	<td><b>Favorites Display Mode</b></td>
	<td>
		<select name="favorites_displaymode" size="1">
			<option value="banners" <?php if ($user->favorites_displaymode == "banners") print "selected"; ?>>Banners
			<option value="text" <?php if ($user->favorites_displaymode == "text") print "selected"; ?>>Text
			</select>
	</td>
</tr>
<tr>
	<td><b>Account Identifier</b></td>
	<td><input type="text" name="form_uniqueid" value="<?=$user->uniqueid?>" readonly></td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" name="function" value="Update User Information"></td>
</tr>
</table>
</form>
</div>


<div class="section">
<h1>API Keys</h1>
	<table id="listtable" cellspacing=0 cellpadding=2 border=0 width="100%">
	<tr><td class=head>Project</td><td class=head>API Key</td></tr>
<?php
	## Get this user' API information
	$query	= "SELECT * FROM apiusers WHERE userid=$user->id ORDER BY projectname";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($apikey = mysql_fetch_object($result))  {
		print "<tr><td class=odd>$apikey->projectname</td><td class=odd>$apikey->apikey</td></tr>\n";
	}
?>
	</table>
</div>
