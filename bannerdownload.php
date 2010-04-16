<?php
	$url = $_SERVER["QUERY_STRING"];
	$url = preg_replace("/\Afilename=/", "", $_SERVER["QUERY_STRING"]);

	## Print the header info
	header("Cache-Control: public, must-revalidate");
	header("Pragma: hack");
	header("Content-Type: image/jpeg");
	header('Content-Disposition: attachment; filename="folder.jpg"');
	header("Content-Transfer-Encoding: binary\n");

	## Output the file
	print file_get_contents($url);      
?>