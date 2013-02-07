<?php
## Interface that allows clients to get
## seriesid using name
## Parameters:
##   $_REQUEST["name"]
##   $_REQUEST["language"]		(optional)
##   $_REQUEST["user"]			(optional... overrides language setting)
##
## Returns:
##   XML item holding the series id that matches the name
## Include functions, db connection, etc
include("include.php");
include('../simpleimage.php');

## Prepare the search string
$name = addslashes(stripslashes(stripslashes($_REQUEST["name"])));
//$name = str_replace('-', '(.).(.)', $name);

$id = $_REQUEST['id'];
//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];

	###==============###
	###VITAL FUNCTIONS###
	###----------------------------###

	## Function to generate a fanart thumb image if does not already exist
	function makeFanartThumb($sourcefile, $targetfile) {

        ## Get the image sizes and read it into an image object
        $sourcefile_id  = imagecreatefromjpeg($sourcefile);
        $width          = imageSX($sourcefile_id);
        $height         = imageSY($sourcefile_id);

        ## Settings
        //$scale          = 0.1;
		$destWidth = 300;
		$destHeight = 169;

        ## Create a new destination image object - for scale resize replace $destWidth, $destHeight with: $width * $scale, $height * $scale
        $result_id      = imagecreatetruecolor($destWidth, $destHeight);

        ## Copy our source image resized into the destination object - for scale resize replace $destWidth, $destHeight with: $width * $scale, $height * $scale
        imagecopyresampled($result_id, $sourcefile_id, 0, 0, 0, 0, $destWidth, $destHeight, $width, $height);

        ## Return the JPG
        imagejpeg ($result_id, $targetfile, 90);

        ## Wrap it up
        imagedestroy($sourcefile_id);
        imagedestroy($result_id);
	}

	function processFanart($gameID)
	{
		## Select all fanart rows for the requested game id
		$faResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $gameID AND keytype = 'fanart' ORDER BY filename ASC ");

		## Process each fanart row incrementally
		while($faRow = mysql_fetch_assoc($faResult))
		{
			## Construct file names
			$faOriginal = $faRow['filename'];
			$faThumb = str_replace("original", "thumb", $faRow['filename']);

			## Check to see if the original fanart file actually exists before attempting to process
			if(file_exists("../banners/$faOriginal"))
			{
				## Check if thumb already exists
				if(!file_exists("../banners/$faThumb"))
				{
					## If thumb is non-existant then create it
					makeFanartThumb("../banners/$faOriginal", "../banners/$faThumb");
				}

				## Get Fanart Image Dimensions
				list($image_width, $image_height, $image_type, $image_attr) = getimagesize("../banners/$faOriginal");
				$faWidth = $image_width;
				$faHeight = $image_height;

				## Output Fanart XML Branch
				print "<fanart>\n";
					print "<original width=\"$faWidth\" height=\"$faHeight\">$faOriginal</original>\n";
					print "<thumb>$faThumb</thumb>\n";
				print "</fanart>\n";
			}
		}
	}

	function processScreenshots($gameID)
	{
		## Select all fanart rows for the requested game id
		$ssResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $gameID AND keytype = 'screenshot' ORDER BY filename ASC ");

		## Process each fanart row incrementally
		while($ssRow = mysql_fetch_assoc($ssResult))
		{
			## Construct file names
			$ssOriginal = $ssRow['filename'];
			$ssThumb = "screenshots/thumb" . str_replace("screenshots", "", $ssRow['filename']);

			## Check to see if the original fanart file actually exists before attempting to process
			if(file_exists("../banners/$ssOriginal"))
			{
				## Check if thumb already exists
				if(!file_exists("../banners/$ssThumb"))
				{
					## If thumb is non-existant then create it
				   $image = new SimpleImage();
				   $image->load("../banners/$ssOriginal");
				   $image->resizeToWidth(300);
				   $image->save("../banners/$ssThumb");
					//makeFanartThumb("../banners/$ssOriginal", "../banners/$ssThumb");
				}

				## Get Fanart Image Dimensions
				list($image_width, $image_height, $image_type, $image_attr) = getimagesize("../banners/$ssOriginal");
				$ssWidth = $image_width;
				$ssHeight = $image_height;

				## Output Fanart XML Branch
				print "<screenshot>\n";
					print "<original width=\"$ssWidth\" height=\"$ssHeight\">$ssOriginal</original>\n";
					print "<thumb>$ssThumb</thumb>\n";
				print "</screenshot>\n";
			}
		}
	}

	function processBoxart($gameID)
	{
		## Select all boxart rows for the requested game id
		$baResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $gameID AND keytype = 'boxart' ORDER BY filename ASC ");

		## Process each boxart row incrementally
		while($baRow = mysql_fetch_assoc($baResult))
		{
			## Construct file names
			$baOriginal = $baRow['filename'];
			$baThumb = "boxart/thumb" . str_replace("boxart", "", $baRow['filename']);

			$type  = (preg_match('/front/', $baOriginal)) ? 'front' : 'back';

			## Check to see if the original boxart file actually exists before attempting to process
			if(file_exists("../banners/$baOriginal"))
			{
			    ## Check if thumb already exists
			    if(!file_exists("../banners/$baThumb"))
			    {
			    ## If thumb is non-existant then create it
			        $image = new SimpleImage();
			        $image->load("../banners/$baOriginal");
			        $image->resizeToWidth(300);
			        $image->save("../banners/$baThumb");
			    }

				## Get boxart image dimensions
				list($image_width, $image_height, $image_type, $image_attr) = getimagesize("../banners/$baOriginal");
				$baWidth = $image_width;
				$baHeight = $image_height;

				## Output Boxart XML Branch
				echo "<boxart side=\"$type\" width=\"$baWidth\" height=\"$baHeight\" thumb=\"$baThumb\">$baOriginal</boxart>\n";
			}
		}
	}

	function processBanner($gameID)
	{
		## Select all boxart rows for the requested game id
		$banResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $gameID AND keytype = 'series' ORDER BY filename ASC ");

		## Process each boxart row incrementally
		while($banRow = mysql_fetch_assoc($banResult))
		{
			## Construct file names
			$banOriginal = $banRow['filename'];

			## Check to see if the original boxart file actually exists before attempting to process
			if(file_exists("../banners/$banOriginal"))
			{
				## Output Boxart XML Branch
				echo "<banner width=\"760\" height=\"140\">$banOriginal</banner>";
			}
		}
	}

	function processClearLOGO($gameID)
	{
		## Select all boxart rows for the requested game id
		$clResult = mysql_query(" SELECT filename, resolution FROM banners WHERE keyvalue = $gameID AND keytype = 'clearlogo' LIMIT 1 ");

		## Process each boxart row incrementally
		while($clRow = mysql_fetch_assoc($clResult))
		{
			## Construct file names
			$clOriginal = $clRow['filename'];
			$clResolution = $clRow['resolution'];

			$clResolution = explode("x", $clResolution, 2);

			## Check to see if the original boxart file actually exists before attempting to process
			if(file_exists("../banners/$clOriginal"))
			{
				## Output Boxart XML Branch
				echo "<clearlogo width=\"$clResolution[0]\" height=\"$clResolution[1]\">$clOriginal</clearlogo>";
			}
		}
	}

