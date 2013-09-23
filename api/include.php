<?php
	global $baseurl;
	global $db_user;
	global $db_password;
	global $db_database;
	global $db_server;
	global $apache_type;
	global $language;
	include("../config.php");

	## Print the header
	header("Content-Type: text/xml; charset=utf-8");
	print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

	## Set the default language
	if (!isset($language))  {
		$language = 7;  ## English
	}

	## Connect to the database
	$database = mysql_connect($db_server, $db_user, $db_password) or die('Could not connect: ' . mysql_error());
	mysql_select_db($db_database) or die('Could not select database');
	$result = mysql_query("SET NAMES 'utf8'") or die('Query failed: ' . mysql_error());

	function shutdown()
	{
	    global $database;
	    mysql_close($database);
	}
	
	register_shutdown_function('shutdown');

	## Prevent SQL injection attacks
	$_REQUEST  = array_map('mysql_real_escape_string', $_REQUEST);

	## Format the output XML properly
	function xmlformat ($value, $key)  {

		## Format value
		#$value = utf8_encode($value);
		#$value = str_replace('<', '&lt;', $value);
		#$value = str_replace('<', '&gt;', $value);
		$value = str_replace('&', '&amp;', $value); ## This Line Breaks UTF-8 Encoding
		#$value = str_replace('&amp;#', '&#', $value); ## This Line Fixes UTF-8 Encoding added May 29, 2007
		$value = preg_replace('/\0/', '', $value);
		return $value;
	}
	
	function clean($string)
	{
		// Replace other special chars
		$specialCharacters = array(
		'#' => '',
		'$' => '',
		'%' => '',
		'&' => '',
		'@' => '',
		'.' => '',
		'€' => '',
		'+' => '',
		'=' => '',
		'-' => '',
		'§' => '',
		'\\' => '',
		'/' => '',
		);

		while (list($character, $replacement) = each($specialCharacters)) {
		$string = str_replace($character, '-' . $replacement . '-', $string);
		}

		$string = strtr($string,
		"ÀÁÂÃÄÅáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
		"AAAAAAaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"
		);

		// Remove all remaining other unknown characters
		$string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);
		$string = preg_replace('/^[\-]+/', '', $string);
		$string = preg_replace('/[\-]+$/', '', $string);
		$string = preg_replace('/[\-]{2,}/', ' ', $string);
		$string = str_replace('   ', ' ', $string);
		$string = str_replace('  ', ' ', $string);

		return $string;
	}
?>

