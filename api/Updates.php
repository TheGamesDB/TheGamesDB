<?php
    ## Interface that returns all updates since a given time
	## Parameters:
	##   $_REQUEST["time"]
	##   $_REQUEST["type"]			[series(default)|episode|all]
	##
	## Returns:
	##   XML items for each series or episode that was updated since "time"

	## Include functions, db connection, etc
	include("include.php");
?>
<?php
	## Prepare the search string
	$time		= $_REQUEST["time"];
	$type		= $_REQUEST["type"];
	$actualtime	= time();


	## If type is none, just return the time
	if ($type == "none")  {
		print "<Items>\n";
		print "<Time>" . time() . "</Time>\n";
		print "</Items>\n";
		exit;
	}


	## Time is a required field
	if ($time == "")  {
		print "<Error>Time is required</Error>\n";
		exit;
	}
	## This interface only allows lookups within the last 30 days
	elseif ($time - $actualtime > 2592000)  {
		print "<Error>Time is greater than 30 days. Please do a full download of all series.</Error>\n";
		exit;
	}
	## Type can only be series, episode, all, or blank
	elseif ($type != "game" && $type != "all" && $type != "")  {
		print "<Error>Unknown value for type</Error>\n";
		exit;
	}
	## No errors, so we continue
	else  {
		print "<Items>\n";
		print "<Time>" . time() . "</Time>\n";
	}


	## Query for series (if desired)
	if ($type == "game" || $type == "all" || $type == "")  {
		$query = "SELECT id FROM games WHERE lastupdated>=$time LIMIT 1000";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($db = mysql_fetch_object($result))  {
			print "<Game>$db->id</Game>\n";
		}
	}
    
?>
</Items>
