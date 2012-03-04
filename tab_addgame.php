<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
<?php
	if ($loggedin) {
?>

	<h1 style="text-align: left;">Add A Game</h1>
	<h2 style="color: #00CC3F;">Adding Rules:</h2>
    <p style="text-align: justify"><img src="<?= $baseurl ?>/images/feature-mario.png" style="float: right;" /><span style="font-weight: bold;">Always check</span> to make sure a game doesn't already exsist before adding it.  If it is found to be a duplicate, it will be deleted.  If you believe you've found a special case where a duplicate should be allowed, please come to the forums and ask first or it will be deleted.  Use the advanced search tool to try and find your game before adding it.  We may have it listed under a name you aren't aware of, so if you know the ID for it, you may be able to find it that way.</p>
    <p style="text-align: justify">Map packs and certain add-on's for games generally don't qualify for their own entry and should be entered under the main game instead.  If you believe an exception should be made, please come to the forums and ask first.</p>
    <p style="text-align: justify">If you ever notice a game you've added has dissapeared and you can't figure out why, please come to the forums and ask.  Do not simply re-add it as it is likely to be deleted again.</p>
    <p style="text-align: justify"><b>DO NOT</b> put non english information into english fields.  If a game is published in another language, and is never translated from that language into english, we still do not want non-english information in the english fields.</p>
    <p style="text-align: justify">Games are added immediately but new entries are monitored and incorrect entries will get deleted.  If in doubt, always come to the forums and ask before attempting to add a game.</p>
	<div style="text-align: center; clear: both;">
		<p style="font-size: 16px; font-weight: bold;">Game Titles should use capital letters where practical.</p>
		<table style="margin: auto;" cellspacing="10" cellpadding="4">
			<tr>
				<td>
					<p style="font-size: 16px;"><span style="font-weight: bold; color: #CC0000"><img src="<?= $baseurl ?>/images/common/icons/cross_16.png" style="vertical-align: -2px; padding-right: 3px;" />Incorrect: </span><br />super mario bros.<br />sonic the hedgehog</p>
				</td>
				<td>
					<p style="font-size: 16px;"><span style="font-weight: bold; color: #00EA33"><img src="<?= $baseurl ?>/images/common/icons/tick_16.png" style="vertical-align: -2px; padding-right: 3px;" />Correct: </span><br />Super Mario Bros.<br />Sonic the Hedgehog</p>
				</td>
			</tr>
		</table>
		<div style="clear: both;"></div>
	</div>
	<br />
	<hr />
	<br />
	<h2 style="color: #FF4F00;">Game Info:</h2>
	<p>You must select the platform that the game you're submitting corresponds to.  If the same title is on multiple platforms, they must be made into separate entries.  Metadata and artwork (boxart especially) changes depending on platform type.</p>						

		<!-- Begin Game Add Form -->
		<?php
			$platformResult = mysql_query(" SELECT id, name FROM platforms ORDER BY name ASC ");
		?>
		<div style="width: 500px; background-color: #000; color: #FFF; margin: 40px auto 20px auto; border: 1px solid #666; border-radius: 15px; padding: 5px 0px 15px 0px">
		<h3 style="text-align: center;">Enter New Game Info</h3>
		<form action="<?php echo $baseurl;?>/index.php?" method="POST" onsubmit="if($('#GameTitle').val() != '' && $('#Platform').val() != 'empty') { return true; } else { alert('You must both select a valid Platform and enter a Game Title to proceed...'); return false;}">
			<table align="center">
				<tr>
					<td valign="top"><b>Platform:</b></td>
					<td>
						<select id="Platform" name="cleanPlatform">
							<option value="empty">Select Platform...</option>
							<?php
								while($platformRow = mysql_fetch_assoc($platformResult))
								{
							?>
								<option value="<?php echo $platformRow["id"]; ?>"<?php if($passPlatform == $platformRow["id"]) {echo " selected";}?>><?php echo $platformRow["name"]; ?></option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr> 
					<td valign="top"><b>Game Title:</b></td> 
					<td><input type="text" id="GameTitle" name="GameTitle" size="46" value="<?php if(!empty($passTitle)){echo $passTitle;} ?>"></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><a href="http://forums.thegamesdb.net/" style="color: #fff;">Request a new platform option</a></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="submit" name="function" value="Add Game"></td>
				</tr>
			</table>
		</form>
		</div>
		<!-- End Game Add Form -->


<script>
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
		$( "#GameTitle" ).autocomplete({
			source: availableTags
		});
	});
</script>
<?php
	}
	else {
?>

			<h1>Oops!</h1>
			<h2 style="text-align: center;">You must be logged in to add a new game!</h2>
			<p style="text-align: center;">If you haven't already, please make an account with us and then log in.</p>
			<p style="text-align: center;"><a href="<?= $baseurl; ?>/login/" style="color: orange;">Click here to log in</a></p>
	
<?php
	}
?>

	</div>
</div>