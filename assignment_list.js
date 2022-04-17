//Function to get date
function get_day(d){
	if(!d) d = new Date();
	const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	let output = days[d.getDay()] + ", " + months[d.getMonth()] + " " + d.getDate();
	switch(d.getDate()){
		case 1:
			output += "st";
			break;
		case 2:
			output += "nd";
			break;
		case 3:
			output += "rd";
			break;
		default:
			output += "th";
	}

	return output;
}

function day_of_year(date){
	//get milliseconds
	console.log(date);
	console.log(Math.floor(date.getTime() / 1000 / 60 / 60 / 24));
	return Math.round(date.getTime() / 1000 / 60 / 60 / 24);
}

function hide_dialog(selector){
	const obj = document.querySelector(selector);
	if(obj) obj.innerHTML = "";
	else console.log("hide_dialog: bad selector: \"" + selector + "\"");
}

document.querySelector(".banner").innerText = get_day();

function clear_all(){
	for(const element of document.querySelectorAll(".column")){
		element.innerHTML = "";
	}
}

/* Load from database */

function draw_class(entry){
	if(!(entry instanceof Array)){
		console.log(entry);
		console.log("Error loading classes");
		return;
	}

	const id = entry[0];
	const description = entry[1];
	const code = entry[2];
	const column_bool = entry[3];
	const url = entry[4];

	const columns = document.querySelectorAll(".column");
	const insert_column = columns[column_bool];

	//class_html
	/*
	<div class="container course_box">
		<span class="course"></span>
		<button class="add add_assignment">add assignment</button>
		<button class="edit edit_class">edit</button>
		<button class="delete delete_class">delete</button>
	</div>
	*/

	let class_html = "<div class='container course_box' id='course_" + id + "_container'>" +
		"<span class='course' id='course_" + id + "'>";
	if(url != "") class_html += "<a href='" + url + "'>";
	class_html += code;
	if(url != "") class_html += "</a>";
	class_html += "</span>" +
		"<button class=\"add add_assignment\" onclick=\"draw_add_assignment(" + id + ")\">add assignment</button>" + 
		"<button class=\"edit edit_class\" onclick=\"draw_edit_class(" + id + ")\">edit</button>" +
		"<button class=\"delete delete_class\" onclick=\"delete_class(" + id + ")\">delete</button>" +
		"</div>" +
		"<div class=\"add_assignment_box\" id=\"add_assignment_box_" + id + "\"></div>" + //box to draw add_assignment dialog in
		"<ul class=\"assignment_list\" id=\"assignment_list_" + id + "\"></ul>"; //ul box to draw assignments in
	
	insert_column.innerHTML += class_html;
}

function draw_assignment(entry){
	if(!(entry instanceof Array)){
		console.log(entry);
		console.log("Error loading assignment");
		return;
	}

	let id = entry[0];
	let class_id = entry[1];
	let due_date = entry[2];
	let due_date_alt = entry[3];
	let desc = entry[4];
	let color = entry[5];
	let highlight = entry[6];
	let done = entry[7];
	let link = entry[8];

	//custom element handling (based on php)
	/*
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
	*/
	
	if(!highlight) highlight = "none";
	let due_date_pieces;
	let current_date;
	let due_date_object;
	if(due_date){
		due_date_pieces = due_date.split("-");
		current_date = new Date();
		due_date_object = new Date(due_date_pieces[0], due_date_pieces[1]-1, due_date_pieces[2]);
		if(day_of_year(due_date_object) - day_of_year(current_date) <= 0 && color != "black" && color != "brown") color = "red";
		if((done == 0 && (day_of_year(due_date_object) - day_of_year(current_date) <= -1)) || (highlight == "orange" && (day_of_year(due_date_object) - day_of_year(current_date) <= 6))) highlight = "yellow";
	}
	if(highlight != "none" && color == "gray") color = "black";
	if(done == 1){
		color = "green";
		highlight = "none";
	}

	const box = document.querySelector("#assignment_list_" + class_id);

	/* reference:
	print "<li class=\"entries\" style=\"color: $color;" . (!$done && $color != 'black' && $highlight != 'none' ? "font-weight: bold" : "") . "\">";
	if($done) print  "<span style=\"color: green; font-weight: bold\"> &#10004;</span>"; // "&#9989;";
	print "<span style=\"background-color: $highlight; text-decoration: " . ($done ? "line-through" : "none") . "\">";
	print ($due_date != "" ? "$due_date: " : "") . "<span style=\"background-color: $highlight\">$desc</span>";
	print "</span>";
	print "</li>";
	*/

	const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

	let assignment_html = "<div class='container entry_box' id='entry_box_" + id + "'><li class='entries' id='entry_" + id + "' style='color: " + color + ";";
	if(done == 0 && color != "black" && highlight != "none") assignment_html += "font-weight: bold";
	assignment_html += "'>";
	if(done == 1) assignment_html += "<span style='color: green; font-weight: bold'> &#10004;</span>";
	assignment_html += "<span style='background-color: " + highlight + "; text-decoration: ";
	if(done == 1) assignment_html += "line-through";
	else assignment_html += "none";
	assignment_html += "'>";
	if(due_date){
		assignment_html += days[due_date_object.getDay()] + ", ";
		if(due_date_object.getMonth()+1 < 10) assignment_html += "0";
		assignment_html +=  due_date_object.getMonth()+1 + "/";
		if(due_date_object.getDate()+1 < 10) assignment_html += "0";
		assignment_html += due_date_object.getDate()
	}
	else assignment_html += due_date_alt;
	assignment_html += ": <span style='background-color: " + highlight + "'>";
	if(link) assignment_html += "<a href='" + link + "'>";
	assignment_html += desc;
	if(link) assignment_html += "</a>";
	assignment_html += "</span></span>" +
		"<button class=\"edit edit_assignment\" onclick=\"draw_edit_assignment(" + class_id + ", " + id + ")\">edit</button>" +
		"<button class=\"add add_assignment\" onclick=\"clone_assignment(" + id + ")\">clone</button>" +
		"<button class=\"mark_done\" onclick=\"toggle_done(" + id + ")\" style=\"background-color: " + (done == 1 ? "hotpink" : "lightgreen") + "\">" + (done == 1 ? "not done" : "done") + "</button>" +
		"<button class=\"delete delete_assignment\" onclick=\"delete_assignment(" + id + ")\">delete</button>" +
		"</div></li>" +
		"<div class=\"add_assignment_box\" id=\"edit_assignment_box_" + id + "\"></div></div>"; //box to draw add_assignment dialog in

	box.innerHTML += assignment_html;
}

