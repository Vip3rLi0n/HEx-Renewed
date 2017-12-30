<?php
require_once('module/template.php');
require_once('module/hardware/h-design.php');
$_SESSION["current-site"] = basename(__FILE__, '.php');
$name = "Hardware";

head($name);
echo '<body class="hardware">';
unk_header();
user_navbar();
sidebar(); 
echo '<div id="content">';
header_info($name);
breadcrumb();

echo'<div class="container-fluid">';

echo'<div class="row-fluid">';
echo'<div class="widget-box">';

echo'</div>';

echo'<div style="clear: both;" class="nav nav-tabs"></div>';

echo'</div>';
echo'</div>';


echo '<div style="clear: both;" class="nav nav-tabs">&nbsp;</div>';
echo "<script>var indexdata={ip:'27.63.173.42',pass:'7tzAHsgB',up:'5 hours',chg:'change'};</script>";
echo '<span id="modal"></span>';

footer(); 
?>
