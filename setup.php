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
	fwrite_wrapper($handle, "\$db = mysqli_connect(\"" . 
		$input["hostname"] . "\"" .
		", \"" . $input["username"] . "\"" .
		", \"" . $input["password"] . "\"" .
		", \"" . $input["database"] . "\"" .
		(!$input["port"] ? "" : ", " . $input["port"]) .
		(!$input["socket"] ? "" : ", \"" . $input["socket"] . "\"") . ");\n"
	);
	fwrite_wrapper($handle, "if(!\$db) die(\"Failed to connect to database: \" . mysqli_connect_error());\n\n");
	fwrite_wrapper($handle, "?>");

	echo "\nSuccessfully wrote \"$config_path\"\n";
}

function fwrite_wrapper($file, $string){
	GLOBAL $config_path;

	if(fwrite($file, $string) === FALSE){
		die("Cannot write to file \"$config_path\"");
	}
}

?>
