<?php
if(isset($_GET['delete-video'])) {
delete_video(intval($_GET['delete-video']));
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
delete_video(intval($del));
}
echo '<div class="msg-info">Videos #'.implode(',', $_POST['checkRow']).' removed permanently.</div>';
}
if(isset($_GET['pub-video'])) {
publish_video(intval($_GET['pub-video']));
echo '<div class="msg-info">Video #'.$_GET['pub-video'].' published.</div>';
} 
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."videos where pub < 1");
$videos = $db->get_results("select id,title,thumb, views, liked, duration from ".DB_PREFIX."videos where pub < 1 ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit()."");
if($videos) {
$ps = admin_url('unvideos').'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
?>

<div class="block full mar20_top mar10_bottom">
 <ul class="nav nav-tabs nav-tabs-line">
                    <li class="active"><a href="<?php echo admin_url('unvideos');?>">Videos</a></li>
                   <li><a href="<?php echo admin_url('unimages');?>">Images</a></li>
				   </ul>

</div>
<form class="row full" action="<?php echo admin_url('unvideos');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">


<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
<th> <div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>  </th>
                                  <th width="130px"><?php echo _lang("Thumb"); ?></th>
                                  <th width="35%"><?php echo _lang("Video"); ?></th>
                                  <th><?php echo _lang("Duration"); ?></th>
                                  <th><?php echo _lang("Likes"); ?></th>
                                  <th><?php echo _lang("Views"); ?></th>
								  <th><button class="btn btn-large btn-danger" type="submit"><?php echo _lang("Permanently delete videos"); ?></button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($videos as $video) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $video->id; ?>" class="styled" /></td>
                                  <td><img src="<?php echo thumb_fix($video->thumb); ?>" style="width:130px; height:90px;"></td>
                                  <td><?php echo stripslashes($video->title); ?></td>
                                  <td><?php echo video_time($video->duration); ?></td>
                                  <td><?php echo stripslashes($video->liked); ?></td>
                                  <td><?php echo stripslashes($video->views); ?></td>
								  <td>
								  <p><a class="confirm" href="<?php echo admin_url('unvideos');?>&p=<?php echo this_page();?>&delete-video=<?php echo $video->id;?>"><i class="icon-trash" style="margin-right:5px;"></i><?php echo _lang("Permanently delete video"); ?></a></p>
								  <p><a href="<?php echo admin_url('unvideos');?>&p=<?php echo this_page();?>&pub-video=<?php echo $video->id;?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("Publish video"); ?></a></p>
								  <p><a href="<?php echo admin_url('edit-video');?>&vid=<?php echo $video->id;?>"><i class="icon-edit" style="margin-right:5px;"></i><?php echo _lang("Edit"); ?></a></p>
								 <p> <a target="_blank" href="<?php echo video_url($video->id, $video->title);?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("View"); ?></a></p>
								  </td>
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</fieldset>					
</form>
<?php  $a->show_pages($ps); } 
else {
echo '<div class="msg-note">Nothing here yet.</div>';
}
?>
