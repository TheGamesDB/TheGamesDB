<?php
	###############################
	## Settings
	###############################
	$src	= 'http://image.com.com/tv/images/content_headers/program/' . $id . '.jpg';

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
		print "ERROR";
		exit;
	}

	## Create the colors
	if ($color == 'black')  {
		$imgcolor	= imagecolorallocate($image, 0, 0, 0);
	}
	else if ($color == 'custom')  {
		$imgcolor	= imagecolorallocate($image, $color_r, $color_g, $color_b);
	}
	else  {
		$imgcolor	= imagecolorallocate($image, 255, 255, 255);	
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