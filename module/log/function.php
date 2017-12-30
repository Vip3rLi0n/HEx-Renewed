<?php
require_once('mysql/mysql.php');
require_once('mysql/mysql-ext.php');
session_start();
$conn = connect();

if(empty($_SESSION["logged"]))
{ 
header('Location: join.php'); 
}

if(!isset($_SESSION["log_text"])){
$_SESSION["log_text"] = '';
}
?>