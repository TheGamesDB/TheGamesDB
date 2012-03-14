<?php
	global $baseurl;
	global $db_user;
	global $db_password;
	global $db_database;
	global $db_server;
	global $apache_type;
	global $userfavorites;
	include("config.php");
	set_time_limit(360);

	$database = mysql_connect($db_server, $db_user, $db_password) or die('Could not connect: ' . mysql_error());
	mysql_select_db($db_database) or die('Could not select database');
	$result	= mysql_query("SET NAMES 'utf8'") or die('Query failed: ' . mysql_error());


	## Function to create the series name for the URL.  This removes all non alphanumeric characters
	## and converts spaces to underscores.
	function urlseriesname($seriesid) {
		$query		= "SELECT translation FROM translation_seriesname WHERE seriesid=$seriesid AND languageid=$lid LIMIT 1";
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
		$seriesinfo	= mysql_fetch_object($result);

		$urlseriesname	= preg_replace("/[^0-9a-zA-Z\s]/", "", $seriesinfo->translation);
		$urlseriesname	= ucwords($urlseriesname);
		$urlseriesname	= preg_replace("/\s/", "", $urlseriesname);
		if ($urlseriesname == '')  {
			#return "ForeignSeries";
			return $seriesid;
		}
		else  {
			return $urlseriesname;
		}
	}

	## Function to create a seriesupdate record. This is used whenever a series or episode needs
	## to be updated in the XML files
	function seriesupdate($seriesid)  {
		$query		= "UPDATE games SET lastupdated=UNIX_TIMESTAMP() WHERE id=$seriesid";
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
	}


	## Function to randomly generate a pronouncable password
	function genpassword($length) {
		srand((double)microtime()*1000000);
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
		"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");

		$num_vowels = count($vowels);
		$num_cons = count($cons);
		for($i = 0; $i < $length; $i++){
			$password .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
		}
		return substr($password, 0, $length);
	}

	## Function to store a SQL call in the sqlhistory table
	function storesql($sql, $sqlid=0) {
		#if ($sqlid)  {
		#	$sql = preg_replace("/INSERT INTO (\S+) \(/i", "INSERT INTO $1 (id, ", $sql);
		#	$sql = preg_replace("/\) VALUES \(/i", ") VALUES ($sqlid, ", $sql);
		#}

		#$sql = mysql_real_escape_string($sql);
		#$query	= "INSERT INTO history (statement) VALUES ('SQL: $sql')";
		#$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		#return 1;
	}

	## Function to store a file change in the sqlhistory table
	function storefile_add($path) {
		$path = mysql_real_escape_string($path);
		$query	= "INSERT INTO history (statement) VALUES ('FILE: $path')";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		return 1;
	}

	## Function to store a file change in the sqlhistory table
	function storefile_del($path) {
		$path = mysql_real_escape_string($path);
		$query	= "INSERT INTO history (statement) VALUES ('DELETE: $path')";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
		return 1;
	}
	## Function to store a file change in the sqlhistory table
	function translatetext($english) {
		if ($texttranslations[$english])  {
			return $texttranslations[$english];
		}
		else  {
			return $english;
		}
	}

## Function to replicate file_put_contents for php4
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data, $respect_lock = true)
    {
        // Open the file for writing
        $fh = @fopen($filename, 'w');
        if ($fh === false) {
            return false;
        }
        // Check to see if we want to make sure the file is locked before we write to it
        if ($respect_lock === true && !flock($fh, LOCK_EX)) {
            fclose($fh);
            return false;
        }
        // Convert the data to an acceptable string format
        if (is_array($data)) {
            $data = implode('', $data);
        } else {
            $data = (string) $data;
        }
        // Write the data to the file and close it
        $bytes = fwrite($fh, $data);
        // This will implicitly unlock the file if it's locked
        fclose($fh);
        return $bytes;
    }
}

