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
$platform = mysql_real_escape_string($_REQUEST["platform"]);
$genre = mysql_real_escape_string($_REQUEST["genre"]);
//$name = str_replace(array(' - ', '-'), '%', $name);

//$language		= $_REQUEST["language"];
$user = $_REQUEST["user"];
try
{
	// ElasticSearch Based Search
	// --------------------------

	// Include ElsticSearch Client
	include("../modules/mod_elasticsearch.php");
	$gamesArray = array();

	$searchterm = $name;

	// Set initial ElasticSearch Parameters
	$searchParams = array();
	$searchParams['index'] = 'thegamesdb';
	$searchParams['type']  = 'game';
	$searchParams['size']  = 100;

	// If $platform was specified, grab platform id
	if(isset($platform) && !empty($platform))
	{
		$platformResult = mysql_query(" SELECT id FROM platforms WHERE name = '$platform' LIMIT 1 ");

		if (mysql_num_rows($platformResult) != 0)
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

	// If name parameter is specified (minimum requirement)
	// ----------------------------------------------------
	if(isset($name) && !empty($name))
	{
		// If name and platform parameters are specified
		// ---------------------------------------------
		if(isset($platform) && !empty($platform))
		{
			$searchplatform = ',
					            "filter": {
									term: {
					              		"PlatformId": "' . $platformId . '"
									}
					            }';
		}

		if(isset($genre) && !empty($genre))
		{
			/*$searchgenre = ',
					            "filter": {
									"regexp":{
										"Genre" : "*' . strtolower($genre) . '*"
									}
								}';*/

			/*$searchgenre = ',
					            "filter": {
									"bool": {
										"must": [{
											"regexp": {
												"Genre": {
													"value": "*' . strtolower($genre) . '*"
												}
											}
										}]
									}
								}';*/

			$searchgenre = ',
					            "query" : {
									"wildcard" : {
										"Genre" : "*' . strtolower($genre) . '*"
									}
								}';
		}

		// Check if $search term contains an integer and also match Roman Numerals
		if (strcspn($searchterm, '0123456789') != strlen($searchterm))
		{
			// Extract first number found in string
			preg_match('/\d+/', $searchterm, $numbermatch, PREG_OFFSET_CAPTURE);
			$numberAsNumber = $numbermatch[0][0];

			// Convert Number to Roman Numerals
			$numberAsRoman = romanNumerals($numberAsNumber);

			// Replace Number in string with RomanNumerals
			$searchtermRoman = str_replace($numberAsNumber, $numberAsRoman, $searchterm);

			$json = '{
						"query": {
							"bool": {
								"must": [
									{
									"match": {
										"GameTitle": "' . $searchterm . '"
									}
									},
									{
									"match": {
										"GameTitle": "' . $searchtermRoman . '"
									}
									}
								]
							}' . $searchgenre . '
						}' . $searchplatform . '
					}';
			$searchParams['body'] = $json;
		}
		// If no integers, directly match the string provided
		else
		{
			$json = '{
						"query": {
							"bool": {
								"must": [{
									"match": {
										"GameTitle": "' . $searchterm . '"
									}
								}]
							}' . $searchgenre . '
						}' . $searchplatform . '
					}';
			$searchParams['body'] = $json;
		}

	}
	// If only $genre is provided
	// --------------------------
	else if(isset($genre) && !empty($genre))
	{
		$json = '{
					"query" : {
						"wildcard" : {
							"Genre" : "*' . strtolower($genre) . '*"
						}
					}
				}';
		$searchParams['body'] = $json;
	}
	// If only $platform is provided
	// -----------------------------
	else if(isset($platform) && !empty($platform))
	{
		$json = '{
					"filter": {
						term: {
							"PlatformId": "' . $platformId . '"
						}
					}
				}';
		$searchParams['body'] = $json;
	}
	// If no parameters are provided at all
	// ------------------------------------
	else
	{
	    print "<Error>Please supply either the name, genre, or platform parameters. The platform parameter can also be used in conjuntion with the name parameter to search for titles only on a specific platform.</Error>\n";
	    exit;
	}

	/* Perform elasticsearch query
	   --------------------------- */
	$elasticResults = $elasticsearchClient->search($searchParams);
	foreach ($elasticResults['hits']['hits'] as $elasticGame)
	{
		//var_dump($elasticGame);

		$gameObject = new stdClass();

		$gameObject->id = $elasticGame['_source']['id'];
		$gameObject->GameTitle = $elasticGame['_source']['GameTitle'];
		$gameObject->ReleaseDate = $elasticGame['_source']['ReleaseDate'];
		$gameObject->Platform = $elasticGame['_source']['PlatformName'];
		//$gameObject->Genre = $elasticGame['_source']['Genre'];

		array_push($gamesArray, $gameObject);

		unset($gameObject);
	}

	/* Output elasticsearch Results as XML
	   ----------------------------------- */
	print "<Data>\n";
	foreach ($gamesArray as $game)
	{
		## Start XML Item
		print "<Game>\n";

		foreach ($game as $key => $value) {
			## Prepare the string for output
			//if (!empty($value)) {
				$value = xmlformat($value, $key);
				print "<$key>$value</$key>\n";
			//}
		}

		## End XML item
		print "</Game>\n";
	}
	print "</Data>\n";
}
catch (Exception $ex)
{
	// MySQL Based Search
	// ------------------
	if(isset($name) && !empty($name))
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

		$query = $query . " ORDER BY MatchValue DESC, MatchValueBoolean";
	}
	else if(isset($genre) && !empty($genre))
	{
		$query = "SELECT * FROM games WHERE Genre Like '%$genre%'";
	}
	else if(isset($platform) && !empty($platform))
	{
		$platformResult = mysql_query(" SELECT id FROM platforms WHERE name = '$platform' LIMIT 1 ");
		if(mysql_num_rows($platformResult) != 0)
		{
			$platformRow = mysql_fetch_assoc($platformResult);
			$platformId = $platformRow['id'];

			$query = "SELECT * FROM games WHERE platform = $platformId";
		}
	}
	else
	{
	    print "<Error>Please supply either the name, genre, or platform parameters. The platform parameter can also be used in conjuntion with the name parameter to search for titles only on a specific platform.</Error>\n";
	    exit;
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
	print "</Data>";
}
?>
