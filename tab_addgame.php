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
	<div style="text-align: center;">
		<p>You must select the platform that the game you're submitting corresponds to.</p>
		<p>If the same title is on multiple platforms, they must be made into separate entries.</p>
		<p>Metadata and artwork (boxart especially) changes depending on platform type.</p>
		<p>If the platform you require does not exist you can <a href="http://forums.thegamesdb.net/" style="color: darkorange;">Request a New Platform</a></p>
	</div>

		<!-- Begin Game Add Form -->
		<?php
			$platformResult = mysql_query(" SELECT id, name FROM platforms ORDER BY name ASC ");
		?>
		<div id="addGamePanel" style="width: 600px; background-color: #000; color: #FFF; margin: 40px auto 20px auto; border: 1px solid #666; border-radius: 15px; padding: 5px 0px 15px 0px; font-size: 1.3em;">
		<h3 style="text-align: center;">Enter New Game Info</h3>
		<form action="<?php echo $baseurl;?>/index.php?" method="POST" onsubmit="if( $('#GameTitle').val() != '' && $('#Platform').val() != 'empty' && $('#existsNo').attr('checked') == 'checked') { return true; } else { return false; }">
			<table align="center">
				<tr>
					<td valign="middle" style="text-align: right;"><b>Platform:</b></td>
					<td>
						<select id="Platform" name="cleanPlatform" style="font-size: 1.2em; width: 355px; margin: 5px; padding: 2px;">
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
					<td valign="middle" style="text-align: right;"><b>Game Title:</b></td> 
					<td>
						<input type="text" id="GameTitle" name="GameTitle" placeholder="Enter Game Title..." autocomplete="off" style="font-size: 1.2em; width: 351px; margin: 5px; padding: 2px;" value="<?php if(!empty($passTitle)){echo $passTitle;} ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><p><a class="greyButton" id="checkExistingGames" href="javascript:;" style="font-size: 1.3em; text-align: center; width: 339px; display: block; margin: 5px;"><img src="<?php echo $baseurl; ?>/images/common/icons/refresh_24.png" style="vertical-align: -3px;"/>&nbsp;&nbsp;&nbsp;Check For Existing Game</a></p></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<div id="existingGames" class="gamesList">

						</div>
						
						<div id="existsCheck" style="display: none;">
							<p>Does the game you wanted to add exist in the list above,<br>or one that closely resembles it?</p>
							
							<table>
								<tr>
									<td style="text-align: center;">
										<input type="radio" name="gameExists" id="existsYes" value="yes" style="width: 28px; height: 28px;" />
										<br>
										<label for="existsYes" style="">Yes</label>

									</td>
									<td style="text-align: center;">
										<input type="radio" name="gameExists" id="existsNo" value="no" style="width: 28px; height: 28px;" />
										<br>
										<label for="existsNo">No</label>
									</td>
								</tr>
							</table>
						</div>

						<div id="denySubmitGame" style="display: none;">
							<h3 style="color: darkorange;"><br>This game has already been entered for this platform.<br>To view or modify information about this game,<br>select it from the list above.</h3>
						</div>

						<div id="approveSubmitGame" style="display: none;">
							<h4 style="color: darkorange;">Please press the button below to add the game to the database.</h4>
							<p><input class="greyButton" type="submit" name="function" value="Add Game"></p>
						</div>
					</td>
				</tr>
			</table>
		</form>
		</div>
		<!-- End Game Add Form -->


<script>
	$('#checkExistingGames').click( function() {
		if($('#GameTitle').val().trim() != '' && $('#Platform').val() != 'empty')
		{
			$.post( "<?php echo $baseurl; ?>/scripts/ajax_searchgame.php", "searchterm=" + $('#GameTitle').val() + "&platform=" + $('#Platform').val(), function( data ) {
				if (data.result == 'success')
				{	
				  	var resultsArray = [];

				  	$.each(data.games, function(index, value) {
				  		var currentResult = ['<li>',
					  							'<a href="<?php $baseurl; ?>/game/' + value.id + '">' + value.title + '<br>',
					  								'<span>' + value.platform + '</span>',
					  							'</a>',
					  						'</li>'].join('\n');

					  	resultsArray.push(currentResult);
					});


				  	var resultDisplay = ['<ul>',
											resultsArray.join('\n'),
				  						'</ul>'].join('\n');

					$('#existingGames').html(resultDisplay);
					$('#existingGames').slideDown();
					$('#existsCheck').slideDown();
					
				}
				else
				{
					var resultDisplay = ['<ul>',
											'<li>',
					  							'<a href="javascript:;">No Existing Games Were Found<br>',
					  								'<span>Feel Free To Submit This Game</span>',
					  							'</a>',
					  						'</li>',
				  						'</ul>'].join('\n');

					$('#existingGames').html(resultDisplay);
					$('#existingGames').slideDown();
				}
			}, "json");

			$('html, body').animate({
		        scrollTop: $("#addGamePanel").offset().top
		    }, 2000);
		}
		else
		{
			alert("You must both select a platform and enter a game title before continuing...");
		}
	});

	$('#existsYes').click( function() {
		$('#approveSubmitGame').slideUp();
		$('#denySubmitGame').slideDown();

		$('html, body').animate({
	        scrollTop: $("#addGamePanel").offset().top
	    }, 2000);
	});

	$('#existsNo').click( function() {
		$('#denySubmitGame').slideUp();
		$('#approveSubmitGame').slideDown();

		$('html, body').animate({
	        scrollTop: $("#addGamePanel").offset().top
	    }, 2000);
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