## Function to generate the required entries for rotating series banners
function bannerdisplay($id) {
  $bcount = 0;
  $textbcount = 0;
	$query	= "SELECT * FROM banners WHERE keytype='series' AND keyvalue=$id AND subkey !='blank' and languageid=1 ORDER BY RAND()";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			if ($banner->subkey == 'text')  { $textbcount++; }
			else {
				print "<img src=\"banners/$banner->filename\" alt='banner' title='banner' class='rotatebanner' />";
				$bcount++;
			}
		}
  if ($bcount == 0 AND $textbcount > 0)  {
	$query	= "SELECT * FROM banners WHERE keytype='series' AND keyvalue=$id ORDER BY RAND()";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($banner = mysql_fetch_object($result))  {
			$bcount++;
			if ($banner->subkey == 'text')  {
				$textbcount++;
				print "<img src=\"banners/$banner->filename\" alt='banner' title='banner' class='rotatebanner' />";
			}
		}
  }
	if ($bcount == 0 AND $textbcount == 0) { print "<style>#bannerrotator {display:none;}</style>"; }
}


## Function to generate/display a cached version of a wide banner
## Inputs: banner filename, author, boolean delete, calling url, bannerid, display author bool
## Outputs: resized image file, IMG html code pointing to resized
function displaybanner ($filename, $bannerauthor, $allowdelete, $fullurl, $bannerid, $displayauthor, $link) {

	## Check if the banner is cached already. If not, create it.
	if (!file_exists("../banners/_cache/$filename"))  {
		## Create the cached version of the image
		$banner = file_get_contents("$baseurl/thumbnail.php?gd=2&maxw=300&src=banners/$filename");
		file_put_contents("banners/_cache/$filename", $banner);
	}

	## Display the image
	print "<a href=\"$link\"><img src=\"$baseurl/banners/_cache/$filename\" class=\"banner\" border=\"0\"></a>\n";

	## Display the author
	if ($displayauthor && $allowdelete)  {
		print "<div id=\"bannerauthor\">Banner by $bannerauthor. You can <a href=\"$fullurl&function=Delete+Banner&bannerid=$bannerid\">delete it</a>.</div>\n";
	}
	elseif ($displayauthor)  {
		print "<div id=\"bannerauthor\">Banner by $bannerauthor</div>\n";
	}
}


