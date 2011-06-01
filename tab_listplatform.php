<?php	## Handle searches differently
	if ($_SESSION['userid'] && !$alllang){
		$languagelimit = "AND languageid = (SELECT languageid FROM users WHERE id = ".$_SESSION['userid'].")";
		$query = "SELECT languages.name FROM users INNER JOIN languages ON users.languageid = languages.id WHERE users.id=".$_SESSION['userid'];
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
?>
	
	<!-- Start Browse By Platform -->
	<div style="width: 550px; margin: auto; margin-bottom: 12px;">
		<div id="platformsPanel" style="border: 1px solid #000; background-color: #555555; padding: 15px; color: #FFFFFF;">
			<div style="width:450px; margin: auto;">
			<h1 class="arcade" style="text-align: center;"><span style="color: #000;">Browse</span> <span style="color: #EF5F00;">Platforms</span></h1>
				<form id="platformBrowseForm" action="<?= $baseurl ?>/index.php" onsubmit="if($('#platformMenu').val() != 'select') { return true; } else { alert('Please Select a Platform...'); return false; }">
					<select name="stringPlatform" id="platformMenu" onchange="showValue(this.value); alert(this.value); if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); }" style="color: #333;">
						<option value="select" title="images/common/icons/question-block_48.png">Please Select Platform...</option>
						<?php
									$platformQuery = mysql_query(" SELECT * FROM platforms ORDER BY name ASC");
									while($platformResult = mysql_fetch_assoc($platformQuery))
									{
										?>
											<option value="<?php echo $platformResult['id']; ?>" title="images/common/consoles/png48/<?php echo $platformResult['icon'];?>"<?php if($stringPlatform == $platformResult['id']) {echo " selected";}?>><?php echo $platformResult['name']; ?></option>
										<?php
									}
								?>
					</select>
					<input type="hidden" name="tab" value="listplatform" />
					<input type="hidden" name="function" value="Browse By Platform" />
					<!-- <a class="arcade" href="javascript: void();" onclick="if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); } else { alert('Please select a console...'); }" style=" font-size: 38px; color: #00FF00; float: left; padding-left: 17px; padding-top: 10px;">Go!</a> -->
					<button type="submit" style="cursor: pointer; height: 46px; padding: 5px; margin-left: 10px; margin-top: 5px; float: left; background: url(images/common/bg_button-black.png) center center repeat-x; border-radius: 10px;"><span class="arcade" style=" font-size: 30px; color: #00FF00;">Go</span></button>
				</form>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<!-- End Browse By Platform -->
	
	<table width="100%" border="0" cellspacing="1" cellpadding="7" id="listtable">
		<tr>
			<td class="head arcade" align="center">ID</td>
			<td class="head arcade">Game Title</td>
			<td class="head arcade">Platform</td>
			<td class="head arcade">Genre</td>
			<td class="head arcade">ESRB</td>
			<td class="head arcade">Boxart</td>
			<td class="head arcade">Fanart</td>
		</tr>

		<?php	## Run the games query
			$gamecount = 0;
			$string = mysql_real_escape_string($string);
			$letter = mysql_real_escape_string($letter);			


			
			if ($function == 'Browse By Platform')  {
				$query = "SELECT * FROM games WHERE Platform = '$stringPlatform' ORDER BY GameTitle";
			
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());

				## Display each game
				while ($game = mysql_fetch_object($result)) {
					$platformIdQuery = mysql_query("SELECT * FROM platforms WHERE id = '$game->Platform' LIMIT 1");
					$platformIdResult = mysql_fetch_object($platformIdQuery);
				
					$boxartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND banners.filename LIKE '%front%' LIMIT 1");
					$boxartResult = mysql_num_rows($boxartQuery);
					
					$fanartQuery = mysql_query("SELECT keyvalue FROM banners WHERE banners.keyvalue = '$game->id' AND keytype = 'fanart' LIMIT 1");
					$fanartResult = mysql_num_rows($fanartQuery);
					
					if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
					?>
					<tr>
						<td align="center" class="<?php echo $class; ?>"><?php echo $game->id; ?></td>
						<td class="<?php echo $class; ?>"><a href="<?php echo $baseurl; ?>/?tab=game&id=<?php echo $game->id; ?>&lid=1"><?php echo $game->GameTitle; ?></a></td>
						<td class="<?php echo $class; ?>"><img src="images/common/consoles/png16/<?php echo $platformIdResult->icon; ?>" alt="<?php echo $platformIdResult->name; ?>" style="vertical-align: middle;" /> <?php echo $platformIdResult->name; ?></td>
						<td class="<?php echo $class; ?>"><?php if(!empty($game->Genre)) { $mainGenre = explode("|", $game->Genre); if(strlen($mainGenre[1]) > 15) { $mainGenre[1] = substr($mainGenre[1], 0, 15) . "..."; }echo $mainGenre[1]; } ?></td>
						<td class="<?php echo $class; ?>"><?php echo $game->Rating; ?></td>
						<td align="center" class="<?php echo $class; ?>"><?php if($boxartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
						<td align="center" class="<?php echo $class; ?>"><?php if($fanartResult != 0){ ?><img src="images/common/icons/tick_16.png" alt="Yes" /><?php } else{ ?><img src="images/common/icons/cross_16.png" alt="Yes" /><?php }?></td>
					</tr>
					<?php
					$gamecount++;
				}
			}

			## No matches found?
			if ($gamecount == 0)  {
				print "<tr><td class=\"odd\" colspan=\"7\" align=\"center\" style=\"font-weight: bold;\">This platform does not have any games yet... Why not <a href=\"?tab=addgame&passPlatform=$stringPlatform\">add one</a>?";
				//if (!$alllang){print "Retry <a href=\"$baseurl/index.php?".$_SERVER["QUERY_STRING"]."&alllang=1\">search</a> in all languages?";}
				print "</td></tr>\n";
				
			}
			else
			{
				?>
					<tr>
						<td class="total" colspan="7">Platform Total: <?=$gamecount?> Games</td>
					</tr>
				<?php
			}
		?>
		</table>


		<script language="javascript">
			$(document).ready(function(e) {
				try {
					$("#platformMenu").msDropDown();
				} 
				catch(e) {
					alert(e.message);
				}
			});
		</script>