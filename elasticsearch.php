<?php
	require 'vendor/autoload.php';

	$client = new Elasticsearch\Client();

	//Insert Record
	/*$params = array();
	$params['body']  = array('firstName' => 'Alex', 'lastName' => 'Nazaruk', 'dateOfBirth' => '26/10/1984');
	$params['index'] = 'alexindex';
	$params['type']  = 'people';
	$params['id']    = 'someID';
	$ret = $client->index($params);
	var_dump($ret); */

	// Search For A Record
	$searchParams['index'] = 'alexindex';
	$searchParams['type']  = 'people';
	$searchParams['body']['query']['match']['firstName'] = 'john';
	$retDoc = $client->search($searchParams);
	var_dump($retDoc);
	var_dump($retDoc['hits']);
?>