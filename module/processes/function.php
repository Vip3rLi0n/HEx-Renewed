<?php
require_once('mysql/mysql.php');
require_once('mysql/mysql-ext.php');
session_start();

//+3 in seconds when multiple processes even when theres only 2??? FIX TODO
// at 9 processes it skips 9 in seconds. 
//NEWEST SHOULD BE ON TOP BUT ITS ON THE BOTTOM NOW! TODO


//FIX: DO NOT CHECK EVERYTIME YOU REFRESH PAGE ONLY CHECK WHEN NEW PROCESS IS ADDED!!
//MAYBE UPDATE EVERY PROCESS TO * TASKS IN MYSQL?
function getRunningTasks($uid){
$data = dbGetProcesses(connect(),$uid);
$count == 0;

	for ($x = 0; $x < sizeof($data); $x++) {
		if(!empty($data)){
			$start = strtotime($data[$x]["start_time"]);
			$end = strtotime($data[$x]["end_time"]);
			$time_left = getTimeleft($start, $end);
			if(!$time_left == 0){ $count++; }
			
		}
	}
	
if($count == 0){ $count = 1; }
return $count;
}

function checkProcesses($uid, $conn){
$data = dbGetProcesses($conn,$uid);
$tasks = getRunningTasks($uid);
$bar = '';
$count = 1;
for ($x = 0; $x < sizeof($data); $x++) {

	if(!empty($data)){
		$start = strtotime($data[$x]["start_time"]);
		$end = strtotime($data[$x]["end_time"]);
		$time_left = getTimeleft($start, $end);
		$bar .= addProcces($data[$x]["pid"], $time_left, $data[$x]["title"]);	
	}
}
return $bar;
}

function checkProcessComplete($pid){
$conn = connect();
$data = dbGetProcess($conn ,$_SESSION["uid"], $_GET["pid"]);

	if(!empty($data)){
		$start = strtotime($data["start_time"]);
		$end = strtotime($data["end_time"]);
		$time_left = getTimeleft($start, $end);	
			
		if($time_left == 0){
			doProcessComplete($data["function"],$data["function_data"],$data["function_id"],$data["redirect"], $_SESSION["uid"],$_GET["pid"]);
		} else {
			
		}
	}
}

function doProcessComplete($func, $func_data, $func_id, $redirect,$uid,$pid){
	
	switch ($func) {
		case 'change_log': //*editlog
		if($func_id == 1){
			setLog(connect(), $uid, $func_data);
			dbRemoveProcess(connect(), $pid, $uid);
		}	
		break;
		
		case 'hack_brute':
			setLog(connect(), $uid, "2017-05-26 15:28 - localhost logged in to [". $func_data ."] as root");
			dbRemoveProcess(connect(), $pid, $uid);
		break;
	}

	if($redirect){
		header('Location: ' . $redirect);
	}
}

function getTimeleft($start, $end){
$_elapsed = time() - $start;
$_end = $end - $start;
$_left = $_end - $_elapsed;

if($_left < 0){
$_left = 0;	
}


return $_left;
}

?>