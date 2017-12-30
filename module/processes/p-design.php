<?php
require_once('function.php');


function breadcrumb(){
echo '<div id="breadcrumb">
<a href="index" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Home</a>
<a href="software" id="link0" class="current"> Task manager</a>
</div>';
}

function nav_tabs(){
echo '<ul class="nav nav-tabs">
<li class="link"><a href="?page=all"><span class="icon-tab he16-taskmanager"></span>All tasks</a>
</li>
<li class="hide-phone link active"><a href="?page=cpu"><span class="icon-tab he16-tasks_cpu"></span>CPU tasks</a>
</li>
<li class="hide-phone link"><a href="?page=network"><span class="icon-tab he16-tasks_download"></span>Download manager</a>
</li>
<li class="link"><a href="?page=running"><span class="he16-running icon-tab"></span><span class="hide-phone">Running softwares</span></a>
</li>
<a href="https://wiki.hackerexperience.com/en:processes"><span class="label label-info">Help</span></a>
</ul>';
}

function proc_header($id,$text){
echo '<li><div class="span4 process'. $id .'">
<div class="proc-desc">'. $text .'</div>
</div>';	
}

function proc_body($id){
echo '<div class="span5">
<div id="process'. $id .'">
<div class="percent"></div>
<div class="pbar"></div>
<div class="elapsed"></div>
</div>
</div>';
}

function proc_footer($id){
echo '<div class="span3 proc-action">
<div class="span6"> <span class="he16-cpu"></span> <span class="small nomargin">'. (number_format(100/getRunningTasks($_SESSION["uid"]),1)+0) . '%' .'</span><br/></div>
<div class="span6" style="text-align: right;"><a href="processes?pid='. $id .'&action=pause"><span class="he16-play heicon"></span></a><a href="processes?pid='. $id .'&del=1"><span class="he16-cancel heicon"></span></a><br/>
<span id="complete'. $id .'"></span>
</div>
</div>
<div style="clear:both;"></div></li>';
}


function addProcces($id,$time,$text){
proc_header($id,$text);
proc_body($id);
proc_footer($id);

return <<< BAR
var iNow=new Date().setTime(new Date().getTime()-1);var iEnd=new Date().setTime(new Date().getTime()+$time*1000);
$('#process$id').anim_progressbar({start:iNow,finish:iEnd,interval:100,id:$id,loaded:true});
BAR;
}

?>