<?php
require_once('module/log/function.php');

if(empty($_SESSION["logged"]))
{ 
header('Location: join.php'); 
}

if(!empty($_POST)){
	if($_POST["log"]){
		if($_POST["id"]){
			if($_POST["id"]==1){
				$conn = connect();
				$stripped_text = strip_tags($_POST["log"]);
				$getid = dbAddProcess($conn,$_SESSION["uid"],6,'Edit log at <a href="software">localhost</a>',"change_log",$stripped_text, 1,"log.php");
			} else {
				
			}
		}
	}
}







header('Location: processes.php?pid=' . $getid);
?>