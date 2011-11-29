<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>

<?php
if($loggedin == 1)
{
?>
	
<h1 style="text-transform: capitalize;">User Information | <?=$user->username?></h1>
<div style="width: 640px; margin: auto;">
	<p>&nbsp;</p>
	<div style="float:left;">
		<form style="padding: 14px; border: 1px solid #999; background-color: #333;" method="post" action="<?= $baseurl; ?>/userinfo/" enctype="multipart/form-data">
		<h2>User Image...</h2>
		<?php
		$filename = glob("banners/users/" . $user->id . "-*.jpg");
		if(file_exists($filename[0]))
		{
		?>
			<p style="text-align: center;"><img src="<?= $baseurl; ?>/<?= $filename[0]; ?>" alt="Current User Image" title="Current User Image" /></p>
		<?php
			$filename = null;
		}
		else
		{
		?>
			<p style="text-align: center;"><img src="<?= $baseurl; ?>/images/common/icons/user-black_64.png" alt="Current User Image" title="Current User Image" /></p>
		<?php
		}
		?>
			<p style="text-align: center;">
			<input type="file" name="userimage" /><br />
			<input type="hidden" name="function" value="Update User Image" />
			<input type="submit" name="submit" value="Upload Image" /></p>
		</form>
	</div>
    <form  style="float:left; border-left: 1px solid #333; padding-left: 16px; margin-left: 16px;" action="<?=$fullurl?>" method="POST">
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
                        foreach ($languages AS $langid => $langname) {
                            ## If we have the currently selected language
                            if ($user->languageid == $langid) {
                                $selected = 'selected';
                            }
                            ## Otherwise
                            else {
                                $selected = '';
                            }
                            print "<option value=\"$langid\" $selected>$langname</option>\n";
                        }
                        ?>
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

	<div style="clear: both;"></div>
</div>

<?php
}
else
{
?>
	<h1>Oops!</h1>
		<h2 style="text-align: center;">You must be logged in to access your user settings!</h2>
		<p style="text-align: center;">If you haven't already, please make an account with us and then log in.</p>
		<p style="text-align: center;"><a href="<?= $baseurl; ?>/login/" style="color: orange;">Click here to log in</a></p>
<?php
}
?>
	</div>
</div>