if (empty($name) && empty($id)) {
    print "<Error>A name or id is required</Error>\n";
    exit;
} else {
    if (isset($name)) {
        if (strpos($name, ", The")) {
            $name = "The " . substr($name, 0, strpos($name, ", The"));
        }
    }
    if (isset($id) && !is_numeric($id)) {
        print "<Error>An ID must be an int</Error>\n";
        exit;
    } else {
        $id = (int) $id;
    }
}

$query;
if (isset($id) && !empty($id))
{
	$query = "SELECT id FROM games WHERE id=$id";
}
else
{
	if(isset($platform) && !empty($platform))
	{
		$platformResult = mysql_query(" SELECT id FROM platforms WHERE name = '$platform' LIMIT 1 ");
		if(mysql_num_rows($platformResult) != 0)
		{
			$platformRow = mysql_fetch_assoc($platformResult);
			$platformId = $platformRow['id'];

			$arr = array();
			preg_match('/[0-9]+/', $name, $arr);
			$query = "SELECT id FROM games WHERE MATCH(GameTitle) AGAINST ('$name')";
			foreach($arr as $numeric)
			{
					$query .= " AND GameTitle LIKE '%$numeric%'";
			}

			$query .= " AND Platform = '$platformId'";
		}
		else
		{
			print "<Error>The specified platform was not valid.</Error>\n";
			exit;
		}
	}
	else
	{
		$arr = array();
		preg_match('/[0-9]+/', $name, $arr);
		$query = "SELECT id FROM games WHERE MATCH(GameTitle) AGAINST ('$name')";
		foreach($arr as $numeric)
		{
				$query .= " AND GameTitle LIKE '%$numeric%'";
		}
	}
}
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
print "<Data>\n<baseImgUrl>http://thegamesdb.net/banners/</baseImgUrl>\n";

while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    // Base Info
    $subquery = "SELECT g.id, g.GameTitle, g.Alternates, p.id as PlatformId, p.name as Platform, g.ReleaseDate, g.Overview, g.Rating as ESRB, g.Genre, g.Players, g.coop as 'Co-op', g.Youtube, g.Publisher, g.Developer, g.Actors, AVG(r.rating) as Rating FROM games as g LEFT JOIN ratings as r ON (g.id=r.itemid and r.itemtype='game'), platforms as p WHERE g.id={$obj->id} AND p.id = g.platform Group By g.id";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            switch ($key) {
                case 'Genre':
                    echo '<Genres>';
                    $genres = explode('|', $value);
                    foreach ($genres as $genre) {
                        if (!empty($genre)) {
                            echo '<genre>' . $genre . '</genre>';
                        }
                    }
                    echo '</Genres>';
                    break;

				case 'Alternates':
                    echo '<AlternateTitles>';
                    $alternates = explode(',', $value);
                    foreach ($alternates as $alternate) {
                        if (!empty($alternate)) {
                            echo '<title>' . $alternate . '</title>';
                        }
                    }
                    echo '</AlternateTitles>';
                    break;

				case'Youtube':
                    print "<$key>http://www.youtube.com/watch?v=$value</$key>\n";
					break;

                case 'Rating':
                    print "<Rating>" . (float) $value . "</Rating>";
                    break;

				case 'Players':
					if($value == 4) {
						print "<$key>4+</$key>";
					}
					else {
						print "<$key>" . $value . "</$key>";
					}
                    break;

                default:
                    print "<$key>$value</$key>\n";
            }
        }
    }

    ## Process Images
	print "<Images>\n";

	processFanart($obj->id);
	processBoxart($obj->id);
	processBanner($obj->id);
	processScreenshots($obj->id);
	processClearLOGO($obj->id);

	print "</Images>\n";


    ## End XML item
    print "</Game>\n";
}
?>
</Data>
