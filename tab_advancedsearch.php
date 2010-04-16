<?
##Checks if criteria is entered/valid and builds WHERE statement
if($searching){
	$seriesname = mysql_real_escape_string($seriesname);
	$genre = mysql_real_escape_string($genre);
	$network = mysql_real_escape_string($network);
	$order = mysql_real_escape_string($order);
	
	$endyear = Date("Y")+2;
	
	$where = "WHERE 1";
	if ($language > 0 && $language < 100){$where.=" and translation_seriesname.languageid = $language";}
	if ($year >= 1900 && $year <= $endyear){$where.=" and year(FirstAired) = ".$year;}elseif($year != ""){$errmsg.="Year must be between 1900 and ".$endyear."<br>";unset($searching);}
	if ($imdb_id == "null"){unset($imdb_id); $where.=" and imdb_id is null";}
	if (strtolower(substr($imdb_id,0,2)) == "tt" && substr($imdb_id,2,12) >= 1 && substr($imdb_id,2,12) < 999999999){$where.=" and imdb_id = '".$imdb_id."'";}elseif($imdb_id != ""){$errmsg.="Invalid IMDB.com ID.<br>";unset($searching);}	
	if ($tvcom_id == "null"){unset($tvcom_id); $where.=" and tvseries.SeriesID is null";}
  if ($tvcom_id > 0 && $tvcom_id < 9999999){$where.=" and tvseries.SeriesID = '".$tvcom_id."'";}elseif($tvcom_id != ""){$errmsg.="Invalid tv.com ID.<br>";unset($searching);}
  if (strtoupper(substr($zap2it_id,0,2)) == "SH" && substr($zap2it_id,2,12) >= 1 && substr($zap2it_id,2,12) < 999999999){$where.=" and zap2it_id = '".$zap2it_id."'";}elseif($zap2it_id != ""){$errmsg.="Invalid zap2it.com ID.<br>";unset($searching);}
  if (strlen($seriesname) > 1 && strlen($seriesname) < 80){$where.=" and translation_seriesname.translation LIKE '%".$seriesname."%' ";}elseif($seriesname != ""){$errmsg=$errmsg."Series names must be inbetween 2 and 80 characters in length.<br>";unset($searching);}
  if (strlen($genre) > 3 && strlen($genre) < 26){$where.=" and tvseries.Genre like '%".$genre."%' ";}
  if (strlen($network) > 1 && strlen($network) <= 30){$where.=" and tvseries.network like '%".$network."%' ";}elseif($network != ""){$errmsg=$errmsg."Network must be between 2 and 30 characters.<br>";unset($searching);}
  if (strlen($order) > 2 && strlen($order) < 15){$orderby=" ORDER BY ".$order;}else{$errmsg.="Invalid order option.<br>";unset($searching);}
}

