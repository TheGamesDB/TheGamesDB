<?php
if ($loggedin = 1 && $adminuserlevel == 'ADMINISTRATOR')
{
	##Image Resizing and caching script

	include('simpleimage.php');

	function imageDualResize($filename, $cleanFilename, $wtarget, $htarget)
	{
		if(!file_exists($cleanFilename))
		{
			$dims = getimagesize($filename);
			$width = $dims[0];
			$height = $dims[1];

			while($width > $wtarget || $height > $htarget)
			{
				if($width > $wtarget)
				{
					$percentage = ($wtarget / $width);
				}

				if($height > $htarget)
				{
					$percentage = ($htarget / $height);
				}

				/*if($width > $height)
				{
					$percentage = ($target / $width);
				}
				else
				{
					$percentage = ($target / $height);
				}*/

				//gets the new value and applies the percentage, then rounds the value
				$width = round($width * $percentage);
				$height = round($height * $percentage);
			}

			$image = new SimpleImage();
			$image->load($filename);
			$image->resize($width, $height);
			$image->save($cleanFilename);
			$image = null;
		}
		//returns the new sizes in html image tag format...this is so you can plug this function inside an image tag and just get the
		return "src=\"$baseurl/$cleanFilename\"";
	}

	if (!isset($cptab)) { $cptab = "userinfo"; }
	?>

	<div id="gameHead">

		<?php if($errormessage): ?>
		<div class="error"><?= $errormessage ?></div>
		<?php endif; ?>
		<?php if($message): ?>
		<div class="message"><?= $message ?></div>
		<?php endif; ?>

		<div>
			<h1>Admin Control Panel</h1>
			<p>&nbsp;</p>
		</div>

		<?php
			// Fetch number of uploads to be moderated
			$modQueueResult = mysql_query("SELECT id FROM moderation_uploads");
			$modQueueCount = mysql_num_rows($modQueueResult);

			// Fetch number of reported images to be moderated
			$repQueueResult = mysql_query("SELECT id FROM moderation_reported");
			$repQueueCount = mysql_num_rows($repQueueResult);
		?>

		<div id="controlPanelWrapper">
			<div id="controlPanelNav">
				<ul>
					<li<?php if($cptab == "userinfo"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=userinfo">My User Info</a></li>
					<li<?php if($cptab == "moderationqueue"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=moderationqueue&queuetype=frontboxart">Uploaded Images Moderation Queue</a><br /><a href="<?= $baseurl ?>/admincp/?cptab=moderationqueue" style="text-decoration: none;"><span style="padding: 3px 9px; font-weight: bold; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $modQueueCount ?></span></a></li>
					<li<?php if($cptab == "reportedqueue"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=frontboxart">Reported Images/Games Moderation Queue</a><br /><a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue" style="text-decoration: none;"><span style="padding: 3px 9px; font-weight: bold; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $repQueueCount ?></span></a></li>
					<li<?php if($cptab == "addplatform"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=addplatform">Add New Platform</a></li>
					<li<?php if($cptab == "publishers"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=pubdev">Manage Publishers &amp; Developers</a></li>
					<li<?php if($cptab == "sendpm"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=sendpm">Send PM</a></li>
					<li<?php if($cptab == "platformalias"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=platformalias">Generate Platform Alias's</a></li>
					<li<?php if($cptab == "elasticsearchmanage"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=elasticsearchmanage">Manage ElasticSearch Index</a></li>
				</ul>
			</div>
			<div id="controlPanelContent">
				<?php
					switch($cptab)
					{
						case "userinfo":
							?>
							<h2>User Information | <?=$user->username?></h2>

							<p>&nbsp;</p>

							<div style="float:left;">
								<form style="padding: 14px; border: 1px solid #999; background-color: #444444;" method="post" action="<?= $baseurl; ?>/admincp/" enctype="multipart/form-data">
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

							<form action="<?=$fullurl?>" method="POST" style="float:left; border-left: 1px solid #333; padding-left: 16px; margin-left: 16px;">
								<table cellspacing="2" cellpadding="2" border="0" align="center">
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
							<?php
							break;

						case "moderationqueue":
						?>
								<style>
									button.approve {
										font-family: Arial, Helvetica, sans-serif;
										font-size: 12px;
										color: #ffffff;
										padding: 6px 12px;
										background: -moz-linear-gradient(
											top,
											#c8ffbf 0%,
											#72cc72 25%,
											#22a800);
										background: -webkit-gradient(
											linear, left top, left bottom,
											from(#c8ffbf),
											color-stop(0.25, #72cc72),
											to(#22a800));
										-moz-border-radius: 11px;
										-webkit-border-radius: 11px;
										border-radius: 11px;
										border: 2px solid #ffffff;
										-moz-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										-webkit-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										text-shadow:
											0px -1px 0px rgba(000,000,000,0.2),
											0px 1px 0px rgba(255,255,255,0.3);
										cursor: pointer;
									}

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

									.compare {
										font-family: Arial, Helvetica, sans-serif;
										font-size: 12px;
										text-decoration: none;
										color: #ffffff;
										padding: 6px 12px;
										background: -moz-linear-gradient(
											top,
											#bfcaff 0%,
											#82a1ff 25%,
											#4646fa);
										background: -webkit-gradient(
											linear, left top, left bottom,
											from(#bfcaff),
											color-stop(0.25, #82a1ff),
											to(#4646fa));
										-moz-border-radius: 11px;
										-webkit-border-radius: 11px;
										border-radius: 11px;
										border: 2px solid #ffffff;
										-moz-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										-webkit-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										text-shadow:
											0px -1px 0px rgba(000,000,000,0.2),
											0px 1px 0px rgba(255,255,255,0.3);
										cursor: pointer;
									}
								</style>

								<?php
									// Fetch number of uploads to be moderated
									$frontQueueResult = mysql_query("SELECT id FROM moderation_uploads WHERE imagekey = 'front'");
									$frontQueueCount = mysql_num_rows($frontQueueResult);
									if( empty($frontQueueCount)) { $frontQueueCount = 0; }

									$backQueueResult = mysql_query("SELECT id FROM moderation_uploads WHERE imagekey = 'back'");
									$backQueueCount = mysql_num_rows($backQueueResult);
									if( empty($backQueueCount)) { $backQueueCount = 0; }

									$clearlogoQueueResult = mysql_query("SELECT id FROM moderation_uploads WHERE imagekey = 'clearlogo'");
									$clearlogoQueueCount = mysql_num_rows($clearlogoQueueResult);
									if( empty($clearlogoQueueCount)) { $clearlogoQueueCount = 0; }
								?>

								<p><a href="<?= $baseurl ?>/admincp/?cptab=moderationqueue&queuetype=frontboxart" style="color: orange; font-size: 16;">Front Boxart</a><span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $frontQueueCount ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= $baseurl ?>/admincp/?cptab=moderationqueue&queuetype=backboxart" style="color: orange; font-size: 16;">Rear Boxart</a><span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $backQueueCount ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= $baseurl ?>/admincp/?cptab=moderationqueue&queuetype=clearlogo" style="color: orange; font-size: 16;">ClearLOGO</a><span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $clearlogoQueueCount ?></span></p>

								<?php
									if ($queuetype == "frontboxart")
									{
										$modQueueResult = mysql_query("SELECT m.*, u.username, g.GameTitle, p. NAME AS PlatformName, p.id AS PlatformID FROM moderation_uploads AS m, users AS u, games AS g, platforms AS p WHERE imagekey = 'front' AND m.userID = u.id AND m.gameID = g.id AND g.Platform = p.id ORDER BY dateadded");
										$queueheader = "Front Boxart Moderation Queue";
									}
									else if ($queuetype == "backboxart")
									{
										$modQueueResult = mysql_query("SELECT m.*, u.username, g.GameTitle, p. NAME AS PlatformName, p.id AS PlatformID FROM moderation_uploads AS m, users AS u, games AS g, platforms AS p WHERE imagekey = 'back' AND m.userID = u.id AND m.gameID = g.id AND g.Platform = p.id ORDER BY dateadded");
										$queueheader = "Rear Boxart Moderation Queue";
									}
									else if ($queuetype == "clearlogo")
									{
										$modQueueResult = mysql_query("SELECT m.*, u.username, g.GameTitle, p. NAME AS PlatformName, p.id AS PlatformID FROM moderation_uploads AS m, users AS u, games AS g, platforms AS p WHERE imagekey = 'clearlogo' AND m.userID = u.id AND m.gameID = g.id AND g.Platform = p.id ORDER BY dateadded");
										$queueheader = "ClearLOGO Moderation Queue";
									}

									$modQueueCount = mysql_num_rows($modQueueResult);
								?>

								<table call-padding="0" cell-spacing="0" style="border: 2px solid #444; border-radius: 6px; background-color: #EEEEEE; color: #333333; border-collapse: separate; border-spacing: 2px; border-color: gray; width: 100%;">
									<thead style="text-align: left;">
										<tr>
											<th colspan="3" style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 18px; text-align: center; border-bottom: 1px solid #444;"><?= $queueheader; ?></th>
										</tr>
										<tr>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 16px; border-bottom: 1px solid #444;">Art</th>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 16px; border-bottom: 1px solid #444;">Info</th>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 16px; border-bottom: 1px solid #444;">Date</th>
										</tr>
									</thead>
									<tfoot style="text-align: left;">
										<tr>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#F9F9F9,#C5C5C5); padding: 7px 7px 8px; font-size: 16px; border-top: 1px solid #444;">Art</th>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#F9F9F9,#C5C5C5); padding: 7px 7px 8px; font-size: 16px; border-top: 1px solid #444;">Info</th>
											<th style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#F9F9F9,#C5C5C5); padding: 7px 7px 8px; font-size: 16px; border-top: 1px solid #444;">Date</th>
										</tr>
									</tfoot>
									<tbody>
										<?php
											if ($modQueueCount > 0)
											{
												while ($modQueueObject = mysql_fetch_object($modQueueResult))
												{
										?>
										<tr id="modItem-<?= $modQueueObject->id ?>">
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top; text-align: center;">
												<?php
													if(!file_exists("moderationqueue/_cache/$modQueueObject->filename"))
													{
														WideImage::load("moderationqueue/$modQueueObject->filename")->resize(200, 200)->saveToFile("moderationqueue/_cache/$modQueueObject->filename");
													}
												?>
													<a href="<?= "$baseurl/moderationqueue/$modQueueObject->filename" ?>" rel="facebox"><img src="<?= "$baseurl/moderationqueue/_cache/$modQueueObject->filename" ?>" /></a>
											</td>
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top;"><span style="font-weight: bold;">Game:</span> <a href="<?= $baseurl . "/game/" . $modQueueObject->gameID; ?>" style="color: darkorange;"><?= $modQueueObject->GameTitle; ?></a><br />
												<span style="font-weight: bold;">Platform:</span> <a href="<?= $baseurl . "/platform/" . $modQueueObject->PlatformID; ?>" style="color: darkorange;"><?= $modQueueObject->PlatformName; ?></a><br />
												<span style="font-weight: bold;">Filename:</span> <?= $modQueueObject->filename; ?> <br />
												<span style="font-weight: bold;">Dimensions:</span> <?= $modQueueObject->resolution; ?>px<br />
												<span style="font-weight: bold;">Uploader:</span> <a href="<?= $baseurl . "/artistbanners/?id=" . $modQueueObject->userID; ?>" style="color: darkorange;"><?= $modQueueObject->username; ?></a>
												<p>&nbsp;</p>
												<p style="text-align: right;"><button type="button" class="approve" onclick="$.get('<?= $baseurl; ?>/scripts/modqueue_approve.php?modID=<?= $modQueueObject->id; ?>', function(data){ if(data == 'Success') { $('#modItem-<?= $modQueueObject->id ?> img').css('display', 'none'); $('#modItem-<?= $modQueueObject->id ?>').slideUp(); } else { alert(data); } });">Approve</button>&nbsp;&nbsp;<a class="compare" rel="facebox" href="<?= "$baseurl/scripts/modqueue_compare.php?modimageid=$modQueueObject->id" ?>">Compare</a>&nbsp;&nbsp;<button type="button" class="deny" onclick="$('#deny-<?= $modQueueObject->id ?>').slideToggle();">Deny</button></p>
												<div id="deny-<?= $modQueueObject->id ?>" style="display: none;">
													<hr />
													<p style="font-weight: bold;">Reason for denial:</p>
													<select id="deny-select-<?= $modQueueObject->id ?>">
														<option>Submitted image is pixellated, of poor quality, or has artifacts.</option>
														<option>Submitted image is of smaller dimensions than the current.</option>
														<option>Submitted image does not possess a transparent background.</option>
														<option>Submitted image is the incorrect type/category.</option>
														<option>Submitted image is of an non-english language.</option>
														<option>Submitted image is for the wrong platform.</option>
														<option>Submitted image is for the wrong game.</option>
														<option>Submitted image is heavily stained.</option>
														<option>Submitted image is watermarked.</option>
														<option>Other (specify in comments).</option>
													</select>
													<p style="font-weight: bold;">Additional comments:</p>
													<textarea id="deny-additional-<?= $modQueueObject->id ?>" style="width: 100%; height: 100px;"></textarea>
													<p style="text-align: center;"><a class="deny" href="javascript:void();" onclick="var reason = $('#deny-select-<?= $modQueueObject->id ?>').val(); var additional = $('#deny-additional-<?= $modQueueObject->id ?>').val(); $.get('<?= $baseurl; ?>/scripts/modqueue_deny.php?modID=<?= $modQueueObject->id; ?>&denyreason=' + reason + '&denyadditional=' + additional, function(data){ if(data == 'Success') { $('#modItem-<?= $modQueueObject->id ?> img').css('display', 'none'); $('#modItem-<?= $modQueueObject->id ?>').slideUp(); } else { alert(data); } });">Confirm Denial</a>
													<!--<p style="text-align: center;"><a class="deny" href="javascript:void();" onclick="$.get('<?= $baseurl; ?>/scripts/modqueue_deny.php?modID=<?= $modQueueObject->id; ?>', function(data){ if(data == 'Success') { $('#modItem-<?= $modQueueObject->id ?> img').css('display', 'none'); $('#modItem-<?= $modQueueObject->id ?>').slideUp(); } else { alert(data); } });">Confirm Denial</a> -->
												</div></td>
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top;"><?= $modQueueObject->dateadded ?></td>
										</tr>
										<?php
												}
											}
											else
											{
										?>
											<tr>
												<td colspan="3" style="padding: 10px 10px; font-size: 18px; text-align: center;">This moderation queue is empty.</td>
											</tr>
										<?php
											}
										?>
									</tbody>
								</table>
						<?php
							break;

						case "reportedqueue":
						?>
								<style>
									button.approve {
										font-family: Arial, Helvetica, sans-serif;
										font-size: 12px;
										color: #ffffff;
										padding: 6px 12px;
										background: -moz-linear-gradient(
											top,
											#c8ffbf 0%,
											#72cc72 25%,
											#22a800);
										background: -webkit-gradient(
											linear, left top, left bottom,
											from(#c8ffbf),
											color-stop(0.25, #72cc72),
											to(#22a800));
										-moz-border-radius: 11px;
										-webkit-border-radius: 11px;
										border-radius: 11px;
										border: 2px solid #ffffff;
										-moz-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										-webkit-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(43,255,0,1);
										text-shadow:
											0px -1px 0px rgba(000,000,000,0.2),
											0px 1px 0px rgba(255,255,255,0.3);
										cursor: pointer;
									}

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

									.compare {
										font-family: Arial, Helvetica, sans-serif;
										font-size: 12px;
										text-decoration: none;
										color: #ffffff;
										padding: 6px 12px;
										background: -moz-linear-gradient(
											top,
											#bfcaff 0%,
											#82a1ff 25%,
											#4646fa);
										background: -webkit-gradient(
											linear, left top, left bottom,
											from(#bfcaff),
											color-stop(0.25, #82a1ff),
											to(#4646fa));
										-moz-border-radius: 11px;
										-webkit-border-radius: 11px;
										border-radius: 11px;
										border: 2px solid #ffffff;
										-moz-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										-webkit-box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										box-shadow:
											0px 3px 11px rgba(000,000,000,0.5),
											inset 0px 0px 8px rgba(0,13,255,1);
										text-shadow:
											0px -1px 0px rgba(000,000,000,0.2),
											0px 1px 0px rgba(255,255,255,0.3);
										cursor: pointer;
									}
								</style>

								<?php
									// Fetch number of reported images to be moderated

									//Front Boxart
									$reportedFrontQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'boxart' AND b.filename LIKE '%front%'");
									$reportedFrontQueueCount = mysql_num_rows($reportedFrontQueueResult);

									//Rear Boxart
									$reportedRearQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'boxart' AND b.filename LIKE '%back%'");
									$reportedRearQueueCount = mysql_num_rows($reportedRearQueueResult);

									//ClearLOGO
									$reportedClearlogoQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'clearlogo'");
									$reportedClearlogoQueueCount = mysql_num_rows($reportedClearlogoQueueResult);

									//Fanart
									$reportedFanartQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'fanart'");
									$reportedFanartQueueCount = mysql_num_rows($reportedFanartQueueResult);

									//Screenshot
									$reportedScreenshotQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'screenshot'");
									$reportedScreenshotQueueCount = mysql_num_rows($reportedScreenshotQueueResult);

									//Banner
									$reportedBannerQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND b.keytype = 'series'");
									$reportedBannerQueueCount = mysql_num_rows($reportedBannerQueueResult);

									//Games
									$reportedGameQueueResult = mysql_query("SELECT m.id FROM moderation_reported AS m, games AS g WHERE m.reportid = g.id AND m.reporttype = 'game'");
									$reportedGameQueueCount = mysql_num_rows($reportedGameQueueResult);
								?>

								<p>
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=frontboxart" style="color: orange; font-size: 16;">Front Boxart</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedFrontQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=rearboxart" style="color: orange; font-size: 16;">Rear Boxart</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedRearQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=clearlogo" style="color: orange; font-size: 16;">ClearLOGO</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedClearlogoQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=fanart" style="color: orange; font-size: 16;">Fanart</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedFanartQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=screenshot" style="color: orange; font-size: 16;">Screenshot</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedScreenshotQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=banner" style="color: orange; font-size: 16;">Banner</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedBannerQueueCount ?></span>
									&nbsp;&nbsp;|&nbsp;&nbsp;
									<a href="<?= $baseurl ?>/admincp/?cptab=reportedqueue&queuetype=game" style="color: orange; font-size: 16;">Game</a>
									<span style="margin-left: 4px; padding: 1px 6px; background-color: orange; color: #666666; border: 1px soid #FFFFFF; border-radius: 5px;"><?= $reportedGameQueueCount ?></span>
								</p>

								<?php
									switch ($queuetype)
									{
										case "frontboxart":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'boxart' AND b.filename LIKE '%front%' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Front Boxart Queue";
										break;

										case "rearboxart":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'boxart' AND b.filename LIKE '%back%' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Rear Boxart Queue";
										break;

										case "clearlogo":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'clearlogo' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported ClearLOGO Queue";
										break;

										case "fanart":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'fanart' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Fanart Queue";
										break;

										case "screenshot":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'screenshot' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Screenshot Queue";
										break;

										case "banner":
											$reportedResult = mysql_query("SELECT m.*, u.username, b.filename, b.resolution, g.id AS gameID, g.GameTitle, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p, banners AS b WHERE m.reportid = b.id AND m.reporttype = 'image' AND m.userID = u.id AND b.keyvalue = g.id AND g.Platform = p.id AND b.keytype = 'series' ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Banner Queue";
										break;

										case "game":
											$reportedResult = mysql_query("SELECT m.*, u.username, g.id AS gameID, g.GameTitle, g.Developer, g.Publisher, g.ReleaseDate, g.Overview, p.name AS PlatformName, p.id AS PlatformID FROM moderation_reported AS m, users AS u, games AS g, platforms AS p WHERE m.reportid = g.id AND m.reporttype = 'game' AND m.userID = u.id AND g.Platform = p.id ORDER BY m.dateadded") or die(mysql_error());
											$queueheader = "Reported Game Queue";
										break;
									}

									$reportedCount = mysql_num_rows($reportedResult);
								?>

								<table call-padding="0" cell-spacing="0" style="border: 2px solid #444; border-radius: 6px; background-color: #EEEEEE; color: #333333; border-collapse: separate; border-spacing: 2px; border-color: gray; width: 100%;">
									<thead style="text-align: left;">
										<tr>
											<th colspan="3" style="background: #F1F1F1; background-image: -webkit-linear-gradient(bottom,#C5C5C5,#F9F9F9); padding: 7px 7px 8px; font-size: 18px; text-align: center; border-bottom: 1px solid #444;"><?= $queueheader; ?></th>
										</tr>
										<tr>
											<?php if ($queuetype == "game"){?>
											<th class="modTableTitle">Game</th>
											<th class="modTableTitle">Report Info</th>
											<?php }else{?>
											<th class="modTableTitle">Art</th>
											<th class="modTableTitle">Art Info</th>
											<th class="modTableTitle">Report Info</th>
											<?php }?>
										</tr>
									</thead>
									<tfoot style="text-align: left;">
										<tr>
											<?php if ($queuetype == "game"){?>
											<th class="modTableTitle">Game</th>
											<th class="modTableTitle">Report Info</th>
											<?php }else{?>
											<th class="modTableTitle">Art</th>
											<th class="modTableTitle">Art Info</th>
											<th class="modTableTitle">Report Info</th>
											<?php }?>
										</tr>
									</tfoot>
									<tbody>
										<?php
											if ($reportedCount > 0)
											{
												while ($reportedObject = mysql_fetch_object($reportedResult))
												{
										?>
										<tr id="modItem-<?= $reportedObject->id ?>">
											<?php if ($queuetype != "game"){?>
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top; text-align: center;">
												<?php
													if(!file_exists("reportedqueue/_cache/$reportedObject->filename"))
													{
														WideImage::load("banners/$reportedObject->filename")->resize(200, 200)->saveToFile("reportedqueue/_cache/$reportedObject->filename");
													}
												?>
													<a href="<?= "$baseurl/banners/$reportedObject->filename" ?>" rel="facebox"><img src="<?= "$baseurl/reportedqueue/_cache/$reportedObject->filename" ?>" /></a>
											</td>
											<?php }?>
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top;"><span style="font-weight: bold;">Game:</span> <a href="<?= $baseurl . "/game/" . $reportedObject->gameID; ?>" style="color: darkorange;"><?= $reportedObject->GameTitle; ?></a><br />
												<span style="font-weight: bold;">Platform:</span> <a href="<?= $baseurl . "/platform/" . $reportedObject->PlatformID; ?>" style="color: darkorange;"><?= $reportedObject->PlatformName; ?></a><br />

												<?php if ($queuetype != "game"){?>
												<span style="font-weight: bold;">Filename:</span> <?= $reportedObject->filename; ?> <br />
												<span style="font-weight: bold;">Dimensions:</span> <? if (!empty($reportedObject->resolution)) { echo $reportedObject->resolution . "px"; } else{ echo "N/A"; } ?>
												<?php } else {?>
												<span style="font-weight: bold;">Developer:</span> <? if (!empty($reportedObject->Developer)) { echo $reportedObject->Developer; } else{ echo "N/A"; } ?><br/>
												<span style="font-weight: bold;">Publisher:</span> <? if (!empty($reportedObject->Publisher)) { echo $reportedObject->Publisher; } else{ echo "N/A"; } ?><br/>
												<span style="font-weight: bold;">Release Date:</span> <? if (!empty($reportedObject->ReleaseDate)) { echo $reportedObject->ReleaseDate; } else{ echo "N/A"; } ?><br/>
												<span style="font-weight: bold;">Overview:</span> <? if (!empty($reportedObject->Overview)) { echo $reportedObject->Overview; } else{ echo "N/A"; } ?>
												<?php }?>

												<p>&nbsp;</p>
												<p style="text-align: right;">
													<?php $type = "image";
													if ($queuetype == "game")
														$type = "game";
													?>
													<button type="button" class="approve" onclick="$.get('<?= $baseurl; ?>/scripts/reportqueue_keep.php?reportType=<?=$type;?>&reportedID=<?= $reportedObject->reportid; ?>&reportID=<?= $reportedObject->id; ?>', function(data){ if(data == 'Success') { $('#modItem-<?= $reportedObject->id ?> img').css('display', 'none'); $('#modItem-<?= $reportedObject->id ?>').slideUp(); } else { alert(data); } });">Keep</button>&nbsp;&nbsp;
													<button type="button" class="deny" onclick="$.get('<?= $baseurl; ?>/scripts/reportqueue_delete.php?reportType=<?=$type;?>&reportedID=<?= $reportedObject->reportid; ?>&reportID=<?= $reportedObject->id; ?>', function(data){ if(data == 'Success') { $('#modItem-<?= $reportedObject->id ?> img').css('display', 'none'); $('#modItem-<?= $reportedObject->id ?>').slideUp(); } else { alert(data); } });">Delete</button>
												</p>
												<div id="deny-<?= $reportedObject->id ?>" style="display: none;">
													<hr />
													<p style="font-weight: bold;">Reason for denial:</p>
													<select id="deny-select-<?= $reportedObject->id ?>">
														<option>Submitted image is pixellated, of poor quality, or has artifacts.</option>
														<option>Submitted image is of smaller dimensions than the current.</option>
														<option>Submitted image does not possess a transparent background.</option>
														<option>Submitted image is of an non-english language.</option>
														<option>Submitted image is for the wrong platform.</option>
														<option>Submitted image is for the wrong game.</option>
														<option>Submitted image is heavily stained.</option>
														<option>Submitted image contains offensive material such as gross violence or nudity.</option>
														<option>Submitted image is not related to the game.</option>
													</select>
													<p style="font-weight: bold;">Additional comments:</p>
													<textarea id="deny-additional-<?= $reportedObject->id ?>" style="width: 100%; height: 100px;"></textarea>
													<p style="text-align: center;"><a class="deny" href="javascript:void();" onclick="var reason = $('#deny-select-<?= $reportedObject->id ?>').val(); var additional = $('#deny-additional-<?= $reportedObject->id ?>').val(); $.get('<?= $baseurl; ?>/scripts/modqueue_deny.php?modID=<?= $reportedObject->id; ?>&denyreason=' + reason + '&denyadditional=' + additional, function(data){ if(data == 'Success') { $('#modItem-<?= $reportedObject->id ?> img').css('display', 'none'); $('#modItem-<?= $reportedObject->id ?>').slideUp(); } else { alert(data); } });">Confirm Denial</a>
													<!--<p style="text-align: center;"><a class="deny" href="javascript:void();" onclick="$.get('<?= $baseurl; ?>/scripts/modqueue_deny.php?modID=<?= $reportedObject->id; ?>', function(data){ if(data == 'Success') { $('#modItem-<?= $reportedObject->id ?> img').css('display', 'none'); $('#modItem-<?= $reportedObject->id ?>').slideUp(); } else { alert(data); } });">Confirm Denial</a> -->
												</div></td>
											<td style="padding: 10px 10px; border-bottom: 1px solid #444; vertical-align: top; width: 30%;">
												<span style="font-weight: bold;">Date Reported:</span><br /><?= $reportedObject->dateadded ?><br />
												<span style="font-weight: bold;">Reported By:</span><br /><a href="<?= $baseurl . "/artistbanners/?id=" . $reportedObject->userid; ?>" style="color: darkorange;"><?= $reportedObject->username; ?></a><br />
												<span style="font-weight: bold;">Reason For Report:</span><br /><?= $reportedObject->reason ?><br />
												<span style="font-weight: bold;">Additional:</span><br /><? if (!empty($reportedObject->additional)) { echo $reportedObject->additional; } else{ echo "N/A"; } ?>
											</td>
										</tr>
										<?php
												}
											}
											else
											{
										?>
											<tr>
												<td colspan="3" style="padding: 10px 10px; font-size: 18px; text-align: center;">This moderation queue is empty.</td>
											</tr>
										<?php
											}
										?>
									</tbody>
								</table>
						<?php
							break;

						case "addplatform":
							?>
								<h2>Add a Platform...</h2>

								<p>&nbsp;</p>

								<form method="post" action="<?= $baseurl; ?>/admincp/" enctype="multipart/form-data">
									<p style="text-align: center;"><img src="<?= $baseurl; ?>/images/common/consoles/png24/console_default.png" style="vertical-align: middle;" />&nbsp;To create a new platform, enter it's name below.</p>
									<p style="text-align: center; font-weight: bold;">Platform Name:&nbsp;<input type="text" name="PlatformTitle" size="60" /><br />
									<input type="hidden" name="function" value="Add Platform" />
									<input type="submit" name="submit" value="Add New Platform" style="padding: 6px;" /></p>
								</form>
							<?php
							break;

						case "pubdev":
							?>
								<h2>Manage Publishers and Developers</h2>

								<p>&nbsp;</p>

								<p style="text-align: center;"><a style="color: orange;" href="<?= $baseurl ?>/addpub/">Add new Publisher/Developer</a></p>

								<table align="center" border="1" cellspacing="0" cellpadding="7" bgcolor="#888888">
									<tr>
										<th style="background-color: #333; color: #FFF;">Keywords</th>
										<th style="background-color: #333; color: #FFF;">Logo</th>
										<th style="background-color: #333; color: #FFF;">Action</th>
									</tr>

								<?php
								$pubdevQuery = mysql_query(" SELECT * FROM pubdev ORDER BY keywords ASC");
								while($pubdevResult = mysql_fetch_object($pubdevQuery))
								{
								?>
									<tr>
										<td><?= $pubdevResult->keywords ?></td>
										<?php
											if(!file_exists("banners/_admincpcache/publisher-logos/$pubdevResult->logo"))
											{
												WideImage::load("banners/publisher-logos/$pubdevResult->logo")->resize(400, 60)->saveToFile("banners/_admincpcache/publisher-logos/$pubdevResult->logo");
											}
										?>
										<?php
											if($pubdevResult->logo)
											{
										?>
										<td><img src="<?= $baseurl ?>/banners/_admincpcache/publisher-logos/<?= $pubdevResult->logo ?>" style="vertical-align: middle;" /></td>
										<?php
											}
											else
											{
												echo "<td>Logo Missing</td>";
											}
										?>
										<td><a style="color: orange;" href="<?= $baseurl ?>/updatepub/?publisherid=<?= $pubdevResult->id ?>">Update Keywords &amp; Logo</a></td>
									</tr>
								<?php
								}
								?>
								</table>
							<?php
							break;


						case "sendpm":
						?>
							<h2>Send PM to User...</h2>

							<form action="<?= $baseurl; ?>/admincp/?cptab=sendpm" method="post">
								<span style="float: right;"><input type="submit" name="function" value="Send PM" /></span>
								<p><b>Send To:</b>&nbsp;<input type="text" name="pmto" id="pm-to" size="36" /> <a href="#pm-userlist" rel="facebox">User List</a></p>
								<p><b>Subject:</b>&nbsp;&nbsp;<input type="text" name="pmsubject" size="36" /></p>
								<p><b>Message:</b><br />
								<textarea name="pmmessage" style="width: 100%; height: 300px;"></textarea></p>
							</form>

							<!-- User List-->
							<div id="pm-userlist" style="display: none;">
								<div style="height: 400px; overflow: auto;">
									<p>
								<?php
									$userlistQuery = mysql_query("SELECT id, username FROM users ORDER BY username ASC");
									while($userlist = mysql_fetch_object($userlistQuery))
									{
										?>
											<a href="javascript: void();" onclick="$('#pm-to').val('<?= $userlist->username ?>'); jQuery(document).trigger('close.facebox');"><?= $userlist->username; ?></a><br />
										<?php
									}
								?>
									</p>
								</div>
							</div>
						<?php
							break;

						case "platformalias":
						?>
							<h2>Generate Platform Alias'...</h2>

							<form action="<?= $baseurl; ?>/admincp/?cptab=platformalias" method="post" style="text-align: center; padding: 16px; border: 1px solid #666; background-color: #333; color: #fff; margin: 16px;">
								<p style="font-size: 18px;">Press the button below to auto-generate alias's for platforms missing an alias.</p>
								<input type="submit" name="function" value="Generate Platform Alias's" style="padding: 16px;" />
							</form>

							<table align="center" border="1" cellspacing="0" cellpadding="7" bgcolor="#888888">
								<tr>
									<th style="background-color: #333; color: #FFF;" width="14%">ID</th>
									<th style="background-color: #333; color: #FFF;" width="43%">Name</th>
									<th style="background-color: #333; color: #FFF;" width="43%">Alias</th>
								</tr>

								<?php
									$platformsResult = mysql_query(" SELECT p.id, p.name, p.alias FROM platforms AS p ORDER BY p.id ");
									while($platforms = mysql_fetch_object($platformsResult))
									{
										if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
								?>
								<tr class="<?= $class; ?>">
									<td align="center"><?= $platforms->id; ?></td>
									<td><?= $platforms->name; ?></td>
									<td><?php if($platforms->alias == "") { echo "N/A"; } else { echo $platforms->alias; } ?></td>
								</tr>
								<?php
									}
								?>

							</table>

						<?php
							break;

						case "elasticsearchmanage":
						?>
							<h2>Manage ElasticSearch Index...</h2>

							<h1>Elasticsearch Management Area</h1>

							<p>&nbsp;</p>

							<p style="text-align: center;"><a class="darkorangeButton" href="<?php echo $baseurl; ?>/admincp/?cptab=elasticsearchmanage&command=createelasticsearchgamesindex">Create ElasticSearch 'TheGamesDB' Index</a></p>
							
							<br>
							<hr>
							<br>

							<p style="text-align: center;"><a class="darkorangeButton" href="<?php echo $baseurl; ?>/admincp/?cptab=elasticsearchmanage&command=populategames">Index/Re-Index Games Table into ElasticSearch</a></p>
							
							<br>
							<hr>
							<br>

							<form action="<?php echo $baseurl; ?>/admincp/" method="post" style="text-align: center;">
								<input type="text" name="searchterm" style="width: 80%; margin: auto; font-size: 18px; padding: 5px;" placeholder="Enter Game Title..." />
								<input type="hidden" name="cptab" value="elasticsearchmanage" />
								<input type="hidden" name="command" value="search" />
								<input class="darkorangeButton" type="submit" value="Search Elasticsearch Index for Game" />
							</form>

							<br>
							<hr>
							<br>

							<p style="text-align: center;"><a class="darkorangeButton" href="<?php echo $baseurl; ?>/adminarea/elastichq/" target="_blank">Launch ElasticHQ Management Portal</a></p>

							<br>
							<hr>
							<br>
					<?php
							// Main Switchboard
							switch ($command)
							{
								case ('createelasticsearchgamesindex'):
									echo '<h2>Create ElasticSearch TheGamesDB Index</h2>';

									try {	
										$indexParams['index']  = 'thegamesdb';

										// Example Index Mapping
										$myTypeMapping = array(
										    '_source' => array(
										        'enabled' => true
										    ),
										        'PlatformName' => array(
										            'type' => 'string',
										            'index' => 'not_analyzed'
										        )
										    );
										$indexParams['body']['mappings']['game'] = $myTypeMapping;

										// Create the index
										if ($elasticsearchClient->indices()->create($indexParams))
										{
											echo '<p style="text-align: center;">ElasticSearch Index \'thegamesdb\' Created Successfully</p>';
										}
									}
									catch (Exception $e)
									{
								?>
										<div style="text-align: center;">
											<p><strong>Whoops!</strong> Something went wrong while trying to create the index...</p>
											<p><em>Maybe the index 'thegamesdb' already exists, or ElasticSearch was unreachable?</em></p>
											<h3>Exception Dump</h3>
											<pre><?php echo $e->getMessage(); ?></pre>
										</div>
								<?php
									}

									break;

								case ('populategames'):
									echo '<h2>Populate Elasticsearch With Games Data</h2>';

									$dbGamesResult = mysql_query("SELECT `g`.*, `p`.`id` AS `PlatformId`, `p`.`name` AS `PlatformName`, `p`.`alias` AS `PlatformAlias`, `p`.`icon` AS `PlatformIcon` FROM `games` AS `g`, `platforms` AS `p` WHERE `g`.`Platform` = `p`.`id`");
									while ($dbGamesRow = mysql_fetch_assoc($dbGamesResult))
									{
										$searchParams = array();
										$searchParams['body']  = $dbGamesRow;
										$searchParams['index'] = 'thegamesdb';
										$searchParams['type']  = 'game';
										$searchParams['id']    = $dbGamesRow['id'];
										$ret = $elasticsearchClient->index($searchParams);
										var_dump($ret);
									}

									break;

								case ('search'):
									echo '<h2>Search Elasticsearch For ' . $searchterm . '</h2>';
										
										// Set initial Search Parameters
										$searchParams = array();
										$searchParams['index'] = 'thegamesdb';
										$searchParams['type']  = 'game';
										$searchParams['size']  = 100;

										// Check if $search term contains an integer
										if (strcspn($searchterm, '0123456789') != strlen($searchterm))
										{
											echo "<p>Search Term Contains a Number</p>";

											// Extract first number found in string
											preg_match('/\d+/', $searchterm, $numbermatch, PREG_OFFSET_CAPTURE);
											$numberAsNumber = $numbermatch[0][0];

											// Convert Number to Roman Numerals
											$numberAsRoman = romanNumerals($numberAsNumber);

											// Replace Number in string with RomanNumerals
											$searchtermRoman = str_replace($numberAsNumber, $numberAsRoman, $searchterm);
											
											echo "<pre>";
											echo "<p>" . $numbermatch[0][0] . " in Roman Numerals is " . $numberAsRoman . "</p>";
											echo "<p>Search Term:" . $searchterm . " in Roman Numerals is " . $searchtermRoman . "</p>";
											echo "</pre>";

											$json = '{
												      "query": {
												        "bool": {
												          "must": [
												            {
												              "match": {
												                "GameTitle": "' . $searchterm . '"
												              }
												            },
												            {
												              "match": {
												                "GameTitle": "' . $searchtermRoman . '"
												              }
												            }
												          ]
												        }
												      }
												    }';
											$searchParams['body'] = $json;
										}
										else
										{
											//$searchParams['body']['query']['match']['title'] = $searchterm;
											$json = '{
												      "query": {
											            "multi_match": {
											                "query": "' . $searchterm . '",
											                "fields": [ "GameTitle", "Alternates" ]
											              }
												        }
												      }
												    }';
											$searchParams['body'] = $json;
										}

										$elasticResults = $elasticsearchClient->search($searchParams);	


										echo "<h3>" . $elasticResults['hits']['total'] . " Games Found</h3>";

										echo "<hr />";


										foreach ($elasticResults['hits']['hits'] as $elasticGame)
										{
											//var_dump($elasticGame);
											?>

												<div>
													<h4>Title: <?php echo $elasticGame['_source']['GameTitle']; ?></h4>
													<p>Search Score: <?php echo $elasticGame['_score']; ?></p>
													<p>ID: <?php echo $elasticGame['_source']['id']; ?></p>
													<p>Released: <?php echo $elasticGame['_source']['ReleaseDate']; ?></p>
													<p>Platform: <?php echo $elasticGame['_source']['Platform'] . " | " . $elasticGame['_source']['PlatformName'] . " | " . $elasticGame['_source']['PlatformAlias']; ?></p>
													<hr>
												</div>

											<?php
										}

									break;
							}

							break;

						default:
							?>
							<p>&nbsp;</p>
							<h2>Please select a section to administrate...</h2>
							<p>&nbsp;</p>
							<?php
							break;
					}
				?>
			</div>
			<div style="clear: both;"></div>
		</div>
		<?php
		}
		else {
		?>
			<div style="text-align: center;">
				<h2>Sorry...</h2>
				<h2>Only administrators are permitted access to this section.</h2>
			</div>
		<?php
		}
		?>

	</div>