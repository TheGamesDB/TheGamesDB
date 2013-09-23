<?php
## Interface that allows clients to post-back ratings for a game
## Parameters:
##   $_REQUEST["accountid"]
##   $_REQUEST["itemtype"]
##   $_REQUEST["itemid"]
##   $_REQUEST["rating"] (1-10) 0=remove rating
##
## Returns:
##   Success

## Include functions, db connection, etc
include("include.php");


## Prepare the search string
$accountid		= $_REQUEST["accountid"];
$itemtype		= $_REQUEST["itemtype"];
$itemid			= intval($_REQUEST["itemid"]);
$rating			= isset($_REQUEST["rating"]) ? intval($_REQUEST["rating"]) : null;


## Check the field values
if ($accountid == "") {
    print "<Error>accountid is required</Error>\n";
    exit;
}
//elseif ($itemtype != "game") {
//    print "<Error>itemtype must be game</Error>\n";
//    exit;
//}
elseif ($rating < 0 || $rating > 10) {
    print "<Error>rating must be an integer between 0 and 10</Error>\n";
    exit;
}

if(isset($rating)) {
## A rating of 0 means we remove it from the database
    if ($rating === 0) {
        $query = "DELETE FROM ratings WHERE itemtype='game' AND itemid=$itemid AND userid=(SELECT id FROM users WHERE uniqueid='$accountid' LIMIT 1)";
        $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
    }


## Handle adds/updates
    else {
        ## Run the query
        $query = "SELECT id FROM ratings WHERE itemtype='game' AND itemid=$itemid AND userid=(SELECT id FROM users WHERE uniqueid='$accountid' LIMIT 1) LIMIT 1";
        $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
        $db = mysql_fetch_object($result);


        ## If we've found a valid user, replace the rating
        if ($db->id) {
            $query	= "UPDATE ratings SET rating=$rating WHERE id=$db->id";
            $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
        }
        ## Otherwise, insert a new record
        else {
            $query	= "INSERT INTO ratings (itemtype, itemid, userid, rating) VALUES ('game', $itemid, (SELECT id FROM users WHERE uniqueid='$accountid' LIMIT 1), $rating)";
            $result	= mysql_query($query) or die('Query failed: ' . mysql_error());
        }
    }


## Recreate the XML files
    if ($itemtype == "game") {
        $query		= "UPDATE games SET lastupdated=UNIX_TIMESTAMP() WHERE id=$itemid";
        $result		= mysql_query($query) or die('Query failed: ' . mysql_error());
    }
}

## Return the current rating
$query = "SELECT ROUND(AVG(rating),1) AS average FROM ratings WHERE itemtype='game' AND itemid=$itemid";
$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
$db = mysql_fetch_object($result);
?>
<Data>
    <game>
    <Rating><?=$db->average?></Rating>
    </game>
</Data>
