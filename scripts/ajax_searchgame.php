<?php

	include("../modules/mod_elasticsearch.php");

	header('Content-type: application/json');

	$response->result = "failure";

	if(!empty($_REQUEST) && isset($_REQUEST['searchterm']))
	{
		$searchterm = $_REQUEST['searchterm'];

		// Set initial ElasticSearch Parameters
		$searchParams = array();
		$searchParams['index'] = 'thegamesdb';
		$searchParams['type']  = 'game';
		$searchParams['size']  = 6;

		$searchplatform = '';

		if(isset($_REQUEST['platform']))
		{

			$platform = $_REQUEST['platform'];
			$searchplatform = ',
					            "filter": {
									term: {
					              		"PlatformId": "' . $platform . '"
									}
					            }
					          ';
		}

		// Check if $search term contains an integer
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
					        }
					      }
					    }';
				$searchParams['body'] = $json;
			}
			else
			{
				$json = '{
					      "query": {
					        "bool": {
					          "must": [
					            {
					              "match": {
					                "GameTitle": "' . $searchterm . '"
					              }
					            }
					          ]
					        }
					      }' . $searchplatform . '
					    }';
				$searchParams['body'] = $json;
			}

		$elasticResults = $elasticsearchClient->search($searchParams);

		$gamesArray = array();

		foreach ($elasticResults['hits']['hits'] as $elasticGame)
		{
			$gameObject->id = $elasticGame['_source']['id'];
			$gameObject->title = $elasticGame['_source']['GameTitle'];
			$gameObject->platform = $elasticGame['_source']['PlatformName'];

			array_push($gamesArray, $gameObject);

			unset($gameObject);
		}

		if (count($gamesArray) > 0)
		{
			$response->games = $gamesArray;
			$response->result = 'success';
		}

	}
	
	echo json_encode($response);

?>