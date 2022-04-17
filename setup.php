<?php

$config_path = "backend/config.php";
$check = "y";

print "This script will help to setup MySQL database access\n";

if(file_exists($config_path)){
	$check = readline("Warning: \"$config_path\" already exists. This script will overwrite the contents of this file. Proceed? [y/N] ");
}

if($check == "y"){
	print "\n";

	if(!$handle = fopen($config_path, "w")){
		die("Error: Cannot open file \"$config_path\"\n");
	}

	$input["hostname"] = readline("Enter hostname [leave blank for default]: ");
	$input["username"] = readline("Enter username: ");
	$input["password"] = readline("Enter password [leave blank for no password]: ");
	$input["database"] = readline("Enter database: ");
	$input["port"] = readline("Enter port number [leave blank for default]: ");
	$input["socket"] = readline("Enter socket [leave blank for default]: ");

	fwrite_wrapper($handle, "<?php\n\n");
	fwrite_wrapper($handle, "\$hostname = '" . $input["hostname"] . "';\n");
	fwrite_wrapper($handle, "\$username = '" . $input["username"] . "';\n");
	fwrite_wrapper($handle, "\$password = '" . $input["password"] . "';\n");
	fwrite_wrapper($handle, "\$database = '" . $input["database"] . "';\n");
	if($input["port"]) fwrite_wrapper($handle, "\$port = " . $input["port"] . ";\n");
	fwrite_wrapper($handle, "\$socket = '" . $input["socket"] . "';\n\n");

	fwrite_wrapper($handle, "\$db = mysqli_connect(" . 
		"\"\$hostname\"" .
		", \"\$username\"" .
		", \"\$password\"" .
		", \"\$database\"" .
		(!$input["port"] ? "" : ", \$port") . 
		(!$input["socket"] ? "" : ", \"\$socket\"") . ");\n"
	);
	fwrite_wrapper($handle, "if(!\$db) die(\"\nFailed to connect to database: \" . mysqli_connect_error()\n);\n\n");
	fwrite_wrapper($handle, "?>");

	echo "\nSuccessfully wrote \"$config_path\"\n";
}

$check = readline("Automatically create tables? [Y/n] ");

if($check != "n"){
	print "\n";

	print "Testing connection using credentials...";
	include "backend/config.php";
	print "\tSuccess!\n";

	GLOBAL $database;
	$tables = [
		"Classes" => "CREATE TABLE IF NOT EXISTS Classes (
  ID int(11) NOT NULL AUTO_INCREMENT,
  Name varchar(255) DEFAULT NULL,
  Code varchar(255) NOT NULL,
  Location tinyint(1) DEFAULT NULL,
  Link varchar(255) DEFAULT NULL,
  Hidden tinyint(1) DEFAULT '0',
  PRIMARY KEY (ID))", 
		"Assignments" => "CREATE TABLE IF NOT EXISTS Assignments (
  ID int(11) NOT NULL AUTO_INCREMENT,
  Class_ID int(11) NOT NULL,
  Due_date date DEFAULT NULL,
  Alt_due_date varchar(255) DEFAULT NULL,
  Description varchar(1024) NOT NULL,
  Color varchar(255) DEFAULT NULL,
  Highlight varchar(255) DEFAULT NULL,
  Done tinyint(1) DEFAULT NULL,
  Link varchar(255) DEFAULT NULL,
  Hidden tinyint(1) DEFAULT '0',
  PRIMARY KEY (ID))"
	];

	foreach(array_keys($tables) as $key){
		print "Creating table \"$key\"...";
		$q = $tables[$key];
		if(mysqli_query($db, $q)) print "\tSuccess!\n";
		else die("$q\nInsertion Failed: " . mysqli_error($db) . "\n");
	}
}

function fwrite_wrapper($file, $string){
	GLOBAL $config_path;

	if(fwrite($file, $string) === FALSE){
		die("Cannot write to file \"$config_path\"");
	}
}

?>
