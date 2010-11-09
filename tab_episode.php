<?php
	## Get this series' information
	$seriesid = mysql_real_escape_string($seriesid);
	$query	= "SELECT * FROM games WHERE id=$seriesid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$series = mysql_fetch_object($result);

	## Get this seasons' information
	$seasonid = mysql_real_escape_string($seasonid);
	$query	= "SELECT * FROM tvseasons WHERE id=$seasonid";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$season = mysql_fetch_object($result);

	## Get this episode's information
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM tvepisodes WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$episode = mysql_fetch_object($result);

	##Get Name of Admin if locked
	if ($series->lockedby ) {
		$query3	= "SELECT * FROM users WHERE id=$series->lockedby limit 1";
		$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
		$lockadmin	= mysql_fetch_object($result3);
	}
	if ($season->lockedby ) {
		$query3	= "SELECT * FROM users WHERE id=$season->lockedby limit 1";
		$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
		$lockadmin	= mysql_fetch_object($result3);
	}
	if ($episode->lockedby ) {
		$query3	= "SELECT * FROM users WHERE id=$episode->lockedby limit 1";
		$result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
		$lockadmin	= mysql_fetch_object($result3);
	}

	## Get the number of episodes
	$query	= "SELECT MIN(EpisodeNumber) AS minimum, MAX(EpisodeNumber) AS maximum FROM tvepisodes WHERE seasonid=$seasonid";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$episodes= mysql_fetch_object($result);

	## Get the id of the previous episode
	$query	= "SELECT id FROM tvepisodes WHERE seasonid=$seasonid AND EpisodeNumber<$episode->EpisodeNumber ORDER BY EpisodeNumber DESC LIMIT 1";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$minepisode= mysql_fetch_object($result);

	## Get the id of the next episode
	$query	= "SELECT id FROM tvepisodes WHERE seasonid=$seasonid AND EpisodeNumber>$episode->EpisodeNumber ORDER BY EpisodeNumber LIMIT 1";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$maxepisode= mysql_fetch_object($result);
if ($lid) {
   $urllang = "&amp;lid=$lid";	
}

	if ($user->lastupdatedby_admin)  { 
		$query	= "SELECT * FROM users WHERE id=$user->lastupdatedby_admin";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$adminuser	= mysql_fetch_object($result);
	}
?>

<div id="bannerrotator">
<?=bannerdisplay($seriesid)?>
</div>


<table cellspacing="5" cellpadding="0" border="0" width="100%">
<tr>
	<td valign="top" width="100%">

<div class="titlesection">
	<h1><a href="/?tab=series&id=<?=$series->id?><?=$urllang?>"><?=stripslashes($series->SeriesName);?></a></h1>
<?php
	if ($season->season == 0) {
		echo "<h2><a href=\"/?tab=season&seriesid=$series->id&seasonid=$season->id$urllang\">Specials</a></h2>";
	}
	else {
		echo "<h2><a href=\"/?tab=season&seriesid=$series->id&seasonid=$season->id$urllang\">Season $season->season</a></h2>";
	}
?>

	<h3><?=$episode->EpisodeNumber?> : <?=stripslashes($episode->EpisodeName);?></h3>
</div>


