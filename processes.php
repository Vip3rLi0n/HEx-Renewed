<?php
require_once('module/template.php');
require_once('module/processes/p-design.php');
$_SESSION["current-site"] = basename(__FILE__, '.php');
$name = "Task manager";
$pbar_info = '';


if($_GET){
	if($_GET["pid"] && $_GET["del"]){
			dbRemoveProcess(connect(), $_GET["pid"], $_SESSION["uid"]);
			header('Location: processes');
	}
	
	if($_GET["pid"]) {
		checkProcessComplete($_GET["pid"]);
	}
}

head($name); 
echo '<body class="processes">';
unk_header();
user_navbar();
echo '<span id="notify"></span>';
sidebar(); 
echo '<div id="content">';
header_info($name);
breadcrumb();
echo '<div class="container-fluid"><div class="row-fluid"><div class="widget-box"><div class="widget-title">';
nav_tabs();
echo '</div><div class="widget-content padding noborder"><ul class="list">';
$pbar_info = checkProcesses($_SESSION["uid"], connect());
echo'</ul></div><div style="clear: both;" class="nav nav-tabs"></div></div></div></div>';

$pbar = <<< ASD
<script type="text/javascript">$(document).ready(function(){jQuery.fn.anim_progressbar=function(aOpts){var iCms=1000;var iMms=60*iCms;var iHms=3600*iCms;var iDms=24*3600*iCms;var vPb=this;return this.each(function(){var iDuration=aOpts.finish-aOpts.start;$(vPb).children('.pbar').progressbar();var vInterval=setInterval(function(){var iLeftMs=aOpts.finish-new Date();var iElapsedMs=new Date()-aOpts.start,iDays=parseInt(iLeftMs/iDms),iHours=parseInt((iLeftMs-(iDays*iDms))/iHms),iMin=parseInt((iLeftMs-(iDays*iDms)-(iHours*iHms))/iMms),iSec=parseInt((iLeftMs-(iDays*iDms)-(iMin*iMms)-(iHours*iHms))/iCms),iPerc=(iElapsedMs>0)?iElapsedMs/iDuration*100:0;$(vPb).children('.percent').html('<b>'+iPerc.toFixed(1)+'%</b>');$(vPb).children('.elapsed').html(iHours+'h:'+iMin+'m:'+iSec+'s</b>');$(vPb).children('.pbar').children('.ui-progressbar-value').css('width',iPerc+'%');if(iPerc>=100){clearInterval(vInterval);$(vPb).children('.percent').html('<b>100%</b>');$(vPb).children('.elapsed').html('<b>Finished</b>');if(aOpts.loaded){document.getElementById('complete'+aOpts.id).innerHTML='<form action="" method="GET"><input type="hidden" name="pid" value="'+aOpts.id+'"><input type="submit" class="btn btn-mini" value="Complete"></form>';}}else{}},aOpts.interval);});}
$pbar_info});</script>
ASD;

footer($pbar); 

?>
