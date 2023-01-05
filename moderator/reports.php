<?php 
if(isset($_GET['delete'])) {
$db->query( "DELETE FROM  ".DB_PREFIX."reports WHERE r_id = '".$_GET['delete']."'" );
echo '<div class="msg-info">Report #'.$_GET['delete'].' removed.</div>';
} 
if(isset($_GET['unvid'])) {
unpublish_video(intval($_GET['unvid']));
echo '<div class="msg-info">Video #'.intval($_GET['unvid']).' unpublished.</div>';
}
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."reports");
$options = DB_PREFIX."videos.id,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
$vq = $db->get_results("select ".$options.", ".DB_PREFIX."reports.*,".DB_PREFIX."users.name   FROM ".DB_PREFIX."reports LEFT JOIN ".DB_PREFIX."videos ON ".DB_PREFIX."reports.vid = ".DB_PREFIX."videos.id LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."reports.uid = ".DB_PREFIX."users.id ORDER BY ".DB_PREFIX."reports.r_id DESC ".this_limit()."");
if($vq) {
$ps = admin_url('reports').'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
echo '
<div class="row">
<div class="box-element col-md-12">
<div class="box-head-light"><h3><i class="icon-film"></i><h3>Reported videos</h3></div>
<div class="box-content">
<ul>';
foreach ($vq as $video) {
?>
<li style="border-bottom: 1px solid #D5DAE0; padding-bottom:10px; margin-bottom:10px;">
<div class="avatar"><img alt="" src="<?php echo thumb_fix($video->thumb)?>"></div>
<div class="info">
<a href="<?php echo video_url($video->id , $video->title); ?>"><?php echo  _html(_cut($video->title, 46)); ?></a> <br>
<div class="pull-right btn-group">
<a href="<?php echo admin_url('reports'); ?>&delete=<?php echo $video->r_id; ?>" class="btn btn-warning"><b class="icon icon-remove"></b> Close Report</a>
<a href="<?php echo admin_url('edit-video'); ?>&vid=<?php echo $video->vid; ?>" class="btn btn-primary"><b class="icon icon-pencil"></b> Edit Video</a>
<a href="<?php echo admin_url('reports'); ?>&unvid=<?php echo $video->vid; ?>&delete=<?php echo $video->r_id; ?>" class="btn btn-danger"><b class="icon icon-trash"></b> Unpublish Video</a>
</div>
<?php echo _lang("Reported by").' <a href="'.profile_url($video->uid, $video->name).'" title="'.$video->name.'">'.$video->name.'</a>';
$report = maybe_unserialize($video->reason);
if(is_array($report)) {
echo ' <p> <span class="label label-primary">Reported for:</span> '.implode(",",$report).'</p>';
} else {
echo ' <p> <span class="label label-primary">Reported for:</span> '.$video->report.'</p>';
}
echo '  <code>Message:</code> '.$video->motive;
?>


</div>
</li>  
<?php } 
} else {
echo '<div class="msg-note">Nothing here yet.</div>';
}
?>                               
 </ul>
 </div>
</div>				
</div>
<?php //End ?>
