<?php

	#####################################################
	## ElasticSearch
	#####################################################

	// Include Composer Autoload Packages
	require 'vendor/autoload.php';

	// Initialize New Elasticsearch elasticsearchClient
	$elasticsearchParams = array();
	$elasticsearchParams['hosts'] = array (
	    'localhost:9200'
	);

	$elasticsearchClient = new Elasticsearch\Client($elasticsearchParams);


	// Converts Numbers to Roman Numerals
	function romanNumerals($num) 
	{
	    $n = intval($num);
	    $res = '';
	 
	    /*** roman_numerals array  ***/
	    $roman_numerals = array(
	                'M'  => 1000,
	                'CM' => 900,
	                'D'  => 500,
	                'CD' => 400,
	                'C'  => 100,
	                'XC' => 90,
	                'L'  => 50,
	                'XL' => 40,
	                'X'  => 10,
	                'IX' => 9,
	                'V'  => 5,
	                'IV' => 4,
	                'I'  => 1);
	 
	    foreach ($roman_numerals as $roman => $number) 
	    {
	        /*** divide to get  matches ***/
	        $matches = intval($n / $number);
	 
	        /*** assign the roman char * $matches ***/
	        $res .= str_repeat($roman, $matches);
	 
	        /*** substract from the number ***/
	        $n = $n % $number;
	    }
	 
	    /*** return the res ***/
	    return $res;
    }

?>