<?php
## Description:
##   Interface that allows clients to get info for a single Platform

## Parameters:
##   $_REQUEST["id"]

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
	
	function processFanart($platformID)
	{
		## Select all fanart rows for the requested game id
		$faResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $platformID AND keytype = 'platform-fanart' ORDER BY filename ASC ");
		
		## Process each fanart row incrementally
		while($faRow = mysql_fetch_assoc($faResult))
		{
			## Construct file names
			$faOriginal = $faRow['filename'];
			$faThumb = str_replace("fanart", "fanart/thumb", $faRow['filename']);
		
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
				print "\t\t<fanart>\n";
					print "\t\t\t<original width=\"$faWidth\" height=\"$faHeight\">$faOriginal</original>\n";
					print "\t\t\t<thumb>$faThumb</thumb>\n";
				print "\t\t</fanart>\n";
			}
		}
	}
	
	function processBoxart($platformID)
	{
		## Select all boxart rows for the requested game id
		$baResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $platformID AND keytype = 'platform-boxart' ORDER BY filename ASC ");
		
		## Process each boxart row incrementally
		while($baRow = mysql_fetch_assoc($baResult))
		{
			## Construct file names
			$baOriginal = $baRow['filename'];
			
			$type  = (preg_match('/front/', $baOriginal)) ? 'front' : 'back';
		
			## Check to see if the original boxart file actually exists before attempting to process 
			if(file_exists("../banners/$baOriginal"))
			{
				## Get boxart image dimensions
				list($image_width, $image_height, $image_type, $image_attr) = getimagesize("../banners/$baOriginal");
				$baWidth = $image_width;
				$baHeight = $image_height;
				
				## Output Boxart XML Branch
				echo "\t\t<boxart side=\"$type\" width=\"$baWidth\" height=\"$baHeight\">$baOriginal</boxart>\n";
			}
		}
	}
	
	function processBanner($platformID)
	{
		## Select all boxart rows for the requested game id
		$banResult = mysql_query(" SELECT filename FROM banners WHERE keyvalue = $platformID AND keytype = 'platform-banner' ORDER BY filename ASC ");
		
		## Process each boxart row incrementally
		while($banRow = mysql_fetch_assoc($banResult))
		{
			## Construct file names
			$banOriginal = $banRow['filename'];
		
			## Check to see if the original boxart file actually exists before attempting to process 
			if(file_exists("../banners/$banOriginal"))
			{			
				## Output Boxart XML Branch
				echo "\t\t<banner width=\"760\" height=\"140\">$banOriginal</banner>";
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
	$query = "SELECT id FROM platforms WHERE id=$id";
}

$result = mysql_query($query) or die('Query failed: ' . mysql_error());
print "<Data>\n<baseImgUrl>http://thegamesdb.net/banners/</baseImgUrl>\n";

while ($obj = mysql_fetch_object($result)) {
    print "<Platform>\n";

    // Base Info
    $subquery = "SELECT p.id, p.name as Platform, p.console, p.controller, p.overview, p.developer, p.manufacturer, p.cpu, p.memory, p.graphics, p.sound, p.display, p.media, p.maxcontrollers, p.Youtube, AVG(r.rating) as Rating FROM platforms as p LEFT JOIN ratings as r ON (p.id=r.itemid and r.itemtype='platform') WHERE p.id={$obj->id} Group By p.id";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            switch ($key) {
				case 'console':
					$console = $value;
			
				case 'controller':
					$controller = $value;
				
				case 'Youtube':
                    print "\t<$key>http://www.youtube.com/watch?v=$value</$key>\n";
					break;
					
                case 'Rating':
                    print "\t<Rating>" . (float) $value . "</Rating>\n";
                    break;
					
                default:
                    print "\t<$key>$value</$key>\n";
            }
        }
    }

    ## Process Images
	print "\t<Images>\n";
	
	processFanart($obj->id);
	processBoxart($obj->id);
	processBanner($obj->id);
	if(!empty($console)) { print "\t\t<consoleart>platform/consoleart/$console</consoleart>\n"; }
	if(!empty($controller)) { print "\t\t<controllerart>platform/controllerart/$controller</controllerart>\n"; }
	
	print "\t</Images>\n";
    

    ## End XML item
    print "</Platform>\n";
}
?>
</Data>
