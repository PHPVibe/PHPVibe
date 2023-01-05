<?php
if(isset($_GET['delete-video'])) {
unpublish_video(intval($_GET['delete-video']));
echo '<div class="msg-info">Video #'.intval($_GET['delete-video']).' was unpublished.</div>';
} 
if(isset($_GET['feature-video'])) {
$id = intval($_GET['feature-video']);
if($id){
$db->query("UPDATE ".DB_PREFIX."videos set featured = '1' where id='".intval($id)."'");
echo '<div class="msg-info">Video #'.$id.' was featured.</div>';
}
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
unpublish_video(intval($del));
}
echo '<div class="msg-info">Videos #'.implode(',', $_POST['checkRow']).' unpublished.</div>';
}
$key = (isset($_GET['key'])) ? $_GET['key'] : $_POST['key'];
if(!$key || empty($key) ) {
echo "Please use the search form to find a video.";
} else {
$options = DB_PREFIX."videos.id,".DB_PREFIX."videos.title,".DB_PREFIX."videos.featured,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
       $vq = "select #what#, ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id 
	WHERE ( ".DB_PREFIX."videos.title like '%".$key."%' or ".DB_PREFIX."videos.description like '%".$key."%' or ".DB_PREFIX."videos.tags like '%".$key."%' ) and pub >  0
	   ORDER BY CASE WHEN ".DB_PREFIX."videos.title like '" .$key. "%' THEN 0
	           WHEN ".DB_PREFIX."videos.title like '%" .$key. "%' THEN 1
	           WHEN ".DB_PREFIX."videos.tags like '" .$key. "%' THEN 2
               WHEN ".DB_PREFIX."videos.tags like '%" .$key. "%' THEN 3		   
               WHEN ".DB_PREFIX."videos.description like '%" .$key. "%' THEN 4
			   WHEN ".DB_PREFIX."videos.tags like '%" .$key. "%' THEN 5
               ELSE 6
          END, title ";
$count = $db->get_row(str_replace("#what#", "count(*) as nr", $vq));
$videos = $db->get_results(str_replace("#what#", $options, $vq.this_limit()));

if($videos) {

$ps = admin_url('search-videos').'&key='.$key.'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
?>
<div class="row">

<div class="block full mar20_top mar10_bottom">
 <ul class="nav nav-tabs nav-tabs-line">
 <li class="disabled" role="presentation"><a href="javascript:void(0)">#<?php echo $key; ?></a></li>
                    <li class="active"><a href="<?php echo $ps;?>"> Videos & Music</a></li>
                   <li><a href="<?php echo str_replace('sk=search-videos','sk=search-images',canonical());?>">Images</a></li>
				   </ul>

</div>
<form class="form-horizontal styled" action="<?php echo $ps;?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
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
								  <th><button class="btn btn-large btn-danger" type="submit"><?php echo _lang("Unpublish selected"); ?></button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($videos as $video) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $video->id; ?>" class="styled" /></td>
                                  <td><img src="<?php echo thumb_fix($video->thumb); ?>" style="width:130px; height:90px;"></td>
                                  <td><?php echo _html($video->title); ?></td>
                                  <td><?php echo video_time($video->duration); ?></td>
                                  <td><?php echo _html($video->liked); ?></td>
                                  <td><?php echo _html($video->views); ?></td>
								  <td>
								  <div class="btn-group">
								  <a class="btn btn-sm btn-outline btn-danger" href="<?php echo $ps;?><?php echo this_page();?>&delete-video=<?php echo $video->id;?>"><i class="icon-trash" style="margin-right:5px;"></i></a>
								  <a class="btn btn-sm btn-outline btn-info" href="<?php echo admin_url('edit-video');?>&vid=<?php echo $video->id;?>"><i class="icon-edit" style="margin-right:5px;"></i><?php echo _lang("Edit"); ?></a>
								  <?php if($video->featured < 1) { ?>
								<a class="btn btn-sm btn-outline btn-default" href="<?php echo $ps; ?><?php echo this_page();?>&feature-video=<?php echo $video->id;?>" title="Feature"><i class="icon-star" style="margin-right:5px;"></i></a>
								 <?php } else { ?>
								<a class="btn btn-sm btn-outline btn-info" href="<?php echo admin_url('videos');?>&p=<?php echo this_page();?>&feature-video=<?php echo $video->id;?>" title="Unfeature"><i class="icon-star-half" style="margin-right:5px;"></i></a>
								 <?php } ?>
								 <a class="btn btn-sm btn-outline btn-primary" target="_blank" href="<?php echo video_url($video->id, $video->title);?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("View"); ?></a></div>
								  </td>
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</fieldset>					
</form>
<?php  $a->show_pages($ps); }

} ?>
</div>