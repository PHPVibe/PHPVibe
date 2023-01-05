<?php
if(isset($_GET['delete-channel'])) {
$db->get_row("DELETE from ".DB_PREFIX."postcats where cat_id ='".intval($_GET['delete-channel'])."' ");
echo '<div class="msg-info">Channel #'.$_GET['delete-channel'].' deleted.</div>';
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
$db->get_row("DELETE from ".DB_PREFIX."postcats where cat_id ='".intval($del)."' ");
}
echo '<div class="msg-info">Channels #'.implode(',', $_POST['checkRow']).' deleted.</div>';
}

$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."postcats");
$channels = $db->get_results("select ".DB_PREFIX."postcats.* from ".DB_PREFIX."postcats order by cat_id DESC ".this_limit()."");
if($channels) {
$ps = admin_url('pch').'&p=';

$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
?><div class="row bottom20"><h3>Blog categories<a class="pull-right btn btn-success inline-block" href="<?php echo admin_url('create-pch'); ?>"> Create category</a>	</h3></div>
<form class="form-horizontal styled" action="<?php echo admin_url('pch');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
<div class="cleafix full"></div>
<fieldset>
<div class="cleafix full"></div>
<div class="panel top10 multicheck">
<fieldset>
<div class="panel-heading">
<h3 class="panel-title">
<i class="material-icons">
list_alt
</i>
</h3>
<ul class="panel-actions">
    <li class="chbox">
	<div class="checkbox-custom checkbox-primary nopad"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>
	</li>
	<li>
<button class="btn btn-large btn-danger" type="submit"><?php echo _lang("Delete selected"); ?></button>
</li>
</ul>
</div>

<div class="panel-body" style="border-top: 1px solid #e4eaec; padding-top:15px;">
 <div class="multilist">
<ul class="list-group">
            
						  <?php 
						 						  
						  
						  foreach ($channels as $video) { ?>
                             <li class="list-group-item">
	                         <div class="row">
                                   <div class="inline-block img-hold">
							      <div class="inline-block right20 img-checker">
								  <span class="pull-left mg-t-xs mg-r-md top20">
								 <input type="checkbox" name="checkRow[]" value="<?php echo $video->cat_id; ?>" class="styled" /></td>
                                  </span>
								  <img class="row-image" data-name="<?php echo $video->cat_name; ?>" src="<?php echo thumb_fix($video->picture); ?>" style="width:130px; height:90px;">
								  </div>
								  <div class="inline-block right20 img-txt">
                                <h4><?php echo _html($video->cat_name); ?></h4>								  
								  <small><?php echo stripslashes($video->cat_desc); ?></small>
								  </div>
                                  </div>
								  <div class="btn-group btn-group-vertical pull-right">
								  <a class="btn btn-sm btn-outline btn-success" target="_blank" href="<?php echo bc_url($video->cat_id, $video->cat_name);?>">
								<i class="material-icons mright10"> pageview </i> view
								  </a>
								  <a class="btn btn-sm btn-raised btn-primary" href="<?php echo admin_url('edit-pch');?>&id=<?php echo $video->cat_id;?>">
								<i class="material-icons mright10"> edit </i> modify
								  </a>				 
								  <a class="btn btn-sm btn-outline btn-default" href="<?php echo admin_url('pch');?>&p=<?php echo this_page();?>&delete-channel=<?php echo $video->cat_id;?>">
		                        <i class="material-icons mright10"> delete </i>  delete
								  </a>
								  </div>
								 
                              </div>
							  </li>
							  <?php } ?>
						</div>  
</div>
					
</fieldset>					
</form>
<?php  $a->show_pages($ps); }else {
echo '<div class="msg-note">Nothing here yet.</div>';
}
?>
</div>	