function load_data(mode){
	let type;
	if(mode == "classes") type = "load_classes";
	else if(mode == "assignments") type = "load_assignments";
	else{
		console.log("Error: Unknown data to load. Only \"classes\" and \"assignments\" are supported arguments");
		return;
	}

	let url = "backend/backend.php";
	let data = "type=" + type;

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		console.log(JSON.parse(this.response));
		for(const entry of JSON.parse(this.response)){
			if(mode == "classes") draw_class(entry);
			else if(mode == "assignments") draw_assignment(entry);
		}
	}
	xhttp.send(data);
}

/* add a new class */

//show dialog
function draw_add_class(id){
	let box;
	if(!id) box = document.querySelector("#add_class_box");
	else box = document.querySelector("#add_assignment_box_" + id);
	const add_class_html = document.querySelector("#add_class_html").innerHTML;
	const buttons = "<input class=\"cancel\" id=\"add_class_cancel\" onclick=\"hide_add_class(" + id + ")\" type=\"button\" value=\"Cancel\">" + 
		"<button class=\"submit\" onclick=\"submit_add_class(" + id + ")\" type=\"button\">Submit</button>";

	box.innerHTML = add_class_html;
	box.querySelector(".submit_box").innerHTML = buttons;
}

//hide dialog
function hide_add_class(id){
	if(!id) hide_dialog("#add_class_box");
	else hide_dialog("#add_assignment_box_" + id);
}

//submit dialog
function submit_add_class(id){
	const code = document.querySelector("[name = add_class_id]").value;
	const name = document.querySelector("[name = add_class_desc]").value;
	const link = document.querySelector("[name = add_class_link]").value;
	const location = document.querySelector("[name = add_class_location]").value;

	//check that class ID is not null
	if(code == ""){
		alert("Missing Class ID");
		return;
	}

	//TODO create array instead
	let url = "backend/backend.php";

	let data = "type=save_class" + 
		"&add_class_desc=" + encodeURIComponent(name) +
		"&add_class_id=" + encodeURIComponent(code) +
		"&add_class_location=" + encodeURIComponent(location) +
		"&add_class_link=" + encodeURIComponent(link);
	
	if(id) data += "&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			if(!id) alert("Successfully added class '" + code + "'");
			hide_add_class(id);
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;

}

/* add assignment to class */

//show dialog
function draw_add_assignment(id, ass_id){
	let box;
	if(!ass_id) box = document.querySelector("#add_assignment_box_" + id);
	else box = document.querySelector("#edit_assignment_box_" + ass_id);
	const add_assignment_html = document.querySelector("#add_assignment_html").innerHTML;
	const buttons = "<input class=\"cancel\" onclick=\"hide_add_assignment(" + id + ", " + ass_id + ")\" type=\"button\" value=\"Cancel\">" +
		"<button class=\"submit\" onclick=\"submit_add_assignment(" + id + ", " + ass_id + ")\" type=\"button\">Submit</button>";

	box.innerHTML = add_assignment_html;
	box.querySelector(".submit_box").innerHTML = buttons;
}

