<?php
if(isset($_GET['delete-image'])) {
unpublish_image(intval($_GET['delete-image']));
} 
if(isset($_GET['feature-image'])) {
$id = intval($_GET['feature-image']);
if($id){
$db->query("UPDATE ".DB_PREFIX."images set featured = '1' where id='".intval($id)."'");
echo '<div class="msg-info">Image #'.$id.' was featured.</div>';
}
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
unpublish_image(intval($del));
}
echo '<div class="msg-info">Images #'.implode(',', $_POST['checkRow']).' unpublished.</div>';
}
$key = (isset($_GET['key'])) ? $_GET['key'] : $_POST['key'];
if(!$key || empty($key) ) {
echo "Please use the search form to find a image.";
} else {
$options = DB_PREFIX."images.id,".DB_PREFIX."images.title,".DB_PREFIX."images.featured,".DB_PREFIX."images.user_id,".DB_PREFIX."images.thumb,".DB_PREFIX."images.views,".DB_PREFIX."images.liked";
       $vq = "select #what#, ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id 
	WHERE ( ".DB_PREFIX."images.title like '%".$key."%' or ".DB_PREFIX."images.description like '%".$key."%' or ".DB_PREFIX."images.tags like '%".$key."%' )
	   ORDER BY CASE WHEN ".DB_PREFIX."images.title like '" .$key. "%' THEN 0
	           WHEN ".DB_PREFIX."images.title like '%" .$key. "%' THEN 1
	           WHEN ".DB_PREFIX."images.tags like '" .$key. "%' THEN 2
               WHEN ".DB_PREFIX."images.tags like '%" .$key. "%' THEN 3		   
               WHEN ".DB_PREFIX."images.description like '%" .$key. "%' THEN 4
			   WHEN ".DB_PREFIX."images.tags like '%" .$key. "%' THEN 5
               ELSE 6
          END, title ";
$count = $db->get_row(str_replace("#what#", "count(*) as nr", $vq));
$images = $db->get_results(str_replace("#what#", $options, $vq.this_limit()));
if($images) {

$ps = admin_url('search-images').'&key='.$key.'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
?>
<div class="block full mar20_top mar10_bottom">
 <ul class="nav nav-tabs nav-tabs-line">
 <li class="disabled" role="presentation"><a href="javascript:void(0)">#<?php echo $key; ?></a></li>
                    <li><a href="<?php echo str_replace('sk=search-images','sk=search-videos',canonical());?>"> Videos & Music</a></li>
                   <li class="active"><a href="<?php echo canonical();?>">Images</a></li>
				   </ul>
</div>


<div class="row">
<form class="form-horizontal styled" action="<?php echo $ps; ?><?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
<th> <div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>  </th>
                                  <th width="130px"><?php echo _lang("Thumb"); ?></th>
                                  <th width="35%"><?php echo _lang("Image"); ?></th>
                                  <th><?php echo _lang("Likes"); ?></th>
                                  <th><?php echo _lang("Views"); ?></th>
								  <th><button class="btn btn-large btn-danger" type="submit"><?php echo _lang("Unpublish selected"); ?></button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($images as $image) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $image->id; ?>" class="styled" /></td>
                                  <td><img src="<?php echo thumb_fix($image->thumb); ?>" style="width:130px; height:90px;"></td>
                                  <td><?php echo _html($image->title); ?></td>
                                  <td><?php echo _html($image->liked); ?></td>
                                  <td><?php echo _html($image->views); ?></td>
								  <td>
								  <div class="btn-group"><a class="btn btn-sm btn-outline btn-danger" href="<?php echo admin_url('images');?>&p=<?php echo this_page();?>&delete-image=<?php echo $image->id;?>"><i class="icon-trash" style="margin-right:5px;"></i></a>
								  <a class="btn btn-sm btn-outline btn-info" href="<?php echo admin_url('edit-image');?>&vid=<?php echo $image->id;?>"><i class="icon-edit" style="margin-right:5px;"></i><?php echo _lang("Edit"); ?></a>
								  <?php if($image->featured < 1) { ?>
								<a class="btn btn-sm btn-outline btn-default" href="<?php echo admin_url('images');?>&p=<?php echo this_page();?>&feature-image=<?php echo $image->id;?>" title="Feature"><i class="icon-star" style="margin-right:5px;"></i></a>
								 <?php } else { ?>
								<a class="btn btn-sm btn-outline btn-info" href="<?php echo admin_url('images');?>&p=<?php echo this_page();?>&feature-image=<?php echo $image->id;?>" title="Unfeature"><i class="icon-star-half" style="margin-right:5px;"></i></a>
								 <?php } ?>
								 <a class="btn btn-sm btn-outline btn-primary" target="_blank" href="<?php echo image_url($image->id, $image->title);?>"><i class="icon-check" style="margin-right:5px;"></i><?php echo _lang("View"); ?></a></div>
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