## Function to generate/display a cached version of a banner
function displaybannernew ($banner, $allowdelete, $link) {

	global $loggedin, $user, $bannercount, $tab, $id, $seriesid, $seasonid, $baseurl;
	switch ($tab) {
		case "mainmenu":
			$fullurl = $baseurl . "/?";
			break;
		case "game":
			$fullurl = $baseurl . "/?tab=game&id=$id";
			break;
		default:
			$fullurl = $baseurl . "/?";
	}


	## Check if the banner is cached already. If not, create it.
	if (!file_exists("../banners/_cache/$banner->filename"))  {
		## Create the cached version of the image
		$target = file_get_contents("$baseurl/thumbnail.php?gd=2&maxw=300&src=banners/$banner->filename");
		file_put_contents("banners/_cache/$banner->filename", $target);
		
		## fetch image dimensions
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize("banners/_cache/$banner->filename");
		$imgWidth = $image_width;
		$imgHeight = $image_height;
	}


	## Display the image
	if ($link)  {
		print "<a href=\"$link\"><img src=\"$baseurl/banners/_cache/$banner->filename\" class=\"banner\" width=\"$imgWidth\" height=\"$imgHeight\" border=\"0\" alt=\"$banner->seriesname\"></a>\n";
	}
	else  {
		print "<img src=\"$baseurl/banners/_cache/$banner->filename\" class=\"banner\" width=\"$imgWidth\" height=\"$imgHeight\" border=\"0\">\n";
	}


	## Display the extra info
	if ($bannercount == 0)  {
		$displaytype = "block";
	}
	else  {	
		$displaytype = "block";	// ORIGINALLY NONE TO HIDE IMAGE DETAILS
	}

	print "<div id=\"bannerinfo$banner->id\" style=\"display:$displaytype\"><table cellspacing=\"2\" cellpadding=\"0\" border=\"0\" width=\"100%\" class=\"bannerinfo\">\n";

	## Skip most stuff for episode images
	if ($tab != 'episode')  {
		print "<tr><td></td><td align=right><a href=\"$baseurl/banners/$banner->filename\" target=\"_blank\">View Full Size</a></td></tr>\n";
		print "<tr><td>Site Rating:</td><td align=\"right\">\n";


		## Get the site banner rating
		$query  = "SELECT AVG(rating) AS average, count(*) AS count FROM ratings WHERE itemtype='banner' AND itemid=$banner->id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$rating = mysql_fetch_object($result);


		## Display the site rating
		for ($i = 1; $i <= 10; $i++)  {
			if ($i <= $rating->average)
				print "<img src=\"$baseurl/images/game/star_on.png\" width=15 height=15 border=0>";
			else 
				print "<img src=\"$baseurl/images/game/star_off.png\" width=15 height=15 border=0>";
		}
		print "</td></tr>\n";


		## Get the user banner rating
		if ($loggedin == 1)  {
			print "<tr><td>Your Rating:</td><td align=\"right\">\n";
			## Get user rating for this series
				$query  = "SELECT rating FROM ratings WHERE itemtype='banner' AND itemid=$banner->id AND userid=$user->id";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$rating = mysql_fetch_object($result);
				if (!$rating->rating)  {
					$rating->rating = 0;
				}
		
				for ($i = 1; $i <= 10; $i++)  {
					if ($i <= $rating->rating)  {
						print "<a href=\"$fullurl&function=UserRating&type=banner&itemid=$banner->id&rating=$i\" OnMouseOver=\"UserRating2('bannerrating$banner->id', $i)\" OnMouseOut=\"UserRating2('bannerrating$banner->id', $rating->rating)\"><img src=\"/images/game/star_on.png\" width=15 height=15 border=0 name=\"bannerrating$banner->id$i\"></a>";
					}
					else  {
						print "<a href=\"$fullurl&function=UserRating&type=banner&itemid=$banner->id&rating=$i\" OnMouseOver=\"UserRating2('bannerrating$banner->id',$i)\" OnMouseOut=\"UserRating2('bannerrating$banner->id',$rating->rating)\"><img src=\"/images/game/star_off.png\" width=15 height=15 border=0 name=\"bannerrating$banner->id$i\"></a>";
					}
				}
			print "</td></tr>\n";
		}

		## Display extra info
		print "<tr><td>Ratings:</td><td align=right>$banner->ratingcount</td></tr>\n";
		print "<tr><td>Created:</td><td align=right>" . date("F j, Y H:i", $banner->dateadded) . "</td></tr>\n";
	}
	print "<tr><td>Creator:</td><td align=right><a href=$baseurl/?tab=artistbanners&id=$banner->userid>$banner->creator</a></td></tr>\n";
	if ($tab != "episode")  {
		if ($banner->keytype != "season" && $banner->keytype != "seasonwide")  {
			print "<tr><td>Format:</td><td align=right>" . ucwords($banner->subkey) . "</td></tr>\n";
		}
		print "<tr><td>Language:</td><td align=right>$banner->language</td></tr>\n";
	}


	## Print the delete banner link
	if ($allowdelete && $tab == "episode")  {
		print "<tr><td></td><td align=right>You can <a href=\"$fullurl&function=Delete+Episode+Banner\">delete it</a>.</td></tr>\n";
	}
	elseif ($allowdelete)  {
		print "<tr><td></td><td align=right>You can <a href=\"$fullurl&function=Delete+Banner&bannerid=$banner->id\">delete it</a>.</td></tr>\n";
	}


	## Finish off the table for this banner
	print "</table></div>\n";
}


## Function to obfuscate email so it's readable by spambots
function cryptemail($myemail)
{
  $i=0; // initializing the counter to count each character
  do{
  $codechar =  substr($myemail,$i,1); // Read each character of the string
  $code = ord($codechar); // Convert each character to its corresponding ASCII value/number
       if($code == "&#0") // If space/NULL is there, don't print anything ( After last character what will happen?)
          { print ""; }
               else { $cryptmail = $cryptmail."&#$code"; // Make a new string with cryptic ASCII codes
			   }
   $i++;
  } while($codechar != ""); // End of do-while loop
return $cryptmail;
} // end of function

