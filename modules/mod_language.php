<?php

	#####################################################
	## Language stuff
	#####################################################
	## Get list of languages and store array
	global $languages;
	global $lid;
	$query = "SELECT * FROM languages ORDER BY name";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($lang = mysql_fetch_object($result)) {
		$languages[$lang->id] = $lang->name;
	}

	## Set the default language
	if (!isset($lid)) {
		if ($user->languageid) {
			$lid = $user->languageid;  ## user preferred language
		} else {
			$lid = 1;  ## English
		}
	}
	
?>