##Displays search options if no search criteria has been given.
if(!$searching){?>
<div class="section">
<h1>Advanced Search</h1>
  <p id="red"><?=$errmsg?></p>
	<form action="index.php" method="GET">
	<table border="0" cellspacing="0" cellpadding="2" width="50%">
		<tr>
			<td>Series Name: </td>
			<td><input type="text" name="seriesname" maxlength="80" STYLE="width: 140px" value="<?=$seriesname?>"></td>
			<td></td>
		</tr>
		<tr>
			<td>Language: </td>
			<td>
				<select name="language" STYLE="width: 145px">
					<option value=''>All</option>
					<?$query = "SELECT * FROM languages";
					  $result = mysql_query($query) or die('Query failed: List ' . mysql_error());
			      while ($languagelist = mysql_fetch_object($result)) {
				      print "<option value='$languagelist->id'>$languagelist->name</option>\n";
			      }?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Genre: </td>
			<td>
				<select name="genre" STYLE="width: 145px">
					<option value=''>All</option>
					<?$query = "SELECT * FROM genres";
					  $result = mysql_query($query) or die('Query failed: List ' . mysql_error());
			      while ($languagelist = mysql_fetch_object($result)) {
				      print "<option value='$languagelist->genre'>$languagelist->genre</option>\n";
			      }?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Year: </td>
			<td><input type="text" name="year" maxlength="4" STYLE="width: 140px" value="<?=$year?>"></td>
			<td></td>
		</tr>
		<tr>
			<td>Network: </td>
			<td><input type="text" name="network" maxlength="30" STYLE="width: 140px" value="<?=$network?>"></td>
			<td></td>
		</tr>
		<tr>
			<td>Zap2it: </td>
			<td><input type="text" name="zap2it_id" maxlength="11" STYLE="width: 140px" value="<?=$zap2it_id?>"></td>
			<td id="formnote">Must include leading SH</td>
		</tr>
		<tr>
			<td>TV.com ID: </td>
			<td><input type="text" name="tvcom_id" maxlength="7" STYLE="width: 140px" value="<?=$tvcom_id?>"></td>
			<td></td>
		</tr>
		<tr>
			<td>IMDB.com ID:</td>
			<td><input type="text" name="imdb_id" maxlength="11" STYLE="width: 140px" value="<?=$imdb_id?>"></td>
			<td id="formnote">Must include leading TT</td>
		</tr>
		<tr>
			<td>Order By:</td>
			<td>
				<select name="order" STYLE="width: 145px">
					<option value='translation'>Series Name</option>
					<option value='name'>Language</option>
					<option value='Status'>Status</option>
					<option value='Network'>Network</option>
					<option value='Genre'>Genre</option>
					<option value='srating desc'>Rating</option>
				</select>				
			</td>
		<td id="formnote"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Search" name="searching">
				<input type="hidden" name="tab" value="advancedsearch">
			</td>
			<td></td>
		</tr>
	</table>
	</form>
</div>


<?
##Displays results if search criteria was given.
}else{?>
<div class="section">
<h1>Results - 50 Max</h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head"></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=translation", $_SERVER['QUERY_STRING']);?>">Series Name</a></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=Genre", $_SERVER['QUERY_STRING']);?>">Genre</a></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=Status", $_SERVER['QUERY_STRING']);?>">Status</a></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=name", $_SERVER['QUERY_STRING']);?>">Language</a></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=Network", $_SERVER['QUERY_STRING']);?>">Network</a></td>
			<td class="head"><a href="./?<?echo str_replace("order=".$order, "order=srating desc", $_SERVER['QUERY_STRING']);?>">Rating</a></td>
		</tr>
<?
	$query = "SELECT *, tvseries.id as showid, (SELECT round(avg(rating)) FROM ratings WHERE itemid = tvseries.id and itemtype='series') AS srating FROM (translation_seriesname INNER JOIN tvseries ON translation_seriesname.seriesid = tvseries.id) INNER JOIN languages ON translation_seriesname.languageid = languages.id ".$where.$orderby." LIMIT 50";
	$result = mysql_query($query) or die('Query failed: List ' . mysql_error());
			while ($serieslist = mysql_fetch_object($result)) {
				if ($class == 'odd')  { $class = 'even';  }  else  { $class = 'odd';  }
				$seriescount++;
				print "<tr><td class=\"$class\">$seriescount</td><td class=\"$class\"><a href=\"/index.php?tab=series&amp;id=$serieslist->showid&amp;lid=$serieslist->languageid\">$serieslist->translation</a></td><td class=\"$class\">$serieslist->Genre</a></td><td class=\"$class\">$serieslist->Status</td><td class=\"$class\">$serieslist->name</td><td class=\"$class\">$serieslist->Network</td><td class=\"$class\">$serieslist->srating</td></tr>\n";				
			}
			if ($seriescount == 0){echo "<tr><td class='odd' colspan='4'>No Series found.</td></tr>";}			
?>
	</table>
</div>
<?}?>