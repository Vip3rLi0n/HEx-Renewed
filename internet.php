<?php
require_once('module/template.php');
require_once('module/internet/net-design.php');
$_SESSION["current-site"] = basename(__FILE__, '.php');
$name = "Internet";

$conn = connect();

if($_GET["action"]=="login" && $_GET["user"] && $_GET["pass"]){
	$getid = dbAddProcess($conn,$_SESSION["uid"],32,'Login to Server',"login_server",$_SESSION["last-ip"],0,"internet.php");
	header('Location: processes.php?pid=' . $getid);
}

if($_GET["action"]=="hack" && $_GET["method"] == "xp"){
	$getid = dbAddProcess($conn,$_SESSION["uid"],32,'Hacking with Exploit',"hack_exploit",$_SESSION["last-ip"],0,"internet.php");
	header('Location: processes.php?pid=' . $getid);
} elseif($_GET["action"]=="hack" && $_GET["method"] == "bf"){
	$getid = dbAddProcess($conn,$_SESSION["uid"],32,'Hacking with Bruteforce',"hack_brute",$_SESSION["last-ip"],0,"internet.php");
	header('Location: processes.php?pid=' . $getid);
}


head($name);
echo '<body class="internet history">';
unk_header();
user_navbar();
echo '<span id="notify"></span>';
sidebar(); 
echo '<div id="content">';
header_info($name);
breadcrumb();
echo '
<div class="container-fluid">
<div class="row-fluid">
<div class="span9">';
if(!empty($_GET["ip"])){
inputBox($_GET["ip"]);
} else {
inputBox($_SESSION["last-ip"]);
}

echo '<div class="widget-box"><div class="widget-title">';
navTab($_GET["action"]);
echo '</div>';
echo '<div class="widget-content padding noborder">';



if(isset($_SESSION["last-ip"])){
	
	if(!$_GET["action"]){
		displayIP($conn, $_GET);
	}
	
	if($_GET["action"]=="hack"){
		hackBox('-','-','-');
	}
	
	if($_GET["action"]=="login"){
		printLoginBox('','');
	}

}


echo '<div style="clear: both;" class="nav nav-tabs">&nbsp;</div>
</div></div>
<div class="span3">';
recently_visited();
echo '<div class="widget-box collapsible">';
important_ip();
echo '</div></div></div></div>';
footer(); 
?>


<?php
function displayIP($conn,$get){	
if(!empty($get["ip"])){
		if(checkIP($get["ip"]) == "ERROR"){
			//print with last ip if invaild ip!
			printIP(getServer($conn,$_SESSION["last-ip"]));
		} else {
			if($data = getServer($conn,$get["ip"])){
				//printIP(getServer($conn,$get["ip"]));
				$_SESSION["last-ip"] = $get["ip"];
			} else {
				error404();
			}
		}
	} else {
		//print with last ip if no ip is input!
		if(!$get["action"]){
		printIP(getServer($conn,$_SESSION["last-ip"]));
		}
	}
}
?>