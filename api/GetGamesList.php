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
//$name = str_replace(array(' - ', '-'), '%', $name);
$platform = $_REQUEST["platform"];

//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];

if (empty($name)) {
    print "<Error>A name is required</Error>\n";
    exit;
}

$query;
if (isset($name) && !empty($name))
{
	$query = "SELECT *, ( MATCH (GameTitle) AGAINST ('$name') OR GameTitle SOUNDS LIKE '$name' ) AS MatchValueBoolean, MATCH (GameTitle) AGAINST ('$name') AS MatchValue FROM games WHERE ( MATCH (GameTitle) AGAINST ('$name') OR GameTitle SOUNDS LIKE '$name' ) OR ( MATCH (Alternates) AGAINST ('$name') OR Alternates SOUNDS LIKE '$name' ) HAVING MatchValueBoolean > 0 ";

	if(isset($platform) && !empty($platform))
	{
		$platformResult = mysql_query(" SELECT id FROM platforms WHERE name = '$platform' LIMIT 1 ");
		if(mysql_num_rows($platformResult) != 0)
		{
			$platformRow = mysql_fetch_assoc($platformResult);
			$platformId = $platformRow['id'];
			
			$query = $query . " AND platform = '$platformId'";
		}
		else
		{
			print "<Error>The specified platform was not valid.</Error>\n";
			exit;
		}
	}
	if(isset($genre) && !empty($genre))
	{
		$query = $query . " AND Genre Like '%$genre%'";
	}	

	$query = $query . " ORDER BY MatchValue DESC, MatchValueBoolean DESC LIMIT 20";
}

$result = mysql_query($query) or die('Query failed: ' . mysql_error());

print "<Data>\n";
while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    // Base Info
    $subquery = "SELECT games.id, games.GameTitle, games.ReleaseDate, platforms.name FROM games, platforms WHERE games.id={$obj->id} AND platforms.id = games.Platform";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
			if($key == "name")
			{
				$key = "Platform";
			}
            print "<$key>$value</$key>\n";
        }
    }

    ## End XML item
    print "</Game>\n";
}
?>
</Data>
