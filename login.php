<?php

if($_POST){
session_start();
require_once('mysql/mysql.php');
require_once('mysql/mysql-ext.php');

$user = $_POST["username"];
$pass = $_POST["password"];
$ignore = $_POST["g-recaptcha-response"];
$conn = connect();

$res = Login($conn, $user, $pass);

if(!empty($res)){
		$_SESSION["logged"] = 1;
}

header('Location: index.php');
}


?>