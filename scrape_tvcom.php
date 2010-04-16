<?php	## Get the data from TV.com
	$errormessage = "";

	## First, let's get the season page
	$page_season = file_get_contents("http://www.tv.com/show/$tvcomid/episode_listings.html?season=$season");

	## Now let's parse out the episode page
	$lookupepisode = $episode - 1;
	if (preg_match("/<a href=\"(http:\/\/www\.tv\.com\/.*?\/.*?\/episode\/.*?\/summary\.html\?tag=ep_list\;title\;$lookupepisode)\">/", $page_season, $matches))  {

		## Now let's get the episode page
		$page_episode = file_get_contents("$matches[1]");

		## Parse: title
		if (preg_match("/.*Episode: <span class=\"f-FF9\">([^<]*)<\/span><br \/>/", $page_episode, $matches))  {
			if ($matches[1] == 'n/a')  {  $matches[1] = "";  }
			$EpisodeName = trim($matches[1]);
		}

		## Parse: first aired
		if (preg_match("/.*First Aired: ([^&]*)/", $page_episode, $matches))  {
			if ($matches[1] == 'n/a')  {  $matches[1] = "";  }
			$FirstAired = trim($matches[1]);
		}

		## Parse: guest stars
		if (preg_match("/Guest Star:(.*?)<tr>/ms", $page_episode, $matches))  {
			$match = strip_tags($matches[1]);
			$match = preg_replace("/ \([^,]*\)/", "", $match);
			$match = preg_replace("/&nbsp;/", " ", $match);
			$match = preg_replace("/\s+/", " ", $match);
			$match = preg_replace("/, /", "|", $match);
			if ($match == 'n/a')  {  $match = "";  }
			$GuestStars = trim($match);
		}

		## Parse: director
		if (preg_match("/Director:(.*?)<tr>/ms", $page_episode, $matches))  {
			$match = strip_tags($matches[1]);
			$match = preg_replace("/ \([^,]*\)/", "", $match);
			$match = preg_replace("/&nbsp;/", " ", $match);
			$match = preg_replace("/\s+/", " ", $match);
			$match = preg_replace("/, /", "|", $match);
			if ($match == 'n/a')  {  $match = "";  }
			$Director = trim($match);
		}

		## Parse: writer
		if (preg_match("/Writer:(.*?)<tr>/ms", $page_episode, $matches))  {
			$match = strip_tags($matches[1]);
			$match = preg_replace("/ \([^,]*\)/", "", $match);
			$match = preg_replace("/&nbsp;/", " ", $match);
			$match = preg_replace("/\s+/", " ", $match);
			$match = preg_replace("/, /", "|", $match);
			if ($match == 'n/a')  {  $match = "";  }
			$Writer = trim($match);
		}



		## Parse: production code
		if (preg_match("/Prod Code: ([^<]*)/", $page_episode, $matches))  {
			if ($matches[1] == 'n/a')  {  $matches[1] = "";  }
			$ProductionCode = trim($matches[1]);
		}

		## Parse: overview
		if (preg_match("/div>([^=]*)<div class=\"ta-r mt-10 f-bold\">/", $page_episode, $matches))  {
			$match = strip_tags($matches[1]);
			$match = preg_replace("/&nbsp;/", " ", $match);
			if ($match == 'n/a')  {  $match = "";  }
			$Overview = trim($match);
		}
		
	}

	## No matches found... set error message
	else  {
		$errormessage = "No episodes on TV.com found. Please verify the TV.com ID ($tvcomid), the season ($season) and the episode ($episode).";
	}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Episode Scraper</title>
	<link rel=stylesheet href="<?php echo $baseurl;?>/pngHack/pngHack.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="<?php echo $baseurl;?>/default.css">
	<script type="text/javascript" src="<?php echo $baseurl;?>/xfade2.js"></script>
	<script type="text/javascript" src="<?php echo $baseurl;?>/niftycube.js"></script>
	<script type="text/javascript">
		window.onload=function(){
			Nifty("DIV.section","big");
			Nifty("DIV.footer","big");
			Nifty("DIV.titlesection","big");
		}
	</script>
	<script type="text/javascript">
		function copyepisode() {
			opener.document.episodeform.EpisodeName_7.value = document.tvcomform.EpisodeName.value;
			opener.document.episodeform.FirstAired.value = document.tvcomform.FirstAired.value;
			opener.document.episodeform.GuestStars.value = document.tvcomform.GuestStars.value;
			opener.document.episodeform.Director.value = document.tvcomform.Director.value;
			opener.document.episodeform.Writer.value = document.tvcomform.Writer.value;
			opener.document.episodeform.ProductionCode.value = document.tvcomform.ProductionCode.value;
			opener.document.episodeform.Overview_7.value = document.tvcomform.Overview.value;
			self.close();
			return false;
		}
	</script>
</head>
<body>
<table width="95%" cellspacing="0" cellpadding="0" border="0" align="center">
<tr><td>
	<div class="titlesection">
		<h1>TV.com Episode Import</h1>
		<h2><?=$seriesname?></h2>
		<h3>Season <?=$season?> x Episode <?=$episode?></h3>
		<p>Please review the information below and click "Copy" to copy the information back to the episode page.  Please note that you MUST press "Save Episode" on the episode page, or the changes will be lost. Also note that this only applies to the English translations of the episode name and overview.</p>
	</div>

	<div class="section">
		<div id="red"><?=$errormessage?></div>

		<?php	if ($errormessage == '')  {  ?>
		<form action="#" method="POST" name="tvcomform" onSubmit="return false">


			<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="datatable">
			<tr>
				<td>Episode Name: </td>
				<td><input type="text" name="EpisodeName" value="<?=$EpisodeName?>"></td>
			</tr>

			<tr>
				<td>First Aired:</td>
				<td><input type="text" name="FirstAired" value="<?=$FirstAired?>" maxlength="255"></td>
			</tr>

			<tr>
				<td>Guest Stars:</td>
				<td><input type="text" name="GuestStars" value="<?=$GuestStars?>" maxlength="255"></td>
			</tr>

			<tr>
				<td>Director:</td>
				<td><input type="text" name="Director" value="<?=$Director?>" maxlength="255"></td>
			</tr>

			<tr>
				<td>Writer:</td>
				<td><input type="text" name="Writer" value="<?=$Writer?>" maxlength="255"></td>
			</tr>

			<tr>
				<td>Production Code:</td>
				<td><input type="text" name="ProductionCode" value="<?=$ProductionCode?>" maxlength="45"></td>
			</tr>

			<tr>
				<td>Overview:</td>
				<td><textarea rows="10" cols="45" name="Overview"><?=$Overview?></textarea></td>
			</tr>

			<tr>
				<td style="text-align: right" colspan="2">
					<input type="submit" name="function" value="Copy" class="submit" onClick="return copyepisode()">
				</td>
			</tr>
			</table>


		</form>
		<?php	}  ?>
	</div>

</td></tr>
</table>
</body>
</html>
