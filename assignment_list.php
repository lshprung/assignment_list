<?php
ini_set('display_errors', 1);
error_reporting(-1);

/*
//Restrict Access to Outside IPs
$allowedip = array("172.114.94.2", "129.210.115.225", "129.210.115.226", "129.210.115.227", "129.210.115.228", "129.210.115.229", "129.210.115.8", "129.210.115.230");
$allowed = false;
$ip = $_SERVER['REMOTE_ADDR'];
for($i = 0; $i < sizeof($allowedip); ++$i){
	if($allowedip[$i] == $ip){
		$allowed = true;
		break;
	}
}

if(!$allowed) exit("$ip: Access Denied");
*/

date_default_timezone_set("America/Los_Angeles");

/*
$db = mysqli_connect("localhost", "louie", "louie2000", "louie_school");
if(!$db) die("Failed to connect to database: " . mysqli_connect_error());

if(isset($_REQUEST['add_class_desc'])){
	print htmlspecialchars( $_REQUEST['add_class_desc'] );
	
	print "Will add<br>";

	$name = empty($_REQUEST['add_class_desc']) ? "" : $_REQUEST['add_class_desc'];
	$code = empty($_REQUEST['add_class_id']) ? "" : $_REQUEST['add_class_id'];
	$location = empty($_REQUEST['add_class_location']) ? "left" : $_REQUEST['add_class_location'];
	$link = empty($_REQUEST['add_class_link']) ? "" : $_REQUEST['add_class_link'];

	if(!$code) print "Missing inputs<br>";
	else{
		$location = $location == "left" ? 0 : 1;

		$name = mysqli_real_escape_string($db, $name);
		$code = mysqli_real_escape_string($db, $code);
		//$location = mysqli_real_escape_string($db, $location);
		$link = mysqli_real_escape_string($db, $link);
		$q = "INSERT INTO Classes (Name, Code, Location, Link) VALUES ('$name', '$code', '$location', '$link')";
		if(!mysqli_query($db, $q)) print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}

}
 */

$day = date("l, F jS");
$bg_image_arr = array("rubber_duck_PNG33.png", 
"dice.png");

function entry($due_date, $desc, $color, $highlight, $done){
	if($due_date != "ASAP" && $due_date != ""){
		$due_date_pieces = explode(" ", $due_date);
		$due_date_num = date('z', mktime(0, 0, 0, substr($due_date_pieces[1], 0, 2), substr($due_date_pieces[1], 3, 2), date('Y')));

		if($due_date_num - date('z') <= 1 && $color != "black" && $color != "brown") $color = "red";
		if((!$done && ($due_date_num - date('z') <= 0)) || ($highlight == "orange" && ($due_date_num - date('z') <= 7))) $highlight = "yellow";
	}
	if($highlight != "none" && $color == "gray") $color = "black";
	if($done){
		$color = "green";
		$highlight = "none";
	}

	print "<li class=\"entries\" style=\"color: $color;" . (!$done && $color != 'black' && $highlight != 'none' ? "font-weight: bold" : "") . "\">";
	if($done) print  "<span style=\"color: green; font-weight: bold\"> &#10004;</span>"; // "&#9989;";
	print "<span style=\"background-color: $highlight; text-decoration: " . ($done ? "line-through" : "none") . "\">";
	print ($due_date != "" ? "$due_date: " : "") . "<span style=\"background-color: $highlight\">$desc</span>";
	print "</span>";
	print "</li>";
}

/* Conceptual **

class Entry{
	public static $num_hw = 0;
	public static $hw_strings = [];
	//$hw_string[0] = HW description
	//$hw_string[1] = HW due Date (0 to 365)

	function add_hw(){
		$hw_string[] = readline("What is the HW?\n");
		$days_til_due = readline("In how many days is the HW due?\n");
		$hw_strings[] = $days_til_due + date('z');
		$num_hw++;
	}
}

function list_hw($entry){
	if(!$entry->num_hw){
		print "The test is have work";
	   	return;
	}
	else{
		print "<ul> class=\"entries\"";
		for($i = 0; $i < $num_hw; $i++){
			print "<li> WIP </li>";
		}
		print "</ul>";
	}

	return;
}

$ENGL2A = new Entry();
$MATH13 = new Entry();
$COEN11 = new Entry();
$PHYS31 = new Entry();
$COEN11L = new Entry();
$ENGR1 = new Entry();
$PHYS31L = new Entry();

$ENGL2A->add_hw();
print "TEST: num_hw = $ENGL2A->num_hw";

 */

?>

<html>
	<head>
		<title>Assignment List</title>
		<link rel="stylesheet" href="assignment_list.css">
		<script defer src="assignment_list.js"></script>
		<script defer src="custom/custom.js"></script>
	</head>

	<body>
	<div id=bg></div>
	<div class="container banner_box">
		<span class="banner"></span>
		<button id="add_class" class="add" onclick="draw_add_class()">add class</button>
	<div id="add_class_box">
	</div>
	</div>
		<div id="hw_div">
			<div class="column" id="column_left">
			</div>
			<div class="column" id="column_right">
			</div>

		</div>

		<!-- templates -->
		<template id='add_class_html'>
			<div class="add_dialog" id="add_class_dialog">
				<div>
					<label for="add_class_id">Class ID:</label>
					<input name="add_class_id"/>
				</div>
				<div>
					<label for="add_class_desc">Description/Title:</label>
					<input name="add_class_desc"/>
				</div>
				<div>
					<label for="add_class_link">Class URL:</label>
					<input type="url" name="add_class_link"/>
				</div>
				<div>
					<label for="add_class_location">Column:</label>
					<select name="add_class_location">
						<option value="left">left</option>
						<option value="right">right</option>
					</select>
				</div>
				<div class="submit_box">
					<!-- Add cancel and submit buttons in javascript -->
				</div>
			</div>
		</template>

		<template id='add_assignment_html'>
			<div class="add_dialog">
				<div>
					<label for="add_due_date">Due Date:</label>
					<input name="add_due_date" type="date"/>
				</div>
				<div>
					<label for="add_due_date_alt">Alt Due Date:</label>
					<input name="add_due_date_alt"/>
				</div>
				<div>
					<label for="add_desc">Description/Title:</label>
					<input name="add_desc"/>
				</div>
				<div>
					<label for="add_color">Color:</label>
					<input name="add_color"/>
				</div>
				<div>
					<label for="add_highlight">Highlight:</label>
					<input name="add_highlight"/>
				</div>
				<div>
					<label for="add_link">Assignment URL:</label>
					<input type="url" name="add_link"/>
				</div>
				<div class="submit_box">
					<!-- Add cancel and submit buttons in javascript -->
				</div>
			</div>
		</template>
	</body>
</html>
