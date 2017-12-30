<?php
function Login($conn, $user, $pass){
	  $sql = "SELECT uid, money, ip, log_data FROM account WHERE username = '$user' and password = '$pass'";
      $result = mysqli_query($conn,$sql)
		or die("Error: ".mysqli_error($conn));
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	  $count = mysqli_num_rows($result);
	  $_SESSION["username"] = $user;
	  $_SESSION["uid"] = $row['uid'];
	  $_SESSION["money"] = $row['money'];
	  $_SESSION["ip"] = $row['ip'];
	  $_SESSION["last-ip"] = "1.2.3.4";
	  $_SESSION['err'] = "no";
	  return $row['uid'];
}

function dbAddProcess($conn, $UID, $time, $title, $function, $function_data, $function_id, $redirect){
$sql = "INSERT INTO process (end_time,  function, function_data, function_id, redirect, userID, title) 
VALUES (DATE_ADD(NOW(), INTERVAL $time SECOND), '$function', '$function_data', '$function_id', '$redirect', '$UID', '$title')";

if ($conn->query($sql) === TRUE) { }

return mysqli_insert_id($conn);
}

function dbGetRunningTasks($conn, $uid){
$result=mysqli_query($conn,"SELECT COUNT(*) as total FROM process WHERE userID = '$uid'");
$data=mysqli_fetch_assoc($result);
return $data['total'];
}

function dbGetProcesses($conn, $uid){
$sql = "SELECT pid,start_time,end_time,title FROM process WHERE userID = '$uid'";	
$result = mysqli_query($conn,$sql);
$data = NULL;

if ($result = mysqli_query($conn,$sql)) {
	while ($row = $result->fetch_assoc()) 
	{
		$data[] = $row;
	}
} 
return $data;
}

function dbUpdateProcessTime($conn, $uid, $pid, $tasks){
$sql = "UPDATE account SET log_data='$data' WHERE uid='$UID'";
if ($conn->query($sql) === TRUE) { }
}

function dbGetProcess($conn, $uid, $pid){
$sql = "SELECT start_time,end_time,function,function_data,function_id,redirect FROM process WHERE userID = '$uid' AND pid = '$pid'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return $row;
}

function dbRemoveProcess($conn, $pid, $uid){
$sql = "DELETE FROM process WHERE pid=$pid AND userID=$uid";
$conn->query($sql);
}

function dbGetServerHDD($conn, $serverID){
$sql = "SELECT id FROM software WHERE serverID = '$serverID'";	
$result = mysqli_query($conn,$sql);

if ($result = mysqli_query($conn,$sql)) {

	while ($row = $result->fetch_assoc()) 
	{
		$data[] = $row["id"];
	}
} 
return $data;
}

function dbGetSoftware($conn, $ID){
$sql = "SELECT name, version, size, ram, type, ownerid, created FROM software WHERE id = '$ID'";	
$result = mysqli_query($conn,$sql);
return mysqli_fetch_array($result,MYSQLI_ASSOC);
}

function setLog($conn, $UID, $data){
$sql = "UPDATE account SET log_data='$data' WHERE uid='$UID'";
if ($conn->query($sql) === TRUE) { }
}

function getLog($conn, $UID){
$sql = "SELECT log_data FROM account WHERE uid='$UID'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return $row["log_data"];
}


function getServer($conn, $ip){
	$sql = "SELECT type, text, extra, ip FROM server WHERE ip = '$ip'";	
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	return $row;
}

function saveHDD_data($conn, $uid, $arrID){
$sarr = serialize($arrID);
$sql = "UPDATE account SET HDD_data='$sarr' WHERE uid='$uid'";
if ($conn->query($sql) === TRUE) { echo "Record updated successfully"; }			
}

function saveServer_ID($conn, $UID, $arrID){
$sarr = serialize($arrID);
$sql = "UPDATE account SET serverID='$sarr' WHERE uid='$UID'";
if ($conn->query($sql) === TRUE) { echo "Record updated successfully"; }
}

function saveBank_ID($conn, $UID, $arrID){
$sarr = serialize($arrID);
$sql = "UPDATE account SET bankID='$sarr' WHERE uid='$UID'";
if ($conn->query($sql) === TRUE) { echo "Record updated successfully"; }
}

function loadHDD_data($conn, $UID){
$sql = "SELECT HDD_data FROM account WHERE uid = '$UID'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return unserialize($row['HDD_data']);
}

function loadServer_ID($conn, $UID){
$sql = "SELECT serverID FROM account WHERE uid = '$UID'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return unserialize($row['serverID']);
}

function loadBank_ID($conn, $UID){
$sql = "SELECT bankID FROM account WHERE uid = '$UID'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return unserialize($row['bankID']);
}

function loadSoftware($conn, $SOFT_ID){
$sql = "SELECT name, level, size, ram, type, ownerid, last FROM software WHERE id = '$SOFT_ID'";	
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return $row['name'] . ' level:' . $row['level'] . ' type:' . $row['type'];
}



?>