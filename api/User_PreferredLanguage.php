<?php	## Interface that returns the preferred langauge from an account identifier
	## Parameters:
	##   $_REQUEST["accountid"]
	##
	## Returns:
	##   XML item holding the preferred language for the user.  Returns 7 if not available.

	## Include functions, db connection, etc
	include("include.php");
?>
<?php
	## Prepare the search string
	$accountid		= $_REQUEST["accountid"];
	if ($accountid == "")  {
		print "<Error>accountid is required</Error>\n";
		exit;
	}
	else  {
		print "<Languages>\n";
	}


	## Run the query
	$query = "SELECT name, abbreviation, id FROM languages WHERE id=(SELECT languageid FROM users WHERE uniqueid='$accountid' LIMIT 1) OR id=7 ORDER BY id DESC LIMIT 1";
	$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($db = mysql_fetch_object($result))  {

		## Start XML item
		print "<Language>\n";

		## Loop through each field for this item
		foreach ($db as $key => $value)  {
			## Prepare the string for output
			$value = xmlformat($value, $key);

			## Print the string
			print "<$key>$value</$key>\n";

		}

		## End XML item
		print "</Language>\n";
	}
?>
</Languages>
