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
$type		= 'game';
$actualtime	= time();

## Time is a required field
if ($time == "") {
    print "<Error>Time is required</Error>\n";
    exit;
} elseif ($time > 2592000) {
    print "<Error>Time is greater than 30 days. Please do a full download of all series.</Error>\n";
    exit;
} else {
    print "<Items>\n";
    print "<Time>$actualtime</Time>\n";
}

$time       = $actualtime - $time;

$query = "SELECT id FROM games WHERE lastupdated>=$time LIMIT 1000";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($db = mysql_fetch_object($result)) {
    print "<Game>$db->id</Game>\n";
}

?>
</Items>
