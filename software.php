<?php
require_once('module/template.php');
require_once('module/software/s-design.php');
$_SESSION["current-site"] = basename(__FILE__, '.php');
$name = "Software";

$size = 0;
$tot_size=10000;
$size_used=0;

head($name); 
echo'<body class="software file-actions pie">';
unk_header();
user_navbar();
sidebar();
echo'<div id="content">';
header_info($name);
breadcrumb();
echo'<div class="container-fluid"><div class="row-fluid"><div class="span12"><div class="widget-box">';
nav_tabs();

if($data = dbGetServerHDD($conn, 20)){
$size_used = printAllSoftwares($data);
} else {
no_software();
}

softwarebar($tot_size, $size_used);
echo'<div style="clear: both;" class="nav nav-tabs">&nbsp;</div></div></div></div></div><div class="center" style="margin-bottom: 20px;"></div>';    
footer();
?>







