<?php
## Interface that allows clients to get
## seriesid using name
## Parameters:
##   $_REQUEST["name"]
##   $_REQUEST["language"]		(optional)
##   $_REQUEST["user"]			(optional... overrides language setting)
##
## Returns:
##   XML items holding the games that matches the name string

## Include functions, db connection, etc
include("include.php");

## Prepare the search string
$name = addslashes(stripslashes(stripslashes($_REQUEST["name"])));
$name = str_replace(array(' - ', '-'), '%', $name);

//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];

if (empty($name)) {
    print "<Error>A name is required</Error>\n";
    exit;
} else {
    if (isset($name)) {
        if (strpos($name, ", The")) {
            $name = "The " . substr($name, 0, strpos($name, ", The"));
        }
    }
    print "<Data>\n";
}

$query;
if (isset($name) && !empty($name)) {
    $query = "SELECT id FROM games WHERE GameTitle LIKE '%$name%' ORDER BY GameTitle ASC";
}
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    // Base Info
    $subquery = "SELECT id, GameTitle, ReleaseDate, Platform FROM games WHERE id={$obj->id}";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            print "<$key>$value</$key>\n";
        }
    }

    ## End XML item
    print "</Game>\n";
}
?>
</Data>
