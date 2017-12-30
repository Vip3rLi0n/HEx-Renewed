<?php
require_once('function.php');


function breadcrumb(){
echo '<div id="breadcrumb">
<a href="index" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Home</a>
<a href="software" id="link0" class="current"> Log File</a>
</div>';
}

function widget_title() {
echo '
<div class="widget-title">
<ul class="nav nav-tabs">
<li class="link active"><a href="log.php"><span class="icon-tab he16-internet_log"></span>Log file</a></li>
<a href="https://wiki.hackerexperience.com/en:log"><span class="label label-info">Help</span></a>
</ul>
</div>';
}

function showLogArea($text,$ownLog){
echo'<form action="logEdit" method="POST" class="log">
<input type="hidden" name="id" value="'. $ownLog .'">
<textarea class="logarea" rows="15" name="log" spellcheck=FALSE>'. $text .'</textarea><br/><br/>
<input class="btn btn-inverse" type="submit" value="Edit log file">
</form>';

}
?>