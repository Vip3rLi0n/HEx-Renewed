<?php
session_start();
require_once('mysql/mysql.php');
require_once('mysql/mysql-ext.php');
$conn = connect();

if(empty($_POST["func"])){
	$_POST["func"]="";
}

switch ($_POST["func"]) {
    case "getCommon":
		sendJSON('[{"online":"1243","unread":"0","mission_complete":"0","finances":"'. $_SESSION["money"] .'","unread_title":"Unread messages.","unread_text":"You have 0 unread messages.","online_title":"1243 online players","finances_title":"Finances"}]');
        break;
    case "getStatic":
		sendStatic($_SESSION["ip"],$_SESSION["username"],"312,423","99","Reputation","Ranking");
        break;
	case "getPwdInfo":
		sendJSON('[{"title":"Change password","text":"You can reset your password now <b>for free!</b>","btn":"<input id=\"modal-submit\" type=\"submit\" class=\"btn btn-primary\" value=\"Change\"><a data-dismiss=\"modal\" class=\"btn\" href=\"#\">Cancel</a>","select2":""}]');
		break;
	case "loadHistory":
		sendJSON('[{"ip":"visited list!","time":"1500-05-22T03:46:39"},{"ip":"recently","time":"1900-05-22T04:37:52"},{"ip":"Testing","time":"2017-05-22T09:30:03"}]');
		break;
	case "gettext":
		sendJSON('[{"title":"hilol","text":"k","btn":""}]');
		break;
	case "formatHD":
		sendJSON("error");
		break;
	case "getFileActionsModal":
		if($_POST["type"]=="folder"){
		sendJSON('[{"title":"Create folder at localhost","text":"<div class=\"control-group\"><div class=\"controls\"><input class=\"name\" type=\"text\" name=\"name\" placeholder=\"Folder name\" style=\"width: 80%;\"/></div></div>","btn":"<input type=\"submit\" class=\"btn btn-primary\" value=\"Create folder\"><a data-dismiss=\"modal\" class=\"btn\" href=\"#\">Cancel</a>"}]');
		} elseif ($_POST["type"]=="text") {
		sendJSON('[{"title":"Create text file at localhost","text":"<div class=\"control-group\"><div class=\"input-prepend\"><div class=\"controls\"><input class=\"name\" type=\"text\" name=\"name\" placeholder=\"File name\" style=\"width: 67%;\"/><span class=\"add-on\" style=\"width: 10%;\">.txt</span></div></div></div><div class=\"control-group\"><div class=\"controls\"><textarea id=\"wysiwyg\" class=\"text\" name=\"text\" rows=\"5\" placeholder=\"Content\" style=\"width: 80%;\"></textarea><br/><span class=\"small pull-left link text-show-editor\" style=\"margin-left: 9%;\">Show editor</span></div></div>","btn":"<input type=\"submit\" class=\"btn btn-primary\" value=\"Create text file\"><a data-dismiss=\"modal\" class=\"btn\" href=\"#\">Cancel</a>"}]');
		}
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
	case "tempHolder":
		sendJSON("error");
		break;
    default:
	Debug("unexepected packet: ",$_POST["func"]);
}
?>


<?php
function sendJSON($data){
header('Content-type: application/json');

if($data == "error"){
$obj = (object) array('status' => 'ERROR', 'redirect' => '', 'msg' => 'STOP SPYING ON ME!');
} else {
$obj = (object) array('status' => 'OK', 'redirect' => '', 'msg' => $data);
}


echo json_encode($obj);	
}

function Debug($p,$data) {
	file_put_contents('data.txt', $p . $data . "\n", FILE_APPEND | LOCK_EX);
}

function sendStatic($ip, $user, $rep, $rank, $rep_title, $rank_title){
	sendJSON('[{"ip":"'. $ip .'","user":"'. $user .'","reputation":"'. $rep .'","rank":"'. $rank .'","rep_title":"'. $rep_title .'","rank_title":"'. $rank_title .'"}]');
}
?>
