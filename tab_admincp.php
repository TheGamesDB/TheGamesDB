<?php
if (isset($function) && $function == "Edit Publisher Keywords")
{
	?>
	<h2>Edit Publisher/Developer Keywords</h2>
	<form action="<?= $baseurl; ?>/admincp/?cptab=publishers" method="post">
	Keywords:&nbsp;<input type="text" name="keyword" value="<?= $keyword; ?>"/>
	<input type="hidden" name="publisherid" value="<?= $id; ?>" />
	<input type="submit" name="function" value="Save Keywords" />
	</form>
	<hr />
	<form action="<?= $baseurl; ?>/admincp/?cptab=publishers" method="post" enctype="multipart/form-data">
	<p><input type="file" name="logoimage" />
	<input type="hidden" name="publisherid" value="<?= $id; ?>" /></p>
	<p style="text-align: right;"><input style="margin-left: auto;" type="submit" name="function" value="Upload New Logo" /></p>
	</form>
	<?php
	die();
}
?>
<?php if ($adminuserlevel == 'ADMINISTRATOR') { ?>
<?php if (!isset($cptab)) { $cptab = "userinfo"; } ?>
<div style="text-align: center;">
	<h1 class="arcade">Admin Control Panel:</h1>
</div>

<div id="controlPanelWrapper">
	<div id="controlPanelNav">
		<ul>
			<li<?php if($cptab == "userinfo"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=userinfo">My User Info</a></li>
			<li<?php if($cptab == "addplatform"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=addplatform">Add New Platform</a></li>
			<li<?php if($cptab == "publishers"){ ?> class="active" <?php } ?>><a href="<?= $baseurl ?>/admincp/?cptab=publishers">Manage Publishers &amp; Developers</a></li>
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
					
					<div style="float:left;">
						<form style="padding: 14px; border: 1px solid #999; background-color: #E6E6E6;" method="post" action="<?= $baseurl; ?>/admincp/" enctype="multipart/form-data">
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
						<form style="padding: 14px; border: 1px solid #999; background-color: #E6E6E6;" method="post" action="<?= $baseurl; ?>/admincp/" enctype="multipart/form-data">
						<h2>Add A Platform...</h2>
							<p style="text-align: center;"><img src="<?= $baseurl; ?>/images/common/consoles/png24/console_atari.png" style="vertical-align: middle;" />&nbsp;To create a new platform, enter it's name below.</p>
							<p style="text-align: center; font-weight: bold;">Platform Name:&nbsp;<input type="text" name="PlatformTitle" size="60" /><br />
							<input type="hidden" name="function" value="Add Platform" />
							<input type="submit" name="submit" value="Add New Platform" /></p>
						</form>
					<?php
					break;
				
				case "publishers":
				?>
					<h2>Manage Platforms &amp; Developers...</h2>
					
					<table id="listtable" style="margin: auto;">
						<tr class="head">
							<th>Keyword</th>
							<th>Logo</th>
							<th>&nbsp;</th>
						</tr>
						<?php
							$pubQuery = mysql_query(" SELECT * FROM publishers ");
							while($pubResult = mysql_fetch_object($pubQuery))
							{
								if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
						?>
						<tr class="<?= $class; ?>">
							<td valign="middle" style="text-align: center; font-size: 12px; font-weight: bold;"><?= $pubResult->keyword; ?></td>
							<?php
								if(!file_exists("banners/_admincpcache/publishers/$pubResult->logo"))
								{
									WideImage::load("banners/publishers/$pubResult->logo")->resize(60, 60, 'outside')->saveToFile("banners/_admincpcache/publishers/$pubResult->logo");
								}
							?>
							<td style="text-align: center;"><img src="<?= $baseurl; ?>/banners/_admincpcache/publishers/<?= $pubResult->logo; ?>" title="<?= $pubResult->keyword; ?>" alt="<?= $pubResult->keyword; ?>" /></td>
							<td><a href="<?= $baseurl; ?>/tab_admincp.php?function=Edit+Publisher+Keywords&id=<?= $pubResult->id; ?>&keyword=<?= $pubResult->keyword; ?>" rel="facebox">Edit Keywords &amp; Logo</a></td>
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
					<h2>Generate Platform Alias's</h2>
					
					<form action="<?= $baseurl; ?>/admincp/?cptab=platformalias" method="post" style="text-align: center; padding: 16px; border: 1px solid #666; background-color: #333; color: #fff; margin: 16px;">
						<p style="font-size: 18px;">Press the button below to auto-generate alias's for platforms missing an alias.</p>
						<input type="submit" name="function" value="Generate Platform Alias's" style="padding: 16px;" />
					</form>
					
					<table id="listtable" style="margin: auto;" cellpadding="6">
						<tr class="head">
							<th width="14%">ID</th>
							<th width="43%">Name</th>
							<th width="43%">Alias</th>
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