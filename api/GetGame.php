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
$name = $_REQUEST["name"];
$id = $_REQUEST['id'];
//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];

if (empty($name) && empty($id)) {
    print "<Error>A name or id is required</Error>\n";
    exit;
} else {
    if (isset($name)) {
        if (strpos($name, ", The")) {
            $name = "The " . substr($name, 0, strpos($name, ", The"));
        }
        if (strpos($name, "'")) {
            $name = str_replace("\'", "", $name);
        }
    }
    if (isset($id) && !is_numeric($id)) {
        print "<Error>An ID must be an int</Error>\n";
        exit;
    } else {
        $id = (int) $id;
    }
    print "<Data>\n<baseImgUrl>http://thegamesdb.net/banners/</baseImgUrl>\n";
}

$query;
if (isset($id) && !empty($id)) {
    $query = "SELECT id FROM games WHERE id=$id";
} else {
    $query = "SELECT id FROM games WHERE GameTitle LIKE '%$name%'";
}
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

while ($obj = mysql_fetch_object($result)) {
    print "<Game>\n";

    // Base Info
    $subquery = "SELECT g.id, g.GameTitle, g.ReleaseDate, g.Overview, g.Rating as ESRB, g.Genre, g.Players, g.Publisher, g.Developer, g.Actors, AVG(r.rating) as Rating FROM games as g LEFT JOIN ratings as r ON (g.id=r.itemid and r.itemtype='game') WHERE g.id={$obj->id} Group By g.id";
    $baseResult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    $baseObj = mysql_fetch_object($baseResult);
    foreach ($baseObj as $key => $value) {
        ## Prepare the string for output
        if (!empty($value)) {
            $value = xmlformat($value, $key);
            switch ($key) {
                case 'Genre':
                    echo '<Genres>';
                    $genres  = explode('|', $value);
                    foreach($genres as $genre){
                        if(!empty($genre)){
                            echo '<genre>' . $genre . '</genre>';
                        }
                    }
                    echo '</Genres>';
                    break;

                case 'Rating':
                    print "<Rating>" . (float)$value . "</Rating>";
                    break;

                default:
                    print "<$key>$value</$key>\n";
            }
        }
    }

    ## Get top banner
    $subquery = "SELECT filename, keytype FROM banners WHERE keyvalue=$obj->id";
    $subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
    if ($subresult) {
        echo "<Images>";
        while ($subdb = mysql_fetch_object($subresult)) {
            $key = $subdb->keytype;
            $value = $subdb->filename;
            $value = xmlformat($value, $key);

            switch ($key) {
                case 'series':
                    echo "<banner>$value</banner>";
                    break;

                case 'fanart':
                    echo '<fanart>';
                    echo "<original>$value</original>";
                    $v = str_replace('original', 'vignette', $value);
                    echo "<vignette>$v</vignette>";
                    echo '</fanart>';
                    break;

                case 'boxart':
                    echo "<boxart>$value</boxart>";
                    break;
            }
        }
        echo "</Images>";
    }

    ## End XML item
    print "</Game>\n";
}
?>
</Data>
