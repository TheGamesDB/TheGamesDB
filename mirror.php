<?php	## Display all of the records that have been updated since $lastupdated
	header("Content-type: text/plain");


	## Settings
	include("config.php");
	$TABLES		= array();
	$FIELDS 	= array();
	$maxupdate	= 0;


	## Connect to the database
	$database = mysql_connect($db_server, $db_user, $db_password) or die('Could not connect: ' . mysql_error());
	mysql_select_db($db_database) or die('Could not select database');


	## Get the last updated time for this mirror and verify it is an actual mirror
	$id	= mysql_real_escape_string($id);
	$pass	= mysql_real_escape_string($pass);
	if ($id == '' || $pass == '')  {
		print "INCORRECT MIRROR ID AND PASS\n";
		exit;
	}
	$query	= "SELECT mirrordate FROM mirrors WHERE id=$id AND mirrorpass='$pass'";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($result) == 0)  {
		print "INCORRECT MIRROR ID AND PASS\n";
		exit;
	}
	$mirror = mysql_fetch_object($result);
	$lasttime = $mirror->mirrordate;
	print "LASTUPDATE::$lasttime\n";


	## If we just got the mirrorupdate parameter, we update the database and exit
	if ($mirrorupdate)  {
		$mirrorupdate	= mysql_real_escape_string($mirrorupdate);
		$mirrorupdate	= date("Y-m-d H:i:s", $mirrorupdate);
		$query		= "UPDATE mirrors SET mirrordate='$mirrorupdate' WHERE id=$id AND '$mirrorupdate' > mirrordate";
		$result		= mysql_query($query) or die('Query failed: ' . mysql_error());
		print "$mirrorupdate\n";
		exit;
	}


	## Get a list of tables, columns , and column data types
	$query	= "SELECT table_name, column_name, column_type FROM information_schema.columns WHERE table_schema = '$db_database'";
	$result	= mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($db = mysql_fetch_object($result))  {

		## Store each table name
		$TABLES[$db->table_name] = 1;

		## Store each field name and type
		$FIELDS[$db->table_name][$db->column_name] = $db->column_type;
		if ($FIELDLIST[$db->table_name] == "")  {
			$FIELDLIST[$db->table_name] = $db->column_name . "|" . $db->column_type;
		}
		else  {
			$FIELDLIST[$db->table_name] .= "\t" . $db->column_name . "|" . $db->column_type;
		}
	}


	## Loop through each table, looking for updated rows
	foreach ($TABLES as $table => $junk)  {
		if ($table == 'history')  {
			continue;
		}


		## First query for the updated rows
		$fieldlist = implode(", ", array_keys($FIELDS[$table]));
		$query = "SELECT $fieldlist FROM $table WHERE mirrorupdate > '$lasttime' ORDER BY mirrorupdate";
		$result	= mysql_query($query) or die('Query failed: ' . mysql_error());


		## If we found rows, print the schema
		if (mysql_num_rows($result) > 0)  {
			$fieldlist = $FIELDLIST[$table];
			print "SCHEMA:$table:$fieldlist\n";
		}


		## Now, print each row
		while ($db = mysql_fetch_assoc($result))  {

			## Properly format the data
			foreach ($db AS $key => $value)  {
				$db[$key] = str_replace(array("\r\n", "\r", "\n"), ":NEWLINE:", $value);
				$db[$key] = str_replace("\t", " ", $db[$key]);
				$db[$key] = stripslashes($db[$key]);
			}


			## Remove user and mirror passwords
			if ($table == 'users')  {
				$db["userpass"] = "";
			}
			if ($table == 'mirrors')  {
				$db["mirrorpass"] = "";
			}


			## Output the data
			$valuelist = implode("\t", $db);
			print "DATA:$table:$valuelist\n";


			## Record the maximum update time
			if (strtotime($db["mirrorupdate"]) > $maxupdate)  {
				$maxupdate = strtotime($db["mirrorupdate"]);
			}
		}
	}


	## Report the maximum update time back to the client
	print "MIRRORUPDATE::$maxupdate";
?>