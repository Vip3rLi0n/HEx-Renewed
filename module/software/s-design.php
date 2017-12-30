<?php
require_once('function.php');

function breadcrumb(){
echo '<div id="breadcrumb">
<a href="index" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Home</a>
<a href="software" id="link0" class="current"> Software</a>
</div>';
}

function table_head(){
echo '<div class="widget-content padding noborder">
<div class="span9">
<table class="table table-cozy table-bordered table-striped table-software table-hover with-check">
<thead>
<tr>
<th></th>
<th>Software name</th>
<th>Version</th>
<th class="hide-phone">Size</th>
<th>Actions</th>
</tr>
</thead>';	
}

function link_format_hd(){
echo '<li>
<a class="link create-txt">
<i class="icon-" style="background-image: none;"><span class="heicon he32-text_create"></span></i>
New text file </a>
</li>';
}

function link_create_folder(){
echo '<li>
<a class="link create-folder">
<i class="icon-" style="background-image: none;"><span class="heicon he32-folder_create"></span></i>
Create folder </a>
</li>';
}

function link_create_txt(){
echo '<li><a class="link hd-format">
<i class="icon-" style="background-image: none;"><span class="heicon he32-previous"></span></i>
Format HD </a>
</li>';
}

function nav_tabs(){
//<a href="software?action=folder&view=10819055"><span class="icon-tab he16-31"></span>Attack</a></li> Folder Tab
echo '<div class="widget-title">
<ul class="nav nav-tabs">
<li class="link  active"><a href="software.php"><span class="icon-tab he16-software"></span></span>Softwares</a></li>
<li class="link "><a href="?page=external"><span class="icon-tab he16-xhd"></span><span class="hide-phone">External HD</span></a></li>
<a href="https://wiki.hackerexperience.com/en:softwares"><span class="label label-info">Help</span></a>
</ul></div>';
}

function widget_title(){
echo '<div class="widget-title">
<span class="icon"><i class="fa fa-arrow-right"></i></span>
<h5>Folder <b>Other</b></h5>
</div>';
}


function software_hdd_usage($size_hdd, $size_used){
$perc = ($size_used / $size_hdd) * 100; if($size_hdd>1024 || $size_used>1024){ $size_hdd /= 1024; $size_used /= 1024; $type = "GB"; } else { $type = "MB"; } 
echo '<div class="hd-usage">
<div class="chart easyPieChart chartpie"  data-percent="'. $perc .'">
<div id="downmeplz"><span id="percentpie"></span></div>
</div>
<div class="hd-usage-text">HDD Usage</div>
<span class="small"><font color="green">'. (number_format($size_used,1) + 0) . ' ' . $type .'</font> / <font color="red">'. (number_format($size_hdd,1) + 0) . ' ' . $type  .'</font></span>
</div>';
}

function no_software(){
echo '
<div class="widget-content padding noborder">
<div class="span9">
<br><center>There are no softwares to display.</center>';
echo "<script>window.onload=function(){". chr(36) ."('.table-software').replaceWith('<br/><center>There are no softwares to display.</center>');}</script></div></div>";
}

function printAllSoftwares($data){
$size=0;
table_head();
echo'<tbody>';

for ($x = 0; $x < sizeof($data); $x++) 
{ 
$size =+ printSoftware($data[$x]); 
} 

echo '</tbody></table></div></div>';
return $size;
}

function softwarebar($tot_size, $size){
echo '<div class="span3" style="text-align: center;">
<div id="softwarebar"><ul class="soft-but">';
software_hdd_usage($tot_size, $size);
link_create_txt();
link_create_folder();
link_format_hd();
echo '</ul><span id="modal"></span>
<br/>
</div>
</div>';
}
?>