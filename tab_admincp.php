<?php if ($adminuserlevel == 'ADMINISTRATOR') { ?>

<?php
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
?>

<?php if (!isset($cptab)) { $cptab = "userinfo"; } ?>
		
<div id="gameWrapper">
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

		<div id="controlPanelWrapper">
			<div id="controlPanelNav">
				<ul>
					<li<?php if($cptab == "userinfo"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=userinfo">My User Info</a></li>
					<li<?php if($cptab == "addplatform"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=addplatform">Add New Platform</a></li>
					<li<?php if($cptab == "publishers"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=pubdev">Manage Publishers &amp; Developers</a></li>
					<li<?php if($cptab == "sendpm"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=sendpm">Send PM</a></li>
					<li<?php if($cptab == "platformalias"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=platformalias">Generate Platform Alias's</a></li>
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
						
						case "addplatform":
							?>					
								<h2>Add a Platform...</h2>
								
								<p>&nbsp;</p>
								
								<form method="post" action="<?= $baseurl; ?>/admincp/" enctype="multipart/form-data">
									<p style="text-align: center;"><img src="<?= $baseurl; ?>/images/common/consoles/png24/console_atari.png" style="vertical-align: middle;" />&nbsp;To create a new platform, enter it's name below.</p>
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
										<td><img src="<?= $baseurl ?>/banners/_admincpcache/publisher-logos/<?= $pubdevResult->logo ?>" style="vertical-align: middle;" /></td>
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
						
						default:
							?>
							<p>&nbsp;</p>
							<h2 class="arcade">Please select a section to administrate...</h2>
							<p>&nbsp;</p>
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
						
						default:
							?>
							<p>&nbsp;</p>
							<h2 class="arcade">Please select a section to administrate...</h2>
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
				<h2 class="arcade">Sorry...</h2>
				<h2>Only administrators are allowed access to this section.</h2>
			</div>
		<?php
		}
		?>
		
	</div>
</div>