<?php
## Description:
##   Interface that allows clients to get a list of all platforms that exist

## Paramenters:
##   [NONE]

## Returns:
##   XML items holding the id, name & alias of each platform


## Include functions, db connection, etc
include("include.php");


## Query for main Platforms list
$query = "SELECT id FROM platforms ORDER BY name";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());


## Output Platforms XML List
print "<Data>\n";
print "\t<basePlatformUrl>$baseurl/platform/</basePlatformUrl>\n";
print "\t<Platforms>\n";

while ($obj = mysql_fetch_object($result)) {
	## Start XML Item
    print "\t\t<Platform>\n";

    ## Query and display basic Platform info
    $subquery = "SELECT p.id, p.name, p.alias FROM platforms AS p WHERE p.id={$obj->id}";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            print "\t\t\t<$key>$value</$key>\n";
        }
    }

    ## End XML item
    print "\t\t</Platform>\n";
}

print "\t</Platforms>\n";
print "</Data>";
?>