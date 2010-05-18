<?php	
## Interface that allows clients to get
## seriesid using name
## Parameters:
##   $_REQUEST["name"]
##   $_REQUEST["language"]		(optional)
##   $_REQUEST["user"]			(optional... overrides language setting)
##
## Returns:
##   XML item holding the series id that matches the name

## Include functions, db connection, etc
include("include.php");

## Prepare the search string
$name           = $_REQUEST["name"];
//$language		= $_REQUEST["language"];
$user			= $_REQUEST["user"];
if ($name == "") {
    print "<Error>name is required</Error>\n";
    exit;
}
else {
    if (strpos($name,", The")) {
        $name = "The ".substr($name,0,strpos($name,", The"));
    }
    if (strpos($name,"'")) {
        $name = str_replace("\'","",$name);
    } ##To be removed if someone can figure out hwo to do this in sphinx
    print "<Data>\n";
}

## Run the query
$query = "SELECT * FROM games WHERE MATCH(GameTitle) AGAINST('$name')";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

while ($obj = mysql_fetch_object($result)) {

    ## Get base information
    $subquery = "SELECT id, GameTitle, ReleaseDate FROM games WHERE id={$obj->id}";
    $subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $db = mysql_fetch_object($subresult);

    ## Get top banner
    $subquery = "SELECT filename FROM banners WHERE keytype='series' AND keyvalue=$obj->id ORDER BY (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) DESC,RAND() LIMIT 1";
    $subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    if ($subdb = mysql_fetch_object($subresult)) {
        $db->banner = $subdb->filename;
    }

    ## Get additional information (overview)
    $subquery = "SELECT translation FROM translation_seriesoverview WHERE seriesid=$obj->id AND languageid=1 AND translation IS NOT NULL ORDER BY languageid DESC LIMIT 2";
    $subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    if ($subdb = mysql_fetch_object($subresult)) {
        $db->Overview = $subdb->translation;
    }

    ## Start XML item
    print "<Game>\n";

    ## Loop through each field for this item
    foreach ($db as $key => $value) {

        ## Prepare the string for output
        $value = xmlformat($value, $key);

        ## Print the string
        print "<$key>$value</$key>\n";

    }

    ## End XML item
    print "</Game>\n";
}
?>
</Data>
