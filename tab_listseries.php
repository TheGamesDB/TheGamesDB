<?php	## Handle searches differently
	if ($_SESSION['userid'] && !$alllang){
		$languagelimit = "AND languageid = (SELECT languageid FROM users WHERE id = ".$_SESSION['userid'].")";
		$query = "SELECT languages.name FROM users INNER JOIN languages ON users.languageid = languages.id WHERE users.id=".$_SESSION['userid'];
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	
	if ($function == 'Search')  {
		$title = 'Search : ' . $string;
	}
	elseif ($function == 'OverviewSearch')  {
		$title = 'Overview Search : ' . $string;
	}
	else  {
		$title = $letter;
	}
?>

<div class="section">
<h1>TV Shows | <?=$title?></h1>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Show Title</td>
			<td class="head">Language</td>			
			<td class="head">Show ID</td>
		</tr>

		<?php	## Run the games query
			$gamecount = 0;
			$string = mysql_real_escape_string($string);
			$letter = mysql_real_escape_string($letter);			

			if ($function == 'Search')  {
				$query = "SELECT * FROM games WHERE GameTitle LIKE '%$string%' ORDER BY GameTitle";
			}
			elseif ($function == 'OverviewSearch')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesoverview.languageid) As language FROM translation_seriesoverview WHERE translation LIKE '%$string%' ORDER BY ID";
			}
			else if ($letter == 'OTHER')  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) NOT BETWEEN 'A' AND 'Z' $languagelimit ORDER BY translation";
			}
			else  {
				$query = "SELECT *, (SELECT name FROM languages WHERE id=translation_seriesname.languageid) As language FROM translation_seriesname WHERE SUBSTRING(translation,1,1) = '$letter' $languagelimit ORDER BY translation";
			}
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());

			## Display each game
			while ($game = mysql_fetch_object($result)) {
				if ($class == 'odd')  {  $class = 'even';  }  else  {  $class = 'odd';  }
				print "<tr><td class=\"$class\"><a href=\"$baseurl/?tab=game&amp;id=$game->id&amp;lid=1\">$game->GameTitle</a> </td><td class=\"$class\">$game->id</td></tr>\n";
				$gamecount++;
			}

			## No matches found?
			if ($gamecount == 0)  {
				print "<tr><td class=odd colspan=2>No Game Found. ";
				if (!$alllang){print "Retry <a href=\"$baseurl/index.php?".$_SERVER["QUERY_STRING"]."&alllang=1\">search</a> in all languages?";}
				print "</td></tr>\n";
				
			}
		?>
		</table>
</div>

