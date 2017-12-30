<?php
require_once('mysql/mysql.php');
require_once('mysql/mysql-ext.php');
session_start();

$conn = connect();

if(empty($_SESSION["logged"]))
{ 
header('Location: join.php'); 
}

function checkIP($ip){
	if(ip2long($ip)){
		return 'OK';
	} else {
		return 'ERROR';
	}
}


function display_software($ID){
$data = dbGetSoftware($conn = connect(), $ID);
$size = $data['size'];

setID($ID);
setName($data['name'],$data['type']);
setVersion($data['version']);
setSize($size);
setAttributes($ID);
mysqli_close($conn);

return $size;
}

function setID($ID){
	echo '<tr id="'. $ID .'" class="installed">';
}

function setName($name, $type){
	switch ($type) { 
    case "Cracker": $img = "he16-1"; $full=$name.".crc"; break;
    case "Hasher": $img = "he16-2"; $full=$name.".hash";  break;   
    case "Port Scan": $img = "he16-3"; $full=$name.".scan"; break;
	case "Firewall": $img = "he16-4"; $full=$name.".crc"; break;
	case "Hidder": $img = "he16-5"; $full=$name.".hdr"; break;
	case "Seeker": $img = "he16-6"; $full=$name.".skr"; break;
	case "Antivirus": $img = "he16-7"; $full=$name.".av"; break;
	case "Spam virus": $img = "he16-8"; $full=$name.".spam"; break;
	case "WareZ virus": $img = "he16-9"; $full=$name.".warez"; break;
	case "Virus collector": $img = "he16-11"; $full=$name.".vcol"; break;
	case "DDoS breaker": $img = "he16-12"; $full=$name.".vbrk"; break;
	case "FTP Exploit": $img = "he16-13"; $full=$name.".exp"; break;
	case "SSH Exploit": $img = "he16-14"; $full=$name.".exp"; break;
	case "Nmap": $img = "he16-15"; $full=$name.".nmap"; break;
	case "Analyzer": $img = "he16-16"; $full=$name.".ana"; break;
	case "Torrent": $img = "he16-17"; $full=$name.".torrent"; break;
	case "Web server": $img = "he16-18"; $full='<a href="?cmd=webserver">Webserver.exe</a>'; break;
	case "Bitcoin Miner": $img = "he16-20"; $full=$name.".vminer"; break;
	case "Doom virus": $img = "he16-96"; $full=$name.".vdoom"; break;
}
echo '<td><span class="'. $img .' tip-top" title="'. $type .'"></span></td>';
echo '<td>'. $full .'</td>';
}


function setVersion($version){
	if($version < 15){
		echo '<td><font color="green">'. number_format($version,1) .'</font></td>';
	} elseif  ($version < 20) {
		echo '<td><font color="red">'. number_format($version,1) .'</font></td>';
	} else {
		echo '<td><font color="red"><b>'. number_format($version,1) .'</b></font></td>';
	}

}

function setSize($size){
		$type = "MB";
	if($size>1024){
		$size = $size / 1024;
		$type = "GB";
	}
	

echo '<td class="hide-phone"><font color="red">'. (number_format($size,1) + 0) . ' ' . $type . '</font></td>';
}

//TOO ANOYING FOR NOW SKIP IT.
function setAttributes($ID){
echo '<td style="text-align: center">
<a href="?view=software&id='. $ID .'" class="tip-top" title="Information"><span class="he16-software_info"></span></a>
<a href="?view=software&cmd=uninstall&id='. $ID .'" class="tip-top" title="Kill"><span class="he16-stop"></span></a>
<a href="?view=software&cmd=install&id='. $ID .'" class="tip-top" title="Run"><span class="he16-cog"></span></a>
<a href="?view=software&cmd=hide&id='. $ID .'" class="tip-top" title="Hide"><span class="he16-hide"></span></a>
<a href="?view=software&cmd=del&id='. $ID .'" class="tip-top" title="Delete"><span class="he16-bin"></span></a></td></tr>';	
}

?>