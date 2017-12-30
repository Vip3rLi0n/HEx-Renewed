<?php
$_host = "127.0.0.1";
$_user = "yourusername";
$_pw = "yourpassword";
$_db = "yourdatabase";

function connect(){
	global $_host, $_user, $_pw, $_db;
	
	$conn = new mysqli($_host, $_user, $_pw, $_db);
	if ($conn->connect_errno) {
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	} else {
	return $conn;
	}
}

function close($conn){
mysqli_close($conn);
}




?>