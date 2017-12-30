<?php
$site_name = "HEXClone";
error_reporting(E_ALL & ~E_NOTICE);

function head($page){
echo '
<!DOCTYPE html>
<html lang="en"><head>
<title>'. $page . ' - ' . $GLOBALS["site_name"] .'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
<link rel="stylesheet" href="css/tipTip.css"/>
<link rel="stylesheet" href="css/he_login.css"/>
<link rel="stylesheet" href="css/select2.css"/>
<link rel="stylesheet" href="css/he.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css"/>
</head>';	
}

function sidebar(){
echo '<div id="sidebar"><ul>';
add_menu_item("index","Home","fa fa-inverse fa-home");
add_menu_item("processes","Task Manager","fa fa-inverse fa-tasks");
add_menu_item("software","Software","fa fa-inverse fa-folder-open");
add_menu_item("internet","Internet","fa fa-inverse fa-globe");
add_menu_item("log","Log File","fa fa-inverse fa-book");
add_menu_item("hardware","Hardware","fa fa-inverse fa-desktop");
add_menu_item("university","University","fa fa-inverse fa-flask");
add_menu_item("finances","Finances","fa fa-inverse fa-briefcase");
add_menu_item("list","Hacked Database","fa fa-inverse fa-terminal");
add_menu_item("mission","Missions","fa fa-inverse fa-building-o");
add_menu_item("clan","Clan","fa fa-inverse fa-users");
add_menu_item("ranking","Ranking","fa fa-inverse fa-bars");
add_menu_item("fame","Hall of Fame","fa fa-inverse fa-star");
echo '</ul></div>';
}

function user_navbar(){
echo '<div id="user-nav" class="navbar navbar-inverse">
<ul class="nav btn-group">
<li class="btn btn-inverse"><a href="profile"><i class="fa fa-inverse fa-user"></i> <span class="text">My Profile</span></a></li>
<li class="btn btn-inverse"><a href="mail"><i class="fa fa-inverse fa-envelope"></i> <span class="text">E-Mail</span> <span class="mail-unread"></span></a></li>
<li class="btn btn-inverse"><a href="settings"><i class="fa fa-inverse fa-wrench"></i> <span class="text">Settings</span></a></li>
<li class="btn btn-inverse"><a href="logout"><i class="fa fa-power-off fa-inverse"></i> <span class="text">Logout</span></a></li>
</ul>
</div>
<span id="notify"></span>';
}

function header_info($page){
echo '<div id="content-header">
<h1>'. $page .'</h1>
<div class="header-ip hide-phone">
<div style="text-align: right;">
<span class="header-ip-show"></span>
</div>
<div class="header-info">
<div class="pull-right">
<span class="icon-tab he16-time" title="Server Time"></span> <span class="small nomargin" style="margin-right: 7px;">2017-05-22 09:14</span>
<span class="online"></span>
<div class="reputation-info"></div><div class="finance-info"></div>
</div>
</div>
</div>
</div>';
}

function unk_header(){
echo '<div id="header">
<h1><a href="#">Hacker Experience</a></h1>
</div>';	
}


function footer($extra=''){
echo '<div id="breadcrumb" class="center">
<span class="pull-left hide-phone" style="margin-left: 10px;"><a href="legal"><font color="">Terms of Use</font></a></span>
<span class="pull-left hide-phone"><a href="https://discord.me/hackerexperience"><font color="">Discord</font></a></span>
<span class="pull-left hide-phone"><a href="stats">Stats</a></span>         
<span class="center">2016 &copy; <b>NeoArt Labs</b><a href="https://status.hackerexperience.com/">21 queries in 6 ms</a></span>
<span id="credits" class="pull-right hide-phone link"><a>Credits</a></span>
<span id="report-bug" class="pull-right hide-phone link"><a>Report Bug</a></span>
<span class="pull-right hide-phone"><a href="premium"><font color="">Premium</font></a></span>
<span class="pull-right hide-phone"><a href="https://leaks.hackerexperience.com/announcing-hacker-experience-2/">HE2</a></span>
</div><!--[if IE]><script src="js/excanvas.min.js"></script><![endif]-->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.flot.min.js"></script>  
<script src="js/jquery.validate.js"></script>
<script src="js/jquery.ui.custom.js"></script>';
echo $extra;
echo '<script src="js/main.js"></script>
</body>
</html>';


}

function add_menu_item($name, $title, $icon){
echo '<li id="'. $id = 'menu-' . $name .'"'; if($_SESSION["current-site"]==$name){ echo ' class="active"'; } echo '><a href="'. $name .'"><i class="'. $icon .'"></i> <span>'. $title .'</span></a></li>'; } 
?>