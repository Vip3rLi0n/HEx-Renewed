<?php
require_once('function.php');


function breadcrumb(){
echo '<div id="breadcrumb">
<a href="index" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Home</a>
<a href="software" id="link0" class="current"> Internet</a>
</div>';
//alot more complicated breadcrumb will fix later!
}

function recently_visited(){
echo '<div class="widget-box collapsible">
<div class="widget-title">
<a href="#history" data-toggle="collapse">
<span class="icon"><span class="he16-page_copy"></span></span>
<h5>Recently visited</h5>
</a>
</div>
<div class="collapse in" id="history">
<div class="widget-content">
<div id="visited-ips"></div>
</div>
</div>
</div>';	
}


function important_ip(){
echo '<div class="widget-title">
<a href="#important" data-toggle="collapse">
<span class="icon"><span class="he16-important"></span></span>
<h5>Important IPs</h5>
<span class="label label-important hide1024">11</span>
</a>
</div>
<div class="collapse in" id="important">
<div class="widget-content">
<a href="internet?ip=1.2.3.4">1.2.3.4<span class="hide1024 small"> First Whois</span></a><br/>
<a href="internet?ip=206.211.30.54">206.211.30.54<span class="hide1024 small"> First International Bank</span></a><br/>
<a href="internet?ip=145.120.155.3">145.120.155.3<span class="hide1024 small"> American Expense</span></a><br/>
<a href="internet?ip=1.158.201.174">1.158.201.174<span class="hide1024 small"> ISP - Internet Service Provider</span></a><br/>
<a href="internet?ip=234.56.132.167">234.56.132.167<span class="hide1024 small"> Safenet</span></a><br/>
<a href="internet?ip=17.69.155.119">17.69.155.119<span class="hide1024 small"> HEBC</span></a><br/>
<a href="internet?ip=9.149.137.9">9.149.137.9<span class="hide1024 small"> NSA</span></a><br/>
<a href="internet?ip=164.50.56.166">164.50.56.166<span class="hide1024 small"> Second Whois</span></a><br/>
<a href="internet?ip=92.248.30.241">92.248.30.241<span class="hide1024 small"> Ultimate Bank</span></a><br/>
<a href="internet?ip=227.238.225.94">227.238.225.94<span class="hide1024 small"> Swiss International Bank</span></a><br/>
<a href="internet?ip=210.117.143.141">210.117.143.141<span class="hide1024 small"> FBI</span></a><br/>
</div>
</div>';
}

function printLoginBox($user, $pass){
echo '</div>';
echo '<div id="loginbox">
<form id="loginform" class="form-vertical" action="" method="GET"/>
<input type="hidden" name="action" value="login">
<p>Enter username and password to continue.</p>
<div class="control-group">
<div class="controls">
<div class="input-prepend">
<span class="add-on"><i class="fa fa-user"></i></span><input type="text" name="user" placeholder="Username" value="'. $user .'"/>
</div>
</div>
</div>
<div class="control-group">
<div class="controls">
<div class="input-prepend">
<span class="add-on"><i class="fa fa-lock"></i></span><input type="password" name="pass" placeholder="Password" value="'. $pass .'"/>
</div>
</div>
</div>
<div class="form-actions">
<span class="small pull-left" style="margin-top: -5px;"><strong>Username</strong>: '. $user .'</span><br/>
<span class="small pull-left" style="margin-top: -5px;"><strong>Password</strong>: '. $pass .'</span>
<span class="pull-right" style="margin-top: -20px;"><input type="submit" class="btn btn-inverse" value="Login"/></span>
</div>
</form>';
echo '</div>';	
}

function hackBox($bfver, $ftpver, $sshver){

echo '<div class="span12 center" style="text-align: center;">
Choose your attack method:<br/>
<ul class="quick-actions">
<li>
<a href="?action=hack&method=bf">
<i class="icon- he32-bruteforce"></i>
Bruteforce attack <br/>
<span class="he16-bruteforce"></span>
'. $bfver .'
</a>
</li>
<li>
<a href="?action=hack&method=xp">
<i class="icon- he32-exploit"></i>
Exploit attack <br/>
<span class="he16-ftp"></span> '. $ftpver .' <span class="he16-ssh"></span> '. $sshver .'&nbsp;&nbsp;
</a>
</li>
</ul>
</div>';	
	echo '</div>';
}

function navTab($active){
echo '<div class="widget-title">';
echo '<ul class="nav nav-tabs">';
if($active==""){ echo '<li class="link active">'; } else { echo '<li class="link">'; }
echo '<a href="internet"><span class="he16-index icon-tab"></span></span>Index</a></li>';
if($active=="login"){ echo '<li class="link active">'; } else { echo '<li class="link">'; }
echo '<a href="?action=login"><span class="he16-login icon-tab"></span></span>Login</a></li>';
if($active=="hack"){ echo '<li class="link active">'; } else { echo '<li class="link">'; }
echo '<a href="?action=hack"><span class="icon-tab he16-internet_hack"></span>Hack</a></li>';

echo '</ul></div>';	
}

function inputBox($default_ip){
echo '<div class="widget-box">
<div class="widget-content padding">
<div class="span12">
<form action="" method="get" class="form-horizontal">
<div class="browser-input">
IP address: <input class="browser-bar" name="ip" type="text" value="'. $default_ip .'"/>
<input type="submit" class="btn btn-inverse" value="Go">
<div style="float: right; padding-top: 5px;" class="hide-phone">
<a href="internet?ip=1.2.3.4"><span class="he16-home" title="Home" style="margin-top: 4px;"></span></img></a>
<a href="internet"><span class="he16-refresh" title="Refresh" style="margin-top: 4px;"></span></a>
</div>
</div>
</form>
</div>
<div style="clear: both;"></div>
</div>
</div>';
}

function error404(){
echo '404 - Page not found<br/>
This ip does not exists.<br/><br/>
<a href="internet?ip=1.2.3.4">Back to First Whois</a>';	
echo '</div>';
}

function printIP($data){
echo '<div class="span12">
<p>Server '. $data["ip"] .' is <font color="green"><b>online</b></font>';
if($data['extra']=="Whois"){ echo '<span class="label label-warning pull-right">Whois</span></p>'; }
if($data['extra']=="NPC"){ echo '<span class="label label-info pull-right">NPC</span></p>'; }
if($data['extra']=="VPC"){ echo '<span class="label label-success pull-right">VPC</span></p>'; }
if($data['extra']=="Bank"){ echo '<span class="label label-inverse pull-right">Bank</span></p>'; }
if($data['extra']=="Bitcoin Market"){ echo '<span class="label label-inverse pull-right">Bitcoin Market</span></p>'; }
if($data['extra']=="Important"){ echo '<span class="label label-important pull-right">Important</span></p>'; }
echo $data['text'];
echo '</div>';
echo '</div>';
}

?>