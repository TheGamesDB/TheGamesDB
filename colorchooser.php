<?php
	## Default functions
	include("include.php");
	$displaywidth = 640;
	$displayheight = 360;
	$colornames = array("Light Accent Color", "Dark Accent Color", "Neutral Midtone Color");


	## On submit
	if ($function)  {
		$id = mysql_real_escape_string($id);
		$colorsubmit = implode("|", array("", $color1, $color2, $color3, ""));
		$colorsubmit = mysql_real_escape_string($colorsubmit);
		$query = "UPDATE banners SET artistcolors='$colorsubmit' WHERE id=$id";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		##print "<script language=\"JavaScript\">\nwindow.opener.location.reload()\nwindow.close()\n</script>\n";
		print "<script language=\"JavaScript\">\nwindow.opener.location=window.opener.location\nwindow.close()\n</script>\n";
		exit;
	}



	## Get this banner info from the database
	$id = mysql_real_escape_string($id);
	$query	= "SELECT * FROM banners WHERE id=$id";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$banner = mysql_fetch_object($result);
	$colors = explode("|", $banner->artistcolors);


	## Override with colors in hidden inputs
	for ($i = 1; $i <= 3; $i++)  {
		$varname = "color" . $i;
		if ($$varname)  {
			$colors[$i] = $$varname;
		}
	}


	## Handle a new color selection
	if ($coordinates_x)  {

		## Calculate our coordinate multiplier
		$resolution = explode("x", $banner->resolution);
		$width = $resolution[0];
		$height = $resolution[1];
		$multiplier = $height / $displayheight;

		## Get the image and find the color at our pixels
		$image = imagecreatefromjpeg("banners/$banner->filename");

		## Fix our parameter
		$rgb = imagecolorat($image, $coordinates_x * $multiplier, $coordinates_y * $multiplier);

		## Store in the proper variable
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		$colorstring = implode(",", array($r, $g, $b));
		$colors[$targetcolor] = $colorstring;
	}


	## Don't allow a targetcolor of 4
	if ($targetcolor == 3)  {
		$targetcolor = 0;
	}
?>

<html>
<head>
	<title>Color Chooser</title>
	<link rel="stylesheet" type="text/css" href="http://thetvdb.com/default.css">
	<style>
		BODY {
			background-color: #001D2D;
			color: #FFF;
			font: 9pt Arial, san-serif;
		}
		A, A:link, A:visited  {
		        color: #B6D415;
		        text-decoration: none;
		}
		A:hover  {
        		color: #DFEF86;
		}
		TD  {
			color: #FFF;
			font: 10pt Arial, san-serif;
			vertical-align: top;
		}
		INPUT {
			font: 9pt Arial, san-serif;
			border: none;
		}
		IMG  {
			border: 1px solid white;
		}
		.image  {
			cursor: crosshair;
		}
		.colorbox  {
			border: 3px solid white;
			width: 180px;
			height: 40px;
		}
		FORM  {
			margin: 0px;
			padding: 0px;
		}
		FORM INPUT  {
			color: #001D2D;
			background-color: #B6D415;
			font: bold 14pt Arial, san-serif;
			border: none;
			text-align: center;
			border: 1px solid white;
		}
	</style>
	<script LANGUAGE="JavaScript" type="text/javascript">
	<!--
		function SelectColor(id) {
			document.getElementById("colorbox1").style.border="3px solid white";
			document.getElementById("colorbox2").style.border="3px solid white";
			document.getElementById("colorbox3").style.border="3px solid white";
			var objectname = eval("document.getElementById('colorbox" + id + "')");
			objectname.style.border = '3px solid #B6D415';
			document.colorform.targetcolor.value = id;
		}

	-->
	</script>

</head>
<body onLoad="SelectColor(<?=$targetcolor+1?>)">
	<form action="colorchooser.php" method="GET" name="colorform">
	<input type="hidden" name="id" value="<?=$id?>">
	<input type="hidden" name="targetcolor" value="1">

	<div class="section">
	<h1>Color Chooser</h1>

	<table cellspacing="2" cellpadding="0" border="0" width="660">
	<tr>
		<td colspan="4" style="padding-bottom: 20px">
			To use the color chooser, click the box of the color you wish to change and then click on the new color in the image.  Once all 3 colors have been selected, you may save your changes.
		</td>
	</tr>
	<tr>
		<td>Light Accent Color</td>
		<td>Dark Accent Color</td>
		<td>Neutral Midtone Color</td>
	</tr>
	<tr>
		<?php	## Loop through 3 colors
			$completed = 1;
			for ($i = 1; $i <= 3; $i++)  {
				print "<td>\n";
				$rgbcolor = "";
				$hexcolor = "";
				$style = "";
				if ($colors[$i])  {
					$colorarray=split(",",$colors[$i]);
					$rgbcolor = $$colorvar;
					$hexcolor = rgb2hex($colorarray);
					print "<div class=\"colorbox\" id=\"colorbox$i\" name=\"colorbox$i\" style=\"background-color:#$hexcolor\" OnClick=\"SelectColor($i)\"></div>\n";
				}
				else  {
					print "<div class=\"colorbox\" id=\"colorbox$i\" name=\"colorbox$i\" OnClick=\"SelectColor($i)\"></div>\n";
					$completed = 0;
				}
				print "<input type=\"hidden\" name=\"color$i\" value=\"$colors[$i]\">\n";
				print "</td>\n";
			}
		?>
	</tr>
	<tr>
		<td colspan="4" style="padding-top: 20px">
				<input type="image" class="image" src="banners/<?=$banner->filename?>" name="coordinates" width="<?=$displaywidth?>" height="<?=$displayheight?>">
		</td>
	</tr>
	<?php	## Only display the submit if they've selected 3 colors
		if ($completed == 1)  {
	?>
		<tr>
			<td colspan="4" style="padding-top: 10px">
				<input type="submit" name="function" value="Accept These Colors">
			</td>
		</tr>
	<?php
		}
	?>
	</table>

	</div>
	</form>
</body>
