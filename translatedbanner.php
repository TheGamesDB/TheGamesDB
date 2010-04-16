<?php
	###############################
	## Image Functions
	###############################



	###############################
	## Settings
	###############################
	$src	= 'banners/' . $filename;

	## Who wouldn't set the text?!
	if (!isset($text))  {	$text = 'Good job, fool';  }

	## Obvious font settings
	if (!isset($fontface))  {  $fontface = 'arial.ttf';	}
	if (!isset($fontsize))  {  $fontsize = 22; }

	## Image crop size
	if (!isset($height))  {  $height = 140;	}
	if (!isset($width))  {  $width = 758;  }

	## Text offsets
	if (!isset($xpos))  {  $xpos = 26;  }
	if (!isset($ypos))  {  $ypos = 78;  }


	###############################
	## Create the image
	###############################
	$image	= imagecreatefromjpeg ($src);
	if ($image == '')  {
		$image	= imagecreatefrompng ($src);
	}
	if ($image == '')  {
		print "ERROR";
		exit;
	}

	## Create the colors
	switch ($color) {
		case 'custom':
			$imgcolor = imagecolorallocate($image, $color_r, $color_g, $color_b);
			break;
		case 'black':
			$imgcolor = imagecolorallocate($image, 0, 0, 0);
			break;
		case 'light gray':
			$imgcolor = imagecolorallocate($image, 192, 192, 192);
			break;
		case 'medium gray':
			$imgcolor = imagecolorallocate($image, 128, 128, 128);
			break;
		case 'dark gray':
			$imgcolor = imagecolorallocate($image, 64, 64, 64);
			break;
		default:
			$imgcolor = imagecolorallocate($image, 255, 255, 255);	
	}


	###############################
	## Crop it to the right size
	###############################
	$crop = imagecreatetruecolor($width, $height);
	imagecopy ( $crop, $image, 0, 0, 0, 0, $width, $height );


	###############################
	## Place the text
	###############################
	imagettftext($crop, $fontsize, 0, $xpos, $ypos, $imgcolor, $fontface, $text);


	###############################
	## Print the image
	###############################
	header ("content-type: image/jpeg");
	imagejpeg ($crop);


	###############################
	## Clean up memory
	###############################
	imagedestroy ($image);
	imagedestroy ($crop);
?>