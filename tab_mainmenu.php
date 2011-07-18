<div class="right" style="margin-left: 15px; padding-left: 15px; border-left: 2px dotted #333;">

<!-- Start Total Game Count -->
<?php
	$countResult = mysql_query(" SELECT id FROM games ");
	if($countRows = mysql_num_rows($countResult))
	{
?>
	<div style="text-align: center;">
		<h3 class="arcade">Total <span style="color: #00CC3F;">GameCount</span></h3>
		<span style=" border: 2px solid #666; color: #FFF; background-color: #000; background: url(images/common/bg_button-black.png) center center repeat-x; height: 30px; border-radius: 6px; padding: 5px 15px; font-size: 20px;">
			<span><?php echo number_format($countRows); ?></span> <span style="color: #CFCFCF;">Games</span>
		</span>
	</div>
<?php
	}
?>
<!-- End Total Game Count -->

<hr style="margin: 20px 0px 0px 0px; height: 1px; background-color: #111;" />

<!-- Start Browse By Platform -->
	<div style="width: 320px; margin: auto;">
		<h3 class="arcade" style="text-align: center;"><span style="color: #000;">Browse</span> <span style="color: #EF5F00;">Platforms</span></h3>
		<form id="platformBrowseForm" action="<?= $baseurl ?>/index.php" onsubmit="if($('#platformMenu').val() != 'select') { return true; } else { alert('Please Select a Platform...'); return false; }">
			<select name="stringPlatform" id="platformMenu" onchange="showValue(this.value); alert(this.value); if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); }" style="color: #333;">
				<option value="select" title="images/common/icons/question-block_24.png">Please Select Platform...</option>
				<?php
							$platformQuery = mysql_query(" SELECT * FROM platforms ORDER BY name ASC");
							while($platformResult = mysql_fetch_assoc($platformQuery))
							{
								?>
									<option value="<?php echo $platformResult['id']; ?>" title="images/common/consoles/png24/<?php echo $platformResult['icon'];?>"<?php if($stringPlatform == $platformResult['name']) {echo " selected";}?>><?php echo $platformResult['name']; ?></option>
								<?php
							}
						?>
			</select>
			<input type="hidden" name="tab" value="listplatform" />
			<input type="hidden" name="function" value="Browse By Platform" />
			<!-- <a class="arcade" href="javascript: void();" onclick="if($('#platformMenu').val() != 'select') { document.forms['platformBrowseForm'].submit(); } else { alert('Please select a console...'); }" style=" font-size: 38px; color: #00FF00; float: left; padding-left: 17px; padding-top: 10px;">Go!</a> -->
			<button type="submit" style="cursor: pointer; height: 31px; padding: 5px; margin-left: 10px; margin-top: 4px; float: left; background: url(images/common/bg_button-black.png) center center repeat-x; border-radius: 10px;"><span class="arcade" style=" font-size: 14px; color: #00FF00;">Go</span></button>
		</form>
	</div>
	<div style="clear: both;"></div>
			
	<script language="javascript">
		$(document).ready(function(e) {
			try {
				$("#platformMenu").msDropDown({mainCSS:'dd2'});
			} 
			catch(e) {
				alert(e.message);
			}
		});
	</script>
<!-- End Browse By Platform -->

<hr style="margin: 20px 0px 0px 0px; height: 1px; background-color: #111;" />

<!-- Start Most Recent Rotator -->
<?php
$sql = "SELECT DISTINCT g.GameTitle, b.filename, g.id, b.filename FROM games as g, banners as b WHERE g.id = b.keyvalue AND b.filename LIKE '%front%' ORDER BY lastupdated desc LIMIT 6";
$result = mysql_query($sql);
if ($result !== FALSE): ?>
    <div id="recent">
        <h3 class="arcade">Recently <span style="color: #FF006F;">Updated</span></h3>
        <ul>
			<?php while ($row = mysql_fetch_array($result)): ?>
				<li>
					<a href="<?= $baseurl ?>/?tab=game&id=<?= $row[2] ?>&lid=1" >
					<?php if (isset($row[1])): ?>
						<img src="<?= $baseurl ?>/banners/_cache/<?= $row[1] ?>" style="border: 2px solid #CACACA; outline: 1px solid #000;" />
					<?php endif ?>
						<span class="arcade"><br /><?= $row[0] ?></span>
					</a>
				</li>
			<?php endwhile ?>
        </ul>
    </div>
<?php endif ?>
<!-- End Most Recent Rotator -->

</div>

<!-- Start Site News -->
<div id="news" style="text-align: justify;">
	<?php include("sitenews.php"); ?>
</div>
<!-- End Site News -->

<div style="clear:both;"></div>