## Function to generate meaningful page titles
## Inputs: Series ID, Season ID, Episode ID, Tab Name, Language ID
function titlegenerator($titleseriesid, $titleseasonid, $titleepisodeid, $tab, $lid)
{
	$id = mysql_real_escape_string($titleseriesid);
	$seriesname = "";
	$query	= "SELECT * FROM translation_seriesname WHERE seriesid=$id && (languageid=1 || languageid=$lid)";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while($series = mysql_fetch_object($result))  {
		if ($seriesname == "" || $series->languageid != 7)  {
			$seriesname = $series->translation;
		}
	}
	
	if ($titleepisodeid != 'Null') {
		$episodeid = mysql_real_escape_string($titleepisodeid);
		$episodename = "";
		$query	= "SELECT * FROM translation_episodename WHERE episodeid=$titleepisodeid && (languageid=1 || languageid=$lid)";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while($episode = mysql_fetch_object($result))  {
			if ($episodename == "" || $episode->languageid != 7)  {
				$episodename = $episode->translation;
			}
		}
		print "<title>$seriesname: $episodename</title>";
	}
	elseif ($titleseasonid != 'Null') {
		$seasonid = mysql_real_escape_string($titleseasonid);
		$query	= "SELECT season FROM tvseasons WHERE id=$seasonid";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$season = mysql_fetch_object($result);
		print "<title>$seriesname: Season $season->season Episode List</title>";
	}
	elseif ($tab == 'seasonall') {
		print "<title>$seriesname: Complete Episode List</title>";
	}
	else {
		print "<title>$seriesname: Series Info</title>";
	}
} // end of function

## Function to check for special episodes
function checkspecial($epnum, $specials, $seriesid) {
	if ($specials)  {
		foreach ($specials as $specialepisode) {
			foreach ($specialepisode as $specialdata) {
				if (key($specialepisode) == $epnum) {
					$explode = explode("'..'", $specialdata);
					print "<tr><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">Special</a></td><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">$explode[1]</a></td><td class=\"special\">$explode[4]</td><td class=\"special\">";
					if ($explode[5]) {
						print "<img src=\"$baseurl/images/checkmark.png\" width=10 height=10>";
					}
					else {echo "&nbsp;";}
					print "</td></tr>\n";
				}
			}
		}
	}
}

## Function to check for special episodes that air after a season
function checkspecialafter($seasonnum, $afterspecials, $seriesid) {
	if ($afterspecials)  {
		foreach ($afterspecials as $afterspecialepisode) {
			foreach ($afterspecialepisode as $specialdata) {
				if (key($afterspecialepisode) == $seasonnum) {
					$explode = explode("'..'", $specialdata);
					print "<tr><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">Special</a></td><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">$explode[1]</a></td><td class=\"special\">$explode[3]</td><td class=\"special\">";
					if ($explode[4]) {
						print "<img src=\"$baseurl/images/checkmark.png\" width=10 height=10>";
					}
					else {echo "&nbsp;";}
					print "</td></tr>\n";
				}
			}
 		}
	}
}

## Function to check for special episodes for all season page
function checkspecialall($epnum, $specials, $seriesid,$seasonnumber) {
if ($specials)  {
  foreach ($specials as $specialepisode) {
	foreach ($specialepisode as $specialdata) {
	  if (key($specialepisode) == $epnum) {
 	  	$explode = explode("'..'", $specialdata);
	  	if ($seasonnumber == $explode[6]) {
	  		print "<tr><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">Special</a></td><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">$explode[1]</a></td><td class=\"special\">$explode[4]</td><td class=\"special\">";
	  		if ($explode[5]) {
				print "<img src=\"$baseurl/images/checkmark.png\" width=10 height=10>";
		  	}
		  	else {echo "&nbsp;";}
	  		print "</td></tr>\n";
	  	}
	  }
  	}
  }
}
}

