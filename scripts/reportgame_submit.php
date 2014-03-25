<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");


	// Script to Report Game
	//	-------------------------------------
	// Parameters:
	//		$reportgameid
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1)
	{
	
		// Look-up Submitted Image in DB
		if (isset($reportgameid))
		{
	?>
	
	<style>
		.deny {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			text-decoration: none;
			color: #ffffff;
			padding: 6px 12px;
			background: -moz-linear-gradient(
				top,
				#ffbfbf 0%,
				#cc7272 25%,
				#a80000);
			background: -webkit-gradient(
				linear, left top, left bottom, 
				from(#ffbfbf),
				color-stop(0.25, #cc7272),
				to(#a80000));
			-moz-border-radius: 11px;
			-webkit-border-radius: 11px;
			border-radius: 11px;
			border: 2px solid #ffffff;
			-moz-box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(255,0,0,1);
			-webkit-box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(255,0,0,1);
			box-shadow:
				0px 3px 11px rgba(000,000,000,0.5),
				inset 0px 0px 8px rgba(255,0,0,1);
			text-shadow:
				0px -1px 0px rgba(000,000,000,0.2),
				0px 1px 0px rgba(255,255,255,0.3);
			cursor: pointer;
		}
	</style>
	
	<div>
	
		<h2>Report Game</h2>
		
		<p>If you consider this game is in need of moderation by an administrator, then please report it to us using the form below.</p>
		
		<form>
			<p style="font-weight: bold;">Reason for Reporting This Game:</p>
			<select id="reportReason" name="reportReason">
				<option>Game has been added for the incorrect platform</option>
				<option>Game is a duplicate</option>
				<option>Game is a mod/hack of another game</option>
				<option>Game is not a game</option>
			</select>
			
			<p style="font-weight: bold;">Additional Information: <span style="font-weight: normal; color: #999;">(optional)</span></p>
			<textarea id="reportAdditional" name="reportAdditional" style="width: 500px; height: 200px;"></textarea>
			
			<input type="hidden" id="reportGameID" name="reportGameID" value="<?= $reportgameid ?>" />
			<input type="hidden" id="reportUserID" name="reportUserID" value="<?= $user->id ?>" />
			
			<p>Game ID: <?= $reportgameid ?><br />User ID: <?= $user->id ?></p>
			
			<p style="text-align: right;"><a href="javascript:void();" class="deny" onclick="processReportedGameAsync();">Report Game</a></p>
			
		</form>
		
		<div style="clear: both;"></div>
		
		<script type="text/javascript">
			function processReportedGameAsync()
			{			
				var reportGameID = $('#reportGameID').val();
				var reportUserID = $('#reportUserID').val();
				var reportReason = $('#reportReason').val();
				var reportAdditional = $('#reportAdditional').val();
				
				$.get('<?= $baseurl; ?>/scripts/reportgame_process-submission.php?reportGameID=' + reportGameID + '&reportUserID=' + reportUserID + '&reportReason=' + reportReason + '&reportAdditional=' + reportAdditional,
					function(data){ 
						if(data == 'Success') {
							alert("Thank you, the game was reported for moderation successfully.");
							// Close Facebox Window
							jQuery(document).trigger('close.facebox');
						}
						else
						{
							alert(data);
						}
					}
				);
			}
		</script>
		
	</div>
	
	<?php
	
		}
	}
	
?>