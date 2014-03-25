<?php
	## Workaround Fix for lack of "register globals" in PHP 5.4+
	require_once("../globalsfix.php");


	// Script to Report Object (Image/Game)
	//	-------------------------------------
	// Parameters:
	//		$reportid
	//		$reporttype
	
	include("../include.php");
	include("../modules/mod_userinit.php");
	
	if ($loggedin = 1)
	{
	
		// Look-up Submitted Object in DB
		if (isset($reportid))
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
	
		<h2>Report <?=ucfirst($reporttype;)?></h2>
		
		<p>If you consider this <?=$reporttype;?> in need of moderation by an administrator, then please report it to us using the form below.</p>
		
		<form>
			<p style="font-weight: bold;">Reason for Reporting This <?=ucfirst($reporttype;)?>:</p>
			<select id="reportReason" name="reportReason">
				<?php if($reporttype == "image" ){ ?>
				<option>Image uses a language other than that which matches the game information.</option>
				<option>Image has been added for the correct game but for the incorrect platform</option>
				<option>Image has an unsuitable border, is incorrectly cropped or is on a skew</option>
				<option>Image is of low-quality, has inappropriate dimensions, or is pixellated</option>
				<option>Image contains offensive material such as gross violence or nudity</option>
				<option>Image does not relate to the game for which is has been uploaded</option>
				<option>Image contains a watermark from another site or publication</option>
				<option>Image is heavily stained or has other visual artifacts</option>
				<?}?>

				<?php if($reporttype == "game" ){ ?>
				<option>Game has been added for the incorrect platform</option>
				<option>Game is a duplicate</option>
				<option>Game is a mod/hack of another game</option>
				<option>Game is not a game</option>
				<option>Game is in a different language</option>
				<?}?>
			</select>
			
			<p style="font-weight: bold;">Additional Information: <span style="font-weight: normal; color: #999;">(optional)</span></p>
			<textarea id="reportAdditional" name="reportAdditional" style="width: 500px; height: 200px;"></textarea>
			
			<input type="hidden" id="reportType" name="reportType" value="<?=$reporttype?>" />
			<input type="hidden" id="reportID" name="reportID" value="<?=$reportid?>" />
			<input type="hidden" id="reportUserID" name="reportUserID" value="<?=$user->id?>" />
			
			<p><?=ucfirst($reporttype);?> ID: <?=$reportid?><br />User ID: <?= $user->id ?></p>
			
			<p style="text-align: right;"><a href="javascript:void();" class="deny" onclick="processReportedAsync();">Report <?=ucfirst($reporttype);?></a></p>
			
		</form>
		
		<div style="clear: both;"></div>
		
		<script type="text/javascript">
			function processReportedAsync()
			{
				var reportType = $('#reportType').val();
				var reportID = $('#reportID').val();
				var reportUserID = $('#reportUserID').val();
				var reportReason = $('#reportReason').val();
				var reportAdditional = $('#reportAdditional').val();
				
				$.get('<?= $baseurl; ?>/scripts/reportqueue_process-submission.php?reportType=' + reportType + 'reportID=' + reportImageID + '&reportUserID=' + reportUserID + '&reportReason=' + reportReason + '&reportAdditional=' + reportAdditional ,
					function(data){ 
						if(data == 'Success') {
							alert("Thank you, the " + reportType + " was reported for moderation successfully.");
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