## Fan art color function
function imagecolors($filename) {
        $colorarray = array();
	$outpalette = array();
        $colors = 6;

        ## Process image
        list($width, $height) = getimagesize($filename);
	$oldimage = imagecreatefromjpeg($filename);

        ## Create new image
        $new_width = 3;
        $new_height = 2;
        $newimage = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($newimage, $oldimage, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        ## Process each pixel
        for ($i = 0; $i <$new_width; $i++)  {
                for ($j = 0; $j <$new_height; $j++)  {
                        $color = imagecolorat($newimage, $i, $j);
                        array_push($colorarray, $color);
                }
        }

        ## Find the n most common colors
        $palette = array_count_values($colorarray);
        ksort($palette);
        $palette = array_slice($palette, 0, $colors, true);
        foreach ($palette AS $color => $frequency)  {
		$r = ($color >> 16) & 0xFF;
		$g = ($color >> 8) & 0xFF;
		$b = $color & 0xFF;
                array_push($outpalette, "$r,$g,$b");
        }

	## Export the formatted colors
	return "|" . implode("|", $outpalette) . "|";
}

function rgb2hex($arrColors = null) {
	if (!is_array($arrColors)) { return "ERR: Invalid input, expecting an array of colors"; }
	if (count($arrColors) < 3) { return "ERR: Invalid input, array too small (3)"; }
        
	array_splice($arrColors, 3);
        
	for ($x = 0; $x < count($arrColors); $x++) {
            if (strlen($arrColors[$x]) < 1) {
                return "ERR: One or more empty values found, expecting array with 3 values";
            }
            
            elseif (eregi("[^0-9]", $arrColors[$x])) {
                return "ERR: One or more non-numeric values found.";
            }
            
            else {
                if ((intval($arrColors[$x]) < 0) || (intval($arrColors[$x]) > 255)) {
                    return "ERR: Range mismatch in one or more values (0-255)";
                }
                
                else {
                    $arrColors[$x] = strtoupper(str_pad(dechex($arrColors[$x]), 2, 0, STR_PAD_LEFT));
                }
            }
	}
        
	return implode("", $arrColors);
}









## Function to check for special episodes for all season page that air after a season
function checkspecialallafter($epnum, $specials, $seriesid,$seasonnumber, $seasonid) {
if ($specials)  {
		## Get the number of episodes
  foreach ($specials as $specialepisode) {
	foreach ($specialepisode as $specialdata) {
	  if (key($specialepisode) == $seasonnumber) {
 	  	$explode = explode("'..'", $specialdata);
	$query	= "SELECT MAX(EpisodeNumber) AS maximum FROM tvepisodes WHERE seasonid=$seasonid";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	$episodes= mysql_fetch_object($result);
//		print_r ($explode);echo"<br>";
 		if ($episodes->maximum == $epnum)  {
	  	  if ($seasonnumber == $explode[5]) {
	  		print "<tr><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">Special</a></td><td class=\"special\"><a href=\"/index.php?tab=episode&seriesid=$seriesid&seasonid=$explode[2]&id=$explode[0]$urllang\">$explode[1]</a></td><td class=\"special\">$explode[3]</td><td class=\"special\">";
	  		if ($explode[4]) {
				print "<img src=\"$baseurl/images/checkmark.png\" width=10 height=10>";
		  	}
		  	else {echo "&nbsp;";}
	  		print "</td></tr>\n";
	  	  }
 		}
	  }
  	}
  }
}
}

		if ($apache_type == "unix")
			{
		  $domain = $_SERVER['HTTP_HOST'];
		  // find out the path to the current file:
		  $path = $_SERVER['SCRIPT_NAME'];
		  // find out the QueryString:
		  $queryString = $_SERVER['QUERY_STRING'];
		  // put it all together:
		  $fullurl = "http://" . $domain . $path . "?" . $queryString;
		  //return "http://" . $domain . $path . $queryString;
		  //echo $url;
			}
		elseif ($apache_type == "windows")
			{
		  $fullurl = $_SERVER[URL];
		  //echo $url;
			}
?>
