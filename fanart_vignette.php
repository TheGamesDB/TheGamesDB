<?php
	vignette($filename);

function vignette($sourcefile) {

	## Get the image sizes and read it into an image object
	$sourcefile_id	= imagecreatefromjpeg($sourcefile);
	$width		= imageSX($sourcefile_id);
	$height		= imageSY($sourcefile_id);

	## Settings
	$vignette_file	= "images/vignette" . $height . ".png";
	$scale		= 0.75;

	## Create a new destination image object
	$result_id	= imagecreatetruecolor($width, $height);
	$black		= imagecolorallocate($result_id, 0, 0, 0);
	imagefill($result_id, 0, 0, $black);

	## Read the vignette into an image object
	$vignette_id	= imagecreatefrompng($vignette_file);

	## Make sure they merge correctly
	imageAlphaBlending($vignette_id, false);
	imageSaveAlpha($vignette_id, true);

	## Create the image
	imagecopy($sourcefile_id, $vignette_id, 0, 0, 0, 0, $width, $height);

	## Copy our source image resized into the destination object
	imagecopyresampled($result_id, $sourcefile_id, 0, 0, 0, 0, $width * $scale, $height * $scale, $width, $height);

	## Return the JPG
	header("Content-type: image/jpg");
	imagejpeg ($result_id, "", 90);

	## Wrap it up
	imagedestroy($sourcefile_id);
	imagedestroy($vignette_id);
	imagedestroy($result_id);
}
?>