//hide dialog
function hide_add_assignment(id, ass_id){
	if(!ass_id) hide_dialog("#add_assignment_box_" + id);
	else hide_dialog("#edit_assignment_box_" + ass_id);
}

//submit dialog
function submit_add_assignment(id, ass_id){
	let box;
	if(!ass_id) box = document.querySelector("#add_assignment_box_" + id);
	else box = document.querySelector("#edit_assignment_box_" + ass_id);

	const class_id = id;
	const due_date = box.querySelector("[name = add_due_date]").value; //formatted YYYY-MM-DD
	const due_date_alt = box.querySelector("[name = add_due_date_alt]").value;
	const desc = box.querySelector("[name = add_desc]").value;
	const color = box.querySelector("[name = add_color]").value;
	const highlight = box.querySelector("[name = add_highlight]").value;
	const link = box.querySelector("[name = add_link]").value;

	//check that desc is not null
	if(desc == ""){
		alert("Missing Assignment Description/Title");
		return;
	}

	let url = "backend/backend.php";

	//TODO create array instead
	let data = "type=save_assignment" + 
		"&add_class_id=" + encodeURIComponent(class_id) + 
		"&add_due_date=" + encodeURIComponent(due_date) + 
		"&add_due_date_alt=" + encodeURIComponent(due_date_alt) + 
		"&add_desc=" + encodeURIComponent(desc) + 
		"&add_color=" + encodeURIComponent(color) + 
		"&add_highlight=" + encodeURIComponent(highlight) + 
		"&add_link=" + encodeURIComponent(link);

	if(ass_id) data += "&id=" + encodeURIComponent(ass_id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			if(!id) alert("Successfully added assignment '" + desc + "'");
			hide_add_assignment(id);
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}

/* edit a class */
function draw_edit_class(id){
	draw_add_class(id);

	//load values
	let url = "backend/backend.php";

	let data = "type=load_classes" +
		"&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		const response = JSON.parse(this.response);
		if(response[0] instanceof Array){
			document.querySelector("[name = add_class_id]").value = response[0][2];
			document.querySelector("[name = add_class_desc]").value = response[0][1];
			document.querySelector("[name = add_class_link]").value = response[0][4];
			document.querySelector("[name = add_class_location]").value = (response[0][3] == "0" ? "left" : "right");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}

/* delete a class */
function delete_class(id){
	let prompt = confirm("Are you sure you want to delete this class?");
	if(!prompt) return;

	//note that classes are not deleted, just set to hidden
	let url = "backend/backend.php";

	let data = "type=delete_class" +
		"&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}

/* edit an assignment */
function draw_edit_assignment(id, ass_id){
	draw_add_assignment(id, ass_id);

	//load values
	const box = document.querySelector("#edit_assignment_box_" + ass_id);

	let url = "backend/backend.php";

	let data = "type=load_assignments" +
		"&id=" + encodeURIComponent(ass_id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		console.log(this.response);
		const response = JSON.parse(this.response);
		if(response[0] instanceof Array){
			box.querySelector("[name = add_due_date]").value = response[0][2];
			box.querySelector("[name = add_due_date_alt]").value = response[0][3];
			box.querySelector("[name = add_desc]").value = response[0][4];
			box.querySelector("[name = add_color]").value = response[0][5];
			box.querySelector("[name = add_highlight]").value = response[0][6];
			box.querySelector("[name = add_link]").value = response[0][8];
		}
	};
	xhttp.send(data);
	return false;
}

/* clone an assignment */
function clone_assignment(id){
	let url = "backend/backend.php";

	let data = "type=clone_assignment" +
		"&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}
	
/* toggle assignment complete */
function toggle_done(id){
	const box = document.querySelector("#edit_assignment_box_" + id);

	let url = "backend/backend.php";

	let data = "type=toggle_done" +
		"&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}

/* delete an assignment */
function delete_assignment(id){
	let prompt = confirm("Are you sure you want to delete this assignment?");
	if(!prompt) return;

	//note that classes are not deleted, just set to hidden
	let url = "backend/backend.php";

	let data = "type=delete_assignment" +
		"&id=" + encodeURIComponent(id);

	console.log(data);
	let xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, false);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.onload = function(){
		if(this.response.indexOf("Success") > -1){
			clear_all();
			load_data("classes");
			load_data("assignments");
		}
		console.log(this.response);
	};
	xhttp.send(data);
	return false;
}

load_data("classes");
load_data("assignments");
