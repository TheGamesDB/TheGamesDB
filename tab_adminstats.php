<!-- Start Admin Only Stats & Reports -->
<?php

if ($adminuserlevel == 'ADMINISTRATOR') {
	if(isset($_GET['statstype'])) {
	$StatsType=$_GET['statstype'];
	
?>

	<div style="text-align: center;">
		<h1 class="arcade">Admin Reports &amp; Statistics:</h1>
		
		<?php
			switch ($StatsType)
			{
			case "locked":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Locked Games</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
						<th style="background-color: #333; color: #FFF;">Locked By</th>
					</tr>
				<?php
				$lockedcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle, games.lockedby, users.username FROM games, users WHERE locked = 'yes' AND games.lockedby = users.id ORDER BY games.GameTitle ASC");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
						<td><?php echo $row[username]; ?></td>
					</tr>
					<?php
					$lockedcount++;
				}
				?>
					<tr>
						<td colspan="3" style="background-color: #EEE; font-weight: bold;">Total Locked Games: <?php echo $lockedcount; ?></td>
					</tr>
				</table>
				<?php
				break;
				
			case "missingoverview":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Overview</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE games.Overview IS NULL ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Overview: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "missingplatform":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Platform Data</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE games.Platform IS NULL ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Platform Data: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "missinggenre":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Genre Data</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE games.Genre IS NULL ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Genre Data: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "multipleplatform":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games With Multiple Platforms</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
						<th style="background-color: #333; color: #FFF;">Platforms</th>
					</tr>
				<?php
				$multiplecount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle, games.Platform FROM games WHERE games.Platform LIKE '|%|%|' ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
						<td><?php echo $row[Platform]; ?></td>
					</tr>
					<?php
					$multiplecount++;
				}
				?>
					<tr>
						<td colspan="3" style="background-color: #EEE; font-weight: bold;" >Total Games With Multiple Platforms: <?php echo $multiplecount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "missingfront":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Front Boxart</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Front Boxart: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "morefront":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games With 2 or More Front Boxart</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$morecount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE (SELECT COUNT(filename) FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%front%') > 1 ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$morecount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games With 2 or More Front Boxart: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
			
			case "missingback":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Back Boxart</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.filename LIKE '%back%') ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Back Boxart: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
				
			case "missingfanart":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Fanart</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'fanart') ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Fanart: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
				
			case "missingbanner":
				?>
				<h2 class="arcade" style="color: #FF4F00;">Games Missing Banners</h2>
				<table align="center" border="1" cellspacing="0" cellpadding="7">
					<tr>
						<th style="background-color: #333; color: #FFF;">Game ID</th>
						<th style="background-color: #333; color: #FFF;">Game Title</th>
					</tr>
				<?php
				$missingcount = 0;
				$result = mysql_query(" SELECT games.id, games.GameTitle FROM games WHERE NOT EXISTS (SELECT keyvalue FROM banners WHERE banners.keyvalue = games.id AND banners.keytype = 'series') ORDER BY games.GameTitle ASC ");
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $row[id]; ?></td>
						<td align="left"><a href="?tab=game&id=<?php echo $row[id]; ?>&lid=1"><?php echo $row[GameTitle]; ?></a></td>
					</tr>
					<?php
					$missingcount++;
				}
				?>
					<tr>
						<td colspan="2" style="background-color: #EEE; font-weight: bold;" >Total Games Missing Banners: <?php echo $missingcount; ?></td>
					</tr>
				</table>
				<?php
				break;
				
			default:
			  ?>
			  <p><span style="color: #FF0000; font-weight: bold;">Error:</span> A report or statistic has not been selected.<br />Please return to the previous page and try again.</p>
			  <?php
			} 
		?>
		
	</div>
	
<?php

		}
	}
	else {
		?>
		<p style="text-align: center;"><span style="color: #FF0000; font-weight: bold;">Error:</span> You must be logged in to view this page.</p>
		<?php
	}
	
?>
<!-- End Admin Only Stats & Reports -->