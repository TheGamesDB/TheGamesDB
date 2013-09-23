<?php	## Interface that allows clients to get
	## seriesid using seriesname
	## Parameters:
	##   $_REQUEST["seriesname"]
	##   $_REQUEST["language"]		(optional)
	##   $_REQUEST["user"]			(optional... overrides language setting)
	##
	## Returns:
	##   XML item holding the series id that matches the seriesname

	## Include functions, db connection, etc
	include("include.php");
?>
<?php
	## Prepare the search string
	$seriesname		= $_REQUEST["seriesname"];
	$language		= $_REQUEST["language"];
	$user			= $_REQUEST["user"];
	if ($seriesname == "")  {
		print "<Error>seriesname is required</Error>\n";
		exit;
	}
	else  {
		if (strpos($seriesname,", The")){$seriesname = "The ".substr($seriesname,0,strpos($seriesname,", The"));}
		if (strpos($seriesname,"'")){$seriesname = str_replace("\'","",$seriesname);} ##To be removed if someone can figure out hwo to do this in sphinx
		print "<Data>\n";
	}


	## Get the languageid from the abbreviation
	if ($language && $language != "all")  {
		$query = "SELECT id FROM languages WHERE abbreviation='$language' LIMIT 1";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$db = mysql_fetch_object($result);
		if ($db->id)  {
			$languageid = $db->id;
		}
		else  {
			$languageid = 7;
		}
	}
	## If language wasn't passed in
	elseif ($language != "all")  {
		## Get the user's preferred language if possible
		if ($user)  {
			$query = "SELECT languageid FROM users WHERE uniqueid='$user' LIMIT 1";
			$result = mysql_query($query) or die('Query failed: ' . mysql_error());
			$db = mysql_fetch_object($result);
			if ($db->languageid)  {
				$languageid = $db->languageid;
			}
			else  {
				$languageid = 7;
			}
		}
		else  {
			$languageid = 7;
		}
	}

	## Run the query
	include('../sphinxapi.php');
	$cl = new SphinxClient();
	$cl->SetServer( "localhost", 3312 );
	$cl->SetMatchMode( SPH_MATCH_ALL );
	$cl->SetLimits(0, 100);
	$cl->SetSortMode ( SPH_SORT_EXTENDED, "@weight DESC, languageid ASC" );

	if ($languageid)  {
		$cl->SetFilter('languageid', array( $languageid, 7 ));
		$cl->SetSortMode ( SPH_SORT_EXTENDED, "@weight DESC, languageid DESC" );
	}

	$result = $cl->Query( "START_ " . $seriesname . " _END", 'seriesname', "seriesname");
	
	if ( $result === false ) {
		echo "Query failed: " . $cl->GetLastError() . ".\n";
	}
	else {
		if ( !empty($result["matches"]) ) {
			foreach ($result["matches"] AS $id => $junk)  {

				## Get base information
				$subquery = "SELECT seriesid, languageid, (SELECT abbreviation FROM languages WHERE id=translation_seriesname.languageid) AS language,  translation AS SeriesName FROM translation_seriesname WHERE id=$id";
				$subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
				$db = mysql_fetch_object($subresult);
				if (!$db->seriesid)  {
					continue;
				}

				## Get top banner
				$subquery = "SELECT filename FROM banners WHERE keytype='series' AND keyvalue=$db->seriesid ORDER BY (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) DESC,RAND() LIMIT 1";
				$subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
				if ($subdb = mysql_fetch_object($subresult))  {
					$db->banner = $subdb->filename;
				}

				## Get additional information (overview)
				$subquery = "SELECT translation FROM translation_seriesoverview WHERE seriesid=$db->seriesid AND (languageid=$db->languageid OR languageid=7) AND translation IS NOT NULL ORDER BY languageid DESC LIMIT 2";
				$subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
				if ($subdb = mysql_fetch_object($subresult))  {
					$db->Overview = $subdb->translation;
				}

				## Get additional information (FirstAired, network, IMDB_id, zap2it_id)
				$subquery = "SELECT FirstAired, Network, IMDB_ID, zap2it_id FROM tvseries WHERE id=$db->seriesid LIMIT 1";
				$subresult = mysql_query($subquery) or die('Query failed: ' . mysql_error());
				if ($subdb = mysql_fetch_object($subresult))  {
					if ($subdb->FirstAired)  {
						$db->FirstAired = $subdb->FirstAired;
					}
					if ($subdb->network)  {
						$db->Network = $subdb->network;
					}
					if ($subdb->IMDB_ID)  {
						$db->IMDB_ID = $subdb->IMDB_ID;
					}
					if ($subdb->zap2it_id)  {
						$db->zap2it_id = $subdb->zap2it_id;
					}
				}

				## Remove fields
				unset($db->languageid);

				## Duplicate series id as id for posterity
				$db->id = $db->seriesid;

				## Start XML item
				print "<Series>\n";

				## Loop through each field for this item
				foreach ($db as $key => $value)  {

					## Prepare the string for output
					$value = xmlformat($value, $key);

					## Print the string
					print "<$key>$value</$key>\n";

				}

				## End XML item
				print "</Series>\n";
			}
		}
	}
?>
</Data>
