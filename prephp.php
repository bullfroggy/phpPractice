<?php
class gameData
{	
	function insertRow($handler){
		if (isset($_POST["Name"])){
			if ($_POST["Name"] !== ""){
				if(isset($_POST["Beaten"])){
					$beaten = 1;
				}
				else {
					$beaten = 0;
				}

				if(isset($_POST["Physical"])){
					$physical= 1;
				}
				else {
					$physical = 0;
				}
				if (isset($_POST["Release"])){
					$release = $_POST["Release"];
					$newRelease = date("Y-m-d", strtotime($release));
				}
				else {
					$newRelease = '0000-00-00';
				}
				
				$sql = "insert into all_games values(DEFAULT,:name, :beaten, :physical, :release_date);";
				$query = $handler->prepare($sql);
				$query->bindParam(':name',$_POST["Name"], PDO::PARAM_STR);
				$query->bindParam(':beaten',$beaten, PDO::PARAM_BOOL);
				$query->bindParam(':physical',$physical, PDO::PARAM_BOOL);
				$query->bindParam(':release_date',$newRelease, PDO::PARAM_STR);
				$query->execute();
			}
		}
	}
	function showQuery($handler){
		$sql3 = "select * from all_games order by name;";
		$query = $handler->query($sql3);

		if ($query->rowCount() > 0) {
			while($row3 = $query->fetch(PDO::FETCH_OBJ)) {
				if ($row3->beaten == 0){
					$beaten = 'No';
				}
				else{
					$beaten = 'Yes';
				}
				if ($row3->physical == 0){
					$physical = 'No';
				}
				else {
					$physical = 'Yes';
				}
				$release = date('m/d/Y', strtotime($row3->release_date));
				$valArray = array($row3->counter, $row3->name, $beaten, $physical, $release);
				$valString = implode('<>', $valArray);
				echo "<tr id='" . 
					$row3->counter . 
					"' class='games'><td class='names'>" . 
					$row3->name . 
					"</td><td><center>" . 
					$beaten . 
					"</center></td><td><center>" . 
					$physical . 
					"</center></td><td><center>". 
					$release . 
					"</center></td><td><center><input type=checkbox name='delete_" . 
					$row3->counter . 
					"' value='yes'></center></td><td><input class='button' type=button onclick=\"editRow('" . $valString . "')\" value='Edit'></td>" .
					"<td id=\"updated_" . $row3->counter . "\"></td></tr>";
			}
		} else {
			echo "<tr class='games'><td class='names>'0 results'</td></tr>";
		}
	}
	
	function deleteRows($handler){
		$sql1 = "select max(counter) as counter from all_games;";
		$result1 = $handler->query($sql1);
		$row1 = $result1->fetch(PDO::FETCH_OBJ);
		$delindex = $row1->counter;
		$sql2 = "delete from all_games where counter is null";
		for($x = 0; $x <= $delindex; $x++){
			if(isset($_POST["delete_" . $x])){
				if($_POST["delete_" . $x] = "yes"){
					$sql2 .= " or counter = " . $x;
				}
			}
		}
		$result2 = $handler->query($sql2);
	}
	
	function editRows($handler){
		$sql1 = "select max(counter) as counter from all_games;";
		$result1 = $handler->query($sql1);
		$row1 = $result1->fetch(PDO::FETCH_OBJ);
		$delindex1 = $row1->counter;
		$sql2 = "select min(counter) as counter from all_games;";
		$result2 = $handler->query($sql2);
		$row2 = $result2->fetch(PDO::FETCH_OBJ);
		$delindex2 = $row2->counter;
		for($x = $delindex2; $x <= $delindex1; $x++){
			if(isset($_POST["undo_" . $x])){
				$valString = $_POST["undo_" . $x];
				$valArray = explode('<br>', $valString);
				$valString = array_pop($valArray);
				$valArray = explode('<>', $valString);
				$counter = $valArray[0];
				$name = $valArray[1];
				$beaten = $valArray[2];
				$physical = $valArray[3];
				$release = $valArray[4];
				
				if($beaten == 'Yes'){
					$beaten = 1;
				}
				else {
					$beaten = 0;
				}

				if($physical == 'Yes'){
					$physical= 1;
				}
				else {
					$physical = 0;
				}
				
				$newRelease = date("Y-m-d", strtotime($release));
				$sql2 = "update all_games set name = :name, beaten = :beaten, physical = :physical, release_date = :release_date where counter = :counter";
				$query = $handler->prepare($sql2);
				$query->bindParam(':name',$name, PDO::PARAM_STR);
				$query->bindParam(':beaten',$beaten, PDO::PARAM_BOOL);
				$query->bindParam(':physical',$physical, PDO::PARAM_BOOL);
				$query->bindParam(':release_date',$newRelease, PDO::PARAM_STR);
				$query->bindParam(':counter',$counter, PDO::PARAM_INT);
				$query->execute();
			}
		}
	}
}

try {
	$handler = new PDO('mysql:host=127.0.0.1;dbname=gamelist', 'jeremy', 'Oopsies!2');
	$handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e) {
	echo $e->getMessage();
	die('Connection to DB failed');
}

$newGame = new GameData;

