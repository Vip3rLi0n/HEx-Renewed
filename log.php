<?php
require_once('module/template.php');
require_once('module/log/log-design.php');

$_SESSION["current-site"] = basename(__FILE__, '.php');
$name = "Log File"; 

head($name);
echo '<body class="page-log">';
unk_header();
user_navbar();
sidebar(); 
echo '<div id="content">';
header_info($name);
breadcrumb();

echo'<div class="container-fluid">';
echo'<div class="row-fluid">';
echo'<div class="span12">';
echo'<div class="widget-box" style="width:100%">';
widget_title();
echo'
<div class="widget-content padding noborder center">
<div class="span2 center">
<style type="text/css">@media (min-width:320px){.logleft{display:none}}@media (min-width:360px) and (max-width:480px){.adslot_log{width:300px;height:250px;display:none}.logleft{display:none}}@media (min-width:768px) and (max-width:1024px){.adslot_log{width:336px;height:280px;display:none}.logleft{display:block!important}}@media (min-width:1024px){.adslot_log{width:120px;height:240px;margin-top:50px}.logleft{display:block!important}}@media (min-width:1280px){.adslot_log{width:120px;height:240px;margin-top:50px}.logleft{display:block!important}}@media (min-width:1366px){.adslot_log{width:120px;height:240px;margin-top:50px}.logleft{display:block!important}}@media (min-width:1824px){.adslot_log{width:160px;height:600px;margin-top:8px}.logarea{height:500px}.logleft{display:block!important}}</style>
</div>
<div class="span8 center">';
showLogArea(getLog(connect(), $_SESSION["uid"]),1);
echo'<br/>
</div>
</div>';
echo'<div class="nav nav-tabs" style="clear: both;"></div>';
echo'</div>';
echo'</div>';
echo'</div>';
echo'</div>';
footer(); 
?>
