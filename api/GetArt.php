<?php

	###=============###
	###PREREQUISITES###
	###--------------------------###
	
	## Include base functions, db connection, etc
	include("include.php");
	include('../simpleimage.php');
	
	## Get requested game id from api call
	$requestedID = $_REQUEST['id'];
	
	if (empty($id) || !is_numeric($id)) {
    print "<Error>An integer formatted id is required</Error>\n";
    exit;
	}
	
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
	
	## Function to process all screenshots for the requested game id
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
	
## Function to process all fanart for the requested game id
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
	
	
	
	###===============###
	###MAIN XML OUTPUT###
	###-----------------------------###
	
	
	print "<Data>\n";
	
	
	print "<baseImgUrl>http://thegamesdb.net/banners/</baseImgUrl>\n";
	
	## Open Images XML Branch
	print "<Images>\n";
	
	processFanart($requestedID);
	processBoxart($requestedID);
	processBanner($requestedID);
	processScreenshots($requestedID);
	processClearLOGO($requestedID);
	
	## Close Images XML Branch
	print "</Images>\n";
	
	
	print "</Data>";
?>