<div class="section">
<form action="<?=$fullurl?>" method="POST" name="episodeform">
	<div id="red"><?=$errormessage?></div>
	<?php if ($series->locked != 'yes' AND $season->locked != 'yes' AND $episode->locked != 'yes' OR $lockadmin->userlevel == 'SUPERADMIN') {
	if ($loggedin == 1 && $series->SeriesID && $episode->EpisodeNumber && $season->season )  {  ?>
		<a href="#" onClick="openChild('/scrape_tvcom.php?seriesname=<?=urlencode($series->SeriesName)?>&tvcomid=<?=$series->SeriesID?>&season=<?=$season->season?>&episode=<?=$episode->EpisodeNumber?>', 'EpisodeEdit<?=$episode->id?>', 500, 600); return false">Import From TV.com</a>
	<?  } } ?>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="datatable">
	<tr>
		<td>Episode Number:</td>
		<td><input type="text" name="EpisodeNumber" value="<?=$episode->EpisodeNumber?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>Episode Name: </td>
		<td>
			<?php
				## Display EpisodeName translations
				$query	= "SELECT l.*, t.translation FROM languages AS l LEFT OUTER JOIN translation_episodename AS t ON t.episodeid=$episode->id AND l.id=t.languageid ORDER BY l.name";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while($lang = mysql_fetch_object($result))  {

					## If we have the currently selected language
					if ($lang->id == $lid)  {
						$display = 'inline';
					}
					## Otherwise
					else  {
						$display = 'none';
					}

					## If a translation is found
					if ($lang->translation)  {
						$episodename_translation[$lang->id] = 1;
					}
					## Otherwise
					else  {
						$episodename_translation[$lang->id] = 0;
					}
				?>
				<input type="text" name="EpisodeName_<?=$lang->id?>" value="<?=stripslashes($lang->translation)?>" style="display: <?=$display?>" >
			<?php	}  ?>

			<br>

			<select name="EpisodeName_LangSelect" size="1" onChange="ShowEpisodeName(this.options[this.selectedIndex].value)">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($lid == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}

					## If a translation is found
					if ($episodename_translation[$langid] == 1)  {
						$class = 'languagesel_off';
					}
					## Otherwise
					else  {
						$class = 'languagesel_on';
					}
					print "<option value=\"$langid\" class=\"$class\" $selected>$langname</option>\n";
				}
			?>
			</select>
		</td>
	</tr>


	<tr>
		<td>First Aired:</td>
		<td><input type="text" name="FirstAired" value="<?=$episode->FirstAired?>" maxlength="255"></td>
	</tr>
	<tr>
		<td>Guest Stars:</td>
		<td><input type="text" name="GuestStars" value="<?=$episode->GuestStars?>" maxlength="255"></td>
	</tr>
	<tr>
		<td>Director:</td>
		<td><input type="text" name="Director" value="<?=$episode->Director?>" maxlength="255"></td>
	</tr>
	<tr>
		<td>Writer:</td>
		<td><input type="text" name="Writer" value="<?=$episode->Writer?>" maxlength="255"></td>
	</tr>
	<tr>
		<td>Production Code:</td>
		<td><input type="text" name="ProductionCode" value="<?=$episode->ProductionCode?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>Overview: </td>
		<td>
			<?php
				## Display OVerview translations
				$query	= "SELECT l.*, t.translation FROM languages AS l LEFT OUTER JOIN translation_episodeoverview AS t ON l.id=t.languageid AND t.episodeid=$episode->id ORDER BY l.name";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while($lang = mysql_fetch_object($result))  {

					## If we have the currently selected language
					if ($lang->id == $lid)  {
						$display = 'inline';
					}
					## Otherwise
					else  {
						$display = 'none';
					}

					## If a translation is found
					if ($lang->translation)  {
						$episodeoverview_translation[$lang->id] = 1;
					}
					## Otherwise
					else  {
						$episodeoverview_translation[$lang->id] = 0;
					}
				?>
				<textarea rows="10" cols="45" name="Overview_<?=$lang->id?>" style="display: <?=$display?>"><?=stripslashes($lang->translation);?></textarea>
			<?php	}  ?>

			<br>

			<select name="Overview_LangSelect" size="1" onChange="ShowEpisodeOverview(this.options[this.selectedIndex].value)">
			<?php
				## Display language selector
				foreach ($languages AS $langid => $langname)  {
					## If we have the currently selected language
					if ($lid == $langid)  {
						$selected = 'selected';
					}
					## Otherwise
					else  {
						$selected = '';
					}

					## If a translation is found
					if ($episodeoverview_translation[$langid] == 1)  {
						$class = 'languagesel_off';
					}
					## Otherwise
					else  {
						$class = 'languagesel_on';
					}
					print "<option value=\"$langid\" class=\"$class\" $selected>$langname</option>\n";
				}
			?>
			</select>
		</td>
	</tr>

	<?php if ($season->season == 0) { ?>
	<tr>
		<td>Airs After Season:</td>
		<td>
		  <select name="airsafter_season">
		  <option value=""> </option>
			<?php	## Display the episodes
				$query	= "SELECT * FROM tvseasons WHERE seriesid=$seriesid";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($db = mysql_fetch_object($result))  {
				  $selected = '';
				  if ($db->season !=0) {
				  	if ($db->season == $episode->airsafter_season) {$selected = 'selected';}
					echo "<option value='$db->season' $selected>$db->season</option>";
				  }
				}
			?>
		  </select>
		</td>
	</tr>
	<tr>
		<td>Airs Before:</td>
		<td>
		  <select name="airsbefore">
		  <option value=" ">&nbsp;</option>
			<?php	## Display the episodes
				$query	= "SELECT (SELECT season FROM tvseasons WHERE id=tvepisodes.seasonid LIMIT 1) AS season, EpisodeNumber, EpisodeName, FirstAired FROM tvepisodes WHERE seriesid=$seriesid ORDER BY season, episodenumber";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($db = mysql_fetch_object($result))  {
					if ($db->season == '0')  {  continue;  }
					$selected = '';
					if ($db->season == $episode->airsbefore_season && $db->EpisodeNumber == $episode->airsbefore_episode) {
						$selected = 'selected';
					}
					
					if (strlen($db->EpisodeName) > 15)  {
						$db->EpisodeName = substr($db->EpisodeName, 0, 15) . '...';
					}
					echo "<option value='$db->season|$db->EpisodeNumber' $selected>" . str_pad($db->season, 2, "0", STR_PAD_LEFT) . "x" . str_pad($db->EpisodeNumber, 2, "0", STR_PAD_LEFT) . " - $db->EpisodeName ($db->FirstAired)</option>";
				}
			?>
		  </select>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td>DVD Disc ID:</td>
		<td><input type="text" name="DVD_discid" value="<?=$episode->DVD_discid?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>DVD Season:</td>
		<td><input type="text" name="DVD_season" value="<?=$episode->DVD_season?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>DVD Episode Number:</td>
		<td><input type="text" name="DVD_episodenumber" value="<?=$episode->DVD_episodenumber?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>DVD Chapter:</td>
		<td><input type="text" name="DVD_chapter" value="<?=$episode->DVD_chapter?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>Absolute Number:</td>
		<td><input type="text" name="absolute_number" value="<?=$episode->absolute_number?>" maxlength="45"></td>
	</tr>
	<tr>
		<td>IMDB.com ID:</td>
		<td><input type="text" name="IMDB_ID" value="<?=$episode->IMDB_ID?>" maxlength="25"></td>
	</tr>

	<?php	
			if ($_SESSION['userlevel'] == 'SUPERADMIN' && $episode->filename){
				echo '<tr>';
				echo '	<td>Image Status: </td>';
				echo '  <td>';
				echo '    <select name="EpImgFlag" size="1">';
				echo '      <option value="0" '.($episode->EpImgFlag==0?"Selected":"").'></option>';
				echo '      <option value="1" '.($episode->EpImgFlag==1?"Selected":"").'>4:3</option>';
				echo '      <option value="2" '.($episode->EpImgFlag==2?"Selected":"").'>16:9</option>';
				echo '      <option value="3" '.($episode->EpImgFlag==3?"Selected":"").'>Invalid Aspect Ratio</option>';
				echo '      <option value="4" '.($episode->EpImgFlag==4?"Selected":"").'>Image too small</option>';
				echo '      <option value="5" '.($episode->EpImgFlag==5?"Selected":"").'>Black Bars</option>';
				echo '      <option value="6" '.($episode->EpImgFlag==6?"Selected":"").'>Improper Action Shot</option>';
				echo '    </select>';
				echo '  </td>';
				echo '</tr>';
			}
	
			if ($series->locked == 'yes' or $season->locked == 'yes' or $episode->locked == 'yes' )  {
		   echo "<tr>";
		     echo '<td style="text-align: left" colspan="3" valign="top">';
			if ($_SESSION['userlevel'] == 'SUPERADMIN')  {
				echo '<input type="submit" name="function" value="Save Episode" class="submit"><br>
				<input type="submit" name="function" value="Delete Episode" class="submit_red" onClick="return confirmSubmit()"><br>';
			  if ($episode->locked != 'yes') {
				$link = "/?tab=moveepisode&seriesid=$seriesid&seasonid=$seasonid&id=$id$urllang";
				echo '<INPUT TYPE="BUTTON" VALUE="Move Episode" ONCLICK="window.location.href=\''.$link.'\'"><br>';
			  }
			}
			else {echo '&nbsp';}
			if  ($series->locked == 'yes')  {
				print "<div id=formnote style='color: red;'>This episode is locked at the series level and cannot be changed.<br> It was locked by $lockadmin->username</div>";
			}
			elseif  ($season->locked == 'yes'){
				print "<div id=formnote style='color: red;'>This episode is locked at the season level and cannot be changed.<br> It was locked by $lockadmin->username</div>";
			}
			else {
				if ($loggedin == 1)  {  ?>
						<?php if ($adminuserlevel == 'ADMINISTRATOR' AND $lockadmin->userlevel != 'SUPERADMIN')  {      
							echo '<input type="submit" value="Lock Episode" name="function" class="submit" disabled><br>';
							echo '<input type="submit" value="UnLock Episode" name="function" class="submit"><br>';
							$link = "/?tab=moveepisode&seriesid=$seriesid&seasonid=$seasonid&id=$id$urllang";
							echo '<INPUT TYPE="BUTTON" VALUE="Move Episode" ONCLICK="window.location.href=\''.$link.'\'"><br>';
						}
						    elseif ($_SESSION['userlevel'] == 'SUPERADMIN')  {  	
							echo '<input type="submit" value="Lock Episode" name="function" class="submit" disabled><br>';
							echo '<input type="submit" value="UnLock Episode" name="function" class="submit"><br>';
							$link = "/?tab=moveepisode&seriesid=$seriesid&seasonid=$seasonid&id=$id$urllang";
							echo '<INPUT TYPE="BUTTON" VALUE="Move Episode" ONCLICK="window.location.href=\''.$link.'\'"><br>';
						}
						?>
				<? }
				print "<div id=formnote style='color: red;'>This episode is locked at the episode level and cannot be changed.<br> It was locked by $lockadmin->username</div>";

			}
		   echo "</td>
		     </tr>";

		} else { ?>
 
				<?php	if ($loggedin == 1)  {  ?>
				<tr>
					<td style="text-align: left" colspan="2">
						<div class="float-right">
						<input type="submit" name="function" value="Save Episode" class="submit">
						</div>

						<div class="float-left">
						<?php	if ($adminuserlevel == 'ADMINISTRATOR')  {  ?>
								<input type="submit" name="function" value="Delete Episode" class="submit_red" onClick="return confirmSubmit()"><br>
								<?php if ($adminuserlevel == 'ADMINISTRATOR')  {  
									echo '<input type="submit" value="Lock Episode" name="function" class="submit"><br>';
									echo '<input type="submit" value="UnLock Episode" name="function" class="submit" disabled><br>';
									$link = "/?tab=moveepisode&seriesid=$seriesid&seasonid=$seasonid&id=$id$urllang";
									echo '<INPUT TYPE="BUTTON" VALUE="Move Episode" ONCLICK="window.location.href=\''.$link.'\'"><br>';
								} ?>
						<?php	}  ?>
						</div>
					</td>
				</tr>
		<?php	}  } ?>
	</table>
</form>


<?php	## Display only the episode links we need
	if ($episodes->minimum != $episodes->maximum)  {
		print "<div class=\"subsection\">";

		if ($episodes->minimum < $episode->EpisodeNumber)  {
			print "<a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$minepisode->id$urllang\">Previous Episode</a>";
		}

		if (($episodes->minimum < $episode->EpisodeNumber) && ($episodes->maximum > $episode->EpisodeNumber))  {
			print " | ";
		}

		if ($episodes->maximum > $episode->EpisodeNumber)  {
			print "<a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$maxepisode->id$urllang\">Next Episode</a>";
		}
		print "</div>";
	}
?>
</div>
	</td>



	<td>

<?php	## Display only the episode links we need
//  if ($user->id == 2)  {
	print '<div class="section">';
	if ($episodes->minimum != $episodes->maximum)  {
		print "<div class=\"subsection\" style=\"margin-right: 40px\">";

		if ($episodes->minimum < $episode->EpisodeNumber)  {
			print "<a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$minepisode->id$urllang\">Previous Episode</a>";
		}

		if (($episodes->minimum < $episode->EpisodeNumber) && ($episodes->maximum > $episode->EpisodeNumber))  {
			print " | ";
		}

		if ($episodes->maximum > $episode->EpisodeNumber)  {
			print "<a href=\"/?tab=episode&seriesid=$seriesid&seasonid=$seasonid&id=$maxepisode->id$urllang\">Next Episode</a>";
		}
		print "</div>";
	}
	print "</div>";
//  }
?>


                <div class="section">
                <H1>Rating</h1>
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                        <td><b>Site Rating:</b></td>
                        <td align="right">
                	<?php   ## Get site rating for this series
				$query  = "SELECT AVG(rating) AS average, COUNT(rating) AS count FROM ratings WHERE itemtype='episode' AND itemid=$id";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$rating = mysql_fetch_object($result);
				
				for ($i = 1; $i <= 10; $i++)  {
					if ($i <= $rating->average)  {
						print "<img src=\"/images/star_on.png\" width=15 height=15 border=0>";
					}
					else  {
						print "<img src=\"/images/star_off.png\" width=15 height=15 border=0>";
					}
				}
			?>
			<div id="smalltext"><?=$rating->count?> rating<?php if ($rating->count != 1) print "s" ?></div>
			</td>
		</tr>

       		<?php   if ($loggedin == 1)  {  ?>
                <tr>
                        <td><b>Your Rating:</b></td>
                        <td align="right">
                	<?php   ## Get user rating for this series
				$query  = "SELECT rating FROM ratings WHERE itemtype='episode' AND itemid=$id AND userid=$user->id";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$rating = mysql_fetch_object($result);

				for ($i = 1; $i <= 10; $i++)  {
					if ($i <= $rating->rating)  {
						print "<a href=\"/?function=UserRating&type=episode&itemid=$id&rating=$i&seriesid=$seriesid&seasonid=$seasonid\" OnMouseOver=\"UserRating($i)\" OnMouseOut=\"UserRating($rating->rating)\"><img src=\"/images/star_on.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
					}
					else  {
						print "<a href=\"/?function=UserRating&type=episode&itemid=$id&rating=$i&seriesid=$seriesid&seasonid=$seasonid\" OnMouseOver=\"UserRating($i)\" OnMouseOut=\"UserRating($rating->rating)\"><img src=\"/images/star_off.png\" width=15 height=15 border=0 name=\"userrating$i\"></a>";
					}
				}
			?>
			</td>
		</tr>
		<?php	}  ?>
		</table>
		</div>


<div class="section">
<h1>Episode Thumbnail</h1>
	<?php	## Display this episode image
		if ($episode->filename) {
			$query	= "SELECT username AS creator, 'N/A' AS language, 0 AS rating, 0 AS ratingcount FROM users WHERE id=$episode->thumb_author LIMIT 1";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$banner = mysql_fetch_object($result);
			$banner->filename = $episode->filename;
			$banner->userid = $episode->thumb_author;

 		  	if ($episode->thumb_author == $user->id || $adminuserlevel == 'ADMINISTRATOR') {
				displaybannernew($banner, 1, "banners/$episode->filename");
	 		}
			else  {
				displaybannernew($banner, 0, "banners/$episode->filename");
			}
		}
		else  {
			print "There is no image for this episode\n";
		}
	?>
</div>


<?php	## Display form if logged in
	if ($loggedin == 1 && $episode->filename == "")  {
		## Start the section
		print "<div class=\"section\">\n";
        	print "<h1>Episode Banner Upload</h1>\n";


		## If the person can't upload banners
		if ($user->bannerlimit == 0) {
			print "Your ability to upload has been removed. If you believe this has happened in error contact <a href=\"mailto:$adminuser->emailaddress\">$adminuser->username</a>";
		}
		elseif ($series->disabled == 'Yes')  {
			print "The ability to upload has been removed because an admin has flagged this record as a duplicate or inaccurate";
		}
		else  {
		?>

	        <form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
		<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
		<tr>
	          	<td>File:</td>
	          	<td>
	          		<input type="file" name="bannerfile">
	          		<div id="formnote"><u>Episode banners must be:</u><br><b>No larger than 400px by 300px and <br>No smaller than 280px by 150px.</b></div>
	          	</td>
	        </tr>
	        <tr>
	          	<td colspan="2" style="text-align: right">
	          		<input type="hidden" name="function" value="Upload Episode Banner">
	          		<input type="submit" name="button" value="Upload" class="submit">
	          	</td>
	        </tr>
	        </table>
          	</form>
<?php
	      	}
		print "</div>\n";
        } 
  
  ##Displays upload box if banner is flagged  
  if ($episode->EpImgFlag > 2 && $episode->EpImgFlag < 7){
?>
		<div class="section">
		<form action="<?=$fullurl?>" method="POST" enctype="multipart/form-data">
 	  <h1>Replace Episode Banner</h1>

<?php
		if ($series->disabled == 'Yes' || $user->bannerlimit == 0)  { 
			print "<div style='width:250px;'>";
			print "The ability to upload has been removed.";
			print "</div>";
		}
		else
		{
?>
	 	 <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="info">
		   <tr>
	 	     <td>File:</td>
	 	     <td>
	 	       <input type="file" name="bannerfile">
	 	       <div id="smalltext">
	 	       	 <?if ($episode->EpImgFlag == 3){echo 'Improper aspect ratio on current image, you may replace it.<br><br>';}
	 	       	   if ($episode->EpImgFlag == 4){echo 'Image too small, you may replace it.<br>';}
	 	       	   if ($episode->EpImgFlag == 5){echo 'Black bars in image, you may replace it.<br>';}
	 	       	   if ($episode->EpImgFlag == 6){echo 'Improper action shot, you may replace it.<br>';}?>
	 	         <u>Episode banners must be:</u><br><b>No larger than 400px by 300px and <br>No smaller than 300px by 170px.</b><br><b>A proper aspect ratio (4:3 or 16:9 only).</b>
	 	       </div>
		     </td>
		   </tr>
		   <tr>
		     <td colspan="2" style="text-align: right">
	 	       <input type="hidden" name="function" value="Replace Episode Banner">
	 	       <input type="submit" name="button" value="Replace" class="submit">
		     </td>
		   </tr>
		 </table>
<?php
		}
?>
 	 </form>
 	 </div>
<?php	
  } 
?>

	</td>
</tr>
</table>
