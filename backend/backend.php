<?php

include "config.php";

if($_REQUEST['type'] == "save_class"){
	print htmlspecialchars( $_REQUEST['add_class_desc'] );
	
	print "Will add<br>";

	$name = empty($_REQUEST['add_class_desc']) ? "" : $_REQUEST['add_class_desc'];
	$code = empty($_REQUEST['add_class_id']) ? "" : $_REQUEST['add_class_id'];
	$location = empty($_REQUEST['add_class_location']) ? "left" : $_REQUEST['add_class_location'];
	$link = empty($_REQUEST['add_class_link']) ? "" : $_REQUEST['add_class_link'];
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];

	if(!$code) print "Missing inputs<br>";
	else{
		$location = $location == "left" ? 0 : 1;

		$name = mysqli_real_escape_string($db, $name);
		$code = mysqli_real_escape_string($db, $code);
		//$location = mysqli_real_escape_string($db, $location);
		$link = mysqli_real_escape_string($db, $link);
		$id = mysqli_real_escape_string($db, $id);

		//if $id exists, this should be an update query, instead of an insert query
		if($id) $q = "UPDATE Classes SET Name = '$name', Code = '$code', Location = '$location', Link = '$link' WHERE id = '$id'";
		else $q = "INSERT INTO Classes (Name, Code, Location, Link) VALUES ('$name', '$code', '$location', '$link')";
		if(mysqli_query($db, $q)) print "Success";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}

}

elseif($_REQUEST['type'] == "load_classes"){
	$q = "SELECT * FROM Classes WHERE Hidden = '0'";
	
	//allow for selection of specific id
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];
	if($id){
		$id = mysqli_real_escape_string($db, $id);
		$q .= "AND id = $id";
	}

	$res = mysqli_query($db, $q);
	if(mysqli_num_rows($res) > 0) print json_encode(mysqli_fetch_all($res));
	else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
}

elseif($_REQUEST['type'] == "delete_class"){
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];
	if(!$id) print "Missing inputs<br>";
	else{
		$id = mysqli_real_escape_string($db, $id);
		$q = "UPDATE Classes Set Hidden = '1' WHERE id = '$id'";
		if(mysqli_query($db, $q)) print "Success";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";

		//also need to hide corresponding assignments
		$q = "UPDATE Assignments Set Hidden = '1' WHERE Class_id = '$id'";
		if(mysqli_query($db, $q)) print "Success";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}
}
	

elseif($_REQUEST['type'] == "save_assignment"){
	print htmlspecialchars( $_REQUEST['add_desc'] );

	print "Will add<br>";

	$class_id = empty($_REQUEST['add_class_id']) ? "" : $_REQUEST['add_class_id'];
	$due_date = empty($_REQUEST['add_due_date']) ? "" : $_REQUEST['add_due_date'];
	$due_date_alt = empty($_REQUEST['add_due_date_alt']) ? "" : $_REQUEST['add_due_date_alt'];
	$desc = empty($_REQUEST['add_desc']) ? "" : $_REQUEST['add_desc'];
	$color = empty($_REQUEST['add_color']) ? "" : $_REQUEST['add_color'];
	$highlight = empty($_REQUEST['add_highlight']) ? "" : $_REQUEST['add_highlight'];
	$link = empty($_REQUEST['add_link']) ? "" : $_REQUEST['add_link'];
	$done = 0;
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];

	if(!$desc) print "Missing inputs<br>";
	else{
		$class_id = mysqli_real_escape_string($db, $class_id);
		$due_date = mysqli_real_escape_string($db, $due_date);
		$due_date_alt = mysqli_real_escape_string($db, $due_date_alt);
		$desc = mysqli_real_escape_string($db, $desc);
		$color = mysqli_real_escape_string($db, $color);
		$highlight = mysqli_real_escape_string($db, $highlight);
		$link = mysqli_real_escape_string($db, $link);
		$id = mysqli_real_escape_string($db, $id);

		//if $id exists, this should be an update query, instead of an insert query
		if($id) $q = "UPDATE Assignments SET Due_date = " . ($due_date ? "'$due_date'" : "NULL") . ", Alt_due_date = '$due_date_alt', Description = '$desc', Color = '$color', Highlight = '$highlight', Link = '$link' WHERE id = '$id'";
		else $q = "INSERT INTO Assignments (Class_ID, Due_date, Alt_due_date, Description, Color, Highlight, Done, Link) VALUES ('$class_id', " . ($due_date ? "'$due_date'" : "NULL") . ", '$due_date_alt', '$desc', '$color', '$highlight', '$done', '$link')";
		if(mysqli_query($db, $q)) print "Success $q";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}
}

elseif($_REQUEST['type'] == "load_assignments"){
	$q = "SELECT * FROM Assignments WHERE Hidden != 1 ";
	
	//allow for selection of specific id
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];
	if($id){
		$id = mysqli_real_escape_string($db, $id);
		$q .= "AND id = $id ";
	}
	$q .=  "ORDER BY Due_date";

	$res = mysqli_query($db, $q);
	if(mysqli_num_rows($res) > 0) print json_encode(mysqli_fetch_all($res));
	else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
}

elseif($_REQUEST['type'] == "clone_assignment"){
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];

	if(!$id) print "Missing inputs<br>";
	else{
		$id = mysqli_real_escape_string($db, $id);

		$q = "SELECT * FROM Assignments WHERE id = '$id'";
		$res = mysqli_query($db, $q);
		if(mysqli_num_rows($res) > 0){
			$row = mysqli_fetch_assoc($res);
		}
		else{
			print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
			return;
		}

		if(!$row["Due_date"]) $row["Due_date"] = "NULL";
		$q = "INSERT INTO Assignments (";
		for($i = 1; $i < count($row); ++$i){
			$q .= array_keys($row)[$i];
			if($i+1 < count($row)) $q .= ", ";
		}
		$q .= ") VALUES (";
		for($i = 1; $i < count($row); ++$i){
			$q .= (array_values($row)[$i] == "NULL" ? "NULL" : "'" . array_values($row)[$i] . "'");
			if($i+1 < count($row)) $q .= ", ";
		}
		$q .= ")";
		if(mysqli_query($db, $q)) print "Success $q";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}
}

elseif($_REQUEST['type'] == "toggle_done"){
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];

	if(!$id) print "Missing inputs<br>";
	else{
		$id = mysqli_real_escape_string($db, $id);
		$q = "UPDATE Assignments SET Done = (Done + 1) % 2 WHERE id = '$id'";
		if(mysqli_query($db, $q)) print "Success";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}
}

elseif($_REQUEST['type'] == "delete_assignment"){
	$id = empty($_REQUEST['id']) ? "" : $_REQUEST['id'];
	if(!$id) print "Missing inputs<br>";
	else{
		$id = mysqli_real_escape_string($db, $id);
		$q = "UPDATE Assignments Set Hidden = '1' WHERE id = '$id'";
		if(mysqli_query($db, $q)) print "Success";
		else print "$q<br>Insertion Failed: " . mysqli_error($db) . "<br>";
	}
}

else print "Errors: Unknown headers " . print_r($_REQUEST,1);

?>
