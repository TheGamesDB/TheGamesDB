<?php
/*
=============================================================================================
Script Name: thumbnail.php
Version: 1.1
Author: Ian Anderson
Date: November 2002
Acknowledge: Teekai - http://www.teekai.info/v8/home.php (see the original script at
http://www.hotscripts.com/Detailed/18727.html on which this script is based).

This script is a self-contained Thumbnail Image Generator. It should be called as follows ...

<img src="/path/to/thumbnail.php?gd=N&src=/path/to/image.EXT&maxw=NNN" />

where N = the GD library version (supported values are 1 and 2)
EXT = the file extension of the image file
(supported values are gif (if gd = 2), jpg and png)
NNN = the desired maximum width of the thumbnail

If the actual image is narrower than the desired maximum width then the original image size
is used for the thumbnail copy.

This script checks for the following errors and generates an error JPEG image accordingly ...

GD version selected neither 1 nor 2;
Image create functions not supported;
Image file not found at the selected location;
GD version 2 functions not supported on the running version of PHP.

This script is available for use as freeware subject to the retention of the preceding
information and acknowledgements in any copy or modification that is made to this code.
=============================================================================================
*/

function ErrorImage ($text) {
global $maxw;
$len = strlen ($text);
if ($maxw < 154) $errw = 154;
$errh = 30;
$chrlen = intval (5.9 * $len);
$offset = intval (($errw - $chrlen) / 2);
$im = imagecreate ($errw, $errh); /* Create a blank image */
$bgc = imagecolorallocate ($im, 153, 63, 63);
$tc = imagecolorallocate ($im, 255, 255, 255);
imagefilledrectangle ($im, 0, 0, $errw, $errh, $bgc);
imagestring ($im, 2, $offset, 7, $text, $tc);
header ("Content-type: image/jpeg");
imagejpeg ($im);
imagedestroy ($im);
exit;
}

function thumbnail ($gdver, $src, $maxw=190) {

$gdarr = array (1,2);
for ($i=0; $i<count($gdarr); $i++) {
if ($gdver != $gdarr[$i]) $test.="|";
}
$exp = explode ("|", $test);
if (count ($exp) == 3) {
ErrorImage ("Incorrect GD version!");
}

if (!function_exists ("imagecreate") || !function_exists ("imagecreatetruecolor")) {
ErrorImage ("No image create functions!");
}

$size = @getimagesize ($src);
if (!$size) {
ErrorImage ("Image File Not Found!");
} else {

if ($size[0] > $maxw) {
$newx = intval ($maxw);
$newy = intval ($size[1] * ($maxw / $size[0]));
} else {
$newx = $size[0];
$newy = $size[1];
}

if ($gdver == 1) {
$destimg = imagecreate ($newx, $newy );
} else {
$destimg = @imagecreatetruecolor ($newx, $newy ) or die (ErrorImage ("Cannot use GD2 here!"));
}

if ($size[2] == 1) {
if (!function_exists ("imagecreatefromgif")) {
ErrorImage ("Cannot Handle GIF Format!");
} else {
$sourceimg = imagecreatefromgif ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/gif");
imagegif ($destimg);
}
}
elseif ($size[2]==2) {
$sourceimg = imagecreatefromjpeg ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/jpeg");
imagejpeg ($destimg,"",80);
}
elseif ($size[2] == 3) {
$sourceimg = imagecreatefrompng ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/png");
imagepng ($destimg);
}
else {
ErrorImage ("Image Type Not Handled!");
}
}

imagedestroy ($destimg);
imagedestroy ($sourceimg);
}

thumbnail ($_GET["gd"], $_GET["src"], $_GET["maxw"]);
?>