if(isset($_POST['Name']) OR isset($_POST['modify'])){
	
	if (isset($_POST["Name"])){
		$newGame->insertRow($handler);
	}

	else if (isset($_POST["modify"])){
		if ($_POST["modify"] == "Delete"){
			$newGame->deleteRows($handler);
		}
		else {
			$newGame->editRows($handler);
		}
	}
	header("Location: testpage.php");
    exit;
}
$newGame->showQuery($handler);
?>
<script>
function editRow(valString){
	var valArray = valString.split("<>");
	var counter = valArray[0];
	var curUpdate = document.getElementById("updated_" + counter).innerHTML;
	if (valArray[2] == "Yes"){
		var Beaten = "checked";
	}
	else {
		var Beaten = "";
	}
	if (valArray[3] == "Yes"){
		var Physical = "checked";
	}
	else {
		var Physical = "";
	}
	var editing = "<td><input class=\"name\" type=\"text\" name=\"Name\" value = \'" + valArray[1] + "\'></td>" + 
                    "<td><input class=\"beaten button\" type=\"checkbox\" name=\"Beaten\" " + Beaten + "></td>" +
					"<td><input class=\"physical button\" type=\"checkbox\" name=\"Physical\" " + Physical + "></td>" +
					"<td><input class=\"release hasDatepicker\" type=\"text\" name=\"Release\" id=\"datepicker\" value = \'" + valArray[4] + "\'></td>" +
					"<td><input class=\"button\" type=\"button\" value=\"Update\" onclick=\"updateTableValues(\'" + counter + "\', \'" + valString + "\')\"></td>" +
					"<td><input class=\"button\" type=\"button\" value=\"Cancel\" onclick=\"resetValues(\'" + counter + 
					"\', \'" + valArray[1] + "\', \'" + valArray[2] + "\', \'" + valArray[3] + "\', \'" + valArray[4] + 
					"\', \'" + valString + "\')\"</td>" + 
					"<td id=\"updated_" + counter + "\">" + curUpdate + "</td>";
	document.getElementById(valArray[0]).innerHTML = editing;
}

function resetValues(counter, name, beaten, physical, release, valString){
	var curUpdate = document.getElementById("updated_" + counter).innerHTML;
	update = "<td class=\"names\">" + name + 
	"</td><td><center>" + beaten + 
	"</center></td><td><center>" + physical + 
	"</center></td><td><center>" + release + 
	"</center></td><td><center><input type=\"checkbox\" name=\"delete_" + counter + 
	"\" value=\"yes\"></center></td><td><input class=\"button\" type=\"button\" onclick=\"editRow('"+valString+"')\" value=\"Edit\"></td>" + 
	"<td id=\"updated_" + counter + "\">" + curUpdate + "</td>";
	document.getElementById(counter).innerHTML = update;
}

function revertChanges(counter, curUpdate){
	curUpdateArray = curUpdate.split("<br>");
	var valArray = curUpdateArray.pop().split("<>");
	var valString = valArray.join("<>");
	curUpdateString = curUpdateArray.join("<br>");
	var name = valArray[1];
	var beaten = valArray[2];
	var physical = valArray[3];
	var release = valArray[4];
	var undo = '';
	if(curUpdateString != ''){
		undo = "<input name=\"" + curUpdateString + "\" class=\"button undo\" type=\"button\" value=\"Undo\" onclick=\"revertChanges(\'" + counter + 
						"\', \'" + curUpdateString + "\')\"</td><td class=\"hidden\"><input name=\"undo_" + counter + "\" type=\"text\" value=\"" + valString + "\"></td>";
	}
	update = "<td class=\"names\">" + name + 
	"</td><td><center>" + beaten + 
	"</center></td><td><center>" + physical + 
	"</center></td><td><center>" + release + 
	"</center></td><td><center><input type=\"checkbox\" name=\"delete_" + counter + 
	"\" value=\"yes\"></center></td><td><input class=\"button\" type=\"button\" onclick=\"editRow('"+valString+"')\" value=\"Edit\"></td>" + 
	"<td id=\"updated_" + counter + "\">" + undo;
	document.getElementById(counter).innerHTML = update;
}

function updateTableValues(counter, valString){
	var name = document.getElementById(counter).getElementsByClassName("name")[0].value;
	var beaten = document.getElementById(counter).getElementsByClassName("beaten")[0].checked;
	var physical = document.getElementById(counter).getElementsByClassName("physical")[0].checked;
	var release = document.getElementById(counter).getElementsByClassName("release")[0].value;
	var curUpdate = document.getElementById("updated_" + counter).innerHTML;
	if(beaten){
		beaten = 'Yes';
	}
	else{
		beaten = 'No';
	}
	
	if(physical){
		physical = 'Yes';
	}
	else{
		physical = 'No';
	}
	var curUndo = '';
	var newValString = counter + '<>' + name + '<>' + beaten + '<>' + physical + '<>' + release;
	if(newValString != valString){
		if(curUpdate != ''){
			curUpdate = document.getElementById("updated_" + counter).getElementsByClassName("undo")[0].getAttribute("name");
			document.getElementById("updated_" + counter).innerHTML = curUpdate + "<br>" + valString;
			curUpdate = document.getElementById("updated_" + counter).innerHTML;
		}
		else {
			curUpdate = valString;
		}
		updated = "<input name=\"" + curUpdate + "\" class=\"button undo\" type=\"button\" value=\"Undo\" onclick=\"revertChanges(\'" + counter + "\', \'" + curUpdate + "\')\">" +
		"</td><td class=\"hidden\"><input name=\"undo_" + counter + "\" type=\"text\" value=\"" + newValString + "\"></td>";
	}
	else{
		updated = curUpdate;
	}
	var update = "<td class=\"names\">" + name + 
	"</td><td><center>" + beaten + 
	"</center></td><td><center>" + physical + 
	"</center></td><td><center>" + release + 
	"</center></td><td><center><input type=\"checkbox\" name=\"delete_" + counter + 
	"\" value=\"yes\"></center></td><td><input class=\"button\" type=\"button\" onclick=\"editRow('"+newValString+"')\" value=\"Edit\"></td>" + 
	"<td id=\"updated_" + counter + "\">" + updated + "</td>";
	document.getElementById(counter).innerHTML = update;
}
</script>