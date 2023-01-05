<?php
if(isset($_GET['delete-image'])) {
delete_image(intval($_GET['delete-image']));
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
delete_image(intval($del));
}
echo '<div class="msg-info">Images #'.implode(',', $_POST['checkRow']).' removed permanently.</div>';
}
if(isset($_GET['pub-image'])) {
publish_image(intval($_GET['pub-image']));
echo '<div class="msg-info">Video #'.$_GET['pub-image'].' published.</div>';
} 
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."images where pub < 1");
$images = $db->get_results("select id,title,source, views, liked from ".DB_PREFIX."images where pub < 1 ORDER BY ".DB_PREFIX."images.id DESC ".this_limit()."");
//$db->debug();
if($images) {
$ps = admin_url('unimages').'&p=';
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
                <li class="active"><a href="<?php echo admin_url('unimages');?>">Images</a></li>
				<li><a href="<?php echo admin_url('unvideos');?>">Videos</a></li>
				 </ul>
</div>
<form class="form-horizontal styled" action="<?php echo admin_url('unimages');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
<th> <div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>  </th>
                                  <th width="130px"><?php echo _lang("Thumb"); ?></th>
                                  <th width="35%"><?php echo _lang("Video"); ?></th>
                                  <th><?php echo _lang("Likes"); ?></th>
                                  <th><?php echo _lang("Views"); ?></th>
								  <th><button class="btn btn-large btn-danger" type="submit"><?php echo _lang("Permanently delete images"); ?></button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($images as $image) {						$image->thumb = site_url().'storage/'.get_option('mediafolder').'/pictures/thumbs/'. $image->source;							  ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $image->id; ?>" class="styled" /></td>
                                  <td><img src="<?php echo thumb_fix($image->thumb); ?>" style="width:130px; height:90px;"></td>
                                  <td><?php echo stripslashes($image->title); ?></td>
                                  <td><?php echo stripslashes($image->liked); ?></td>
                                  <td><?php echo stripslashes($image->views); ?></td>
								  <td>
								  <p><a class="confirm" href="<?php echo admin_url('unimages');?>&p=<?php echo this_page();?>&delete-image=<?php echo $image->id;?>"><i class="icon-trash" style="margin-right:5px;"></i><?php echo _lang("Permanently delete image"); ?></a></p>
								  <p><a href="<?php echo admin_url('unimages');?>&p=<?php echo this_page();?>&pub-image=<?php echo $image->id;?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("Publish image"); ?></a></p>
								  <p><a href="<?php echo admin_url('edit-image');?>&vid=<?php echo $image->id;?>"><i class="icon-edit" style="margin-right:5px;"></i><?php echo _lang("Edit"); ?></a></p>
								 <p> <a target="_blank" href="<?php echo image_url($image->id, $image->title);?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("View"); ?></a></p>
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
