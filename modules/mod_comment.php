<?php

	/*
	 * Comments Functions
	 */

	function check_input($value)
	{
	// Stripslashes
	if (get_magic_quotes_gpc())
	  {
	  $value = stripslashes($value);
	  }
	// Quote if not a number
	if (!is_numeric($value))
	  {
	  $value = "'" . mysql_real_escape_string($value) . "'";
	  }
	return $value;
	}
	 
	if (isset($function) && $function == 'Add Game Comment') {
		$comment = htmlspecialchars($comment, ENT_QUOTES);
		$userid = check_input($userid);
		$gameid = check_input($gameid);
		$commentQuery = mysql_query(" INSERT INTO comments (userid, gameid, comment, timestamp) VALUES ('$userid', '$gameid', '$comment', FROM_UNIXTIME($time)) ") or die('Query failed: ' . mysql_error());
	}

	if (isset($function) && $function == 'Delete Game Comment') {
		$commentQuery = mysql_query(" DELETE FROM comments WHERE id = $commentid ") or die('Query failed: ' . mysql_error());
	}

?>