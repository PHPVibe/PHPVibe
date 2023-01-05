<?php function add_sort($sorter){
	global $ps;
	if($sorter == "featured") {		
	return str_replace('&sort=','&sort='.$sorter.';',$ps);
	}
	return admin_url('images').'&sort='.$sorter.'&p=1';
}
function remove_sort($sorter){
	global $ps;
	return str_replace($sorter.'','',$ps);
}
function get_domain($url)
{
	if ((strpos($url,'localfile') !== false) || ($url == 'up')) {
	return '<i class="icon-cloud-upload"></i>';	
	}
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return str_replace('.com','',$regs['domain']);
  }
  return false;
}

if(isset($_GET['delete-image'])) {
unpublish_image(intval($_GET['delete-image']));
} 
if(isset($_GET['feature-image'])) {
$id = intval($_GET['feature-image']);
if($id){
$db->query("UPDATE ".DB_PREFIX."images set featured = '1' where id='".intval($id)."'");
echo '<div class="msg-info">Video #'.$id.' was featured.</div>';
}
} 
if(isset($_GET['unfeature-image'])) {
$id = intval($_GET['unfeature-image']);
if($id){
$db->query("UPDATE ".DB_PREFIX."images set featured = '0' where id='".intval($id)."'");
echo '<div class="msg-info">Video #'.$id.' was unfeatured.</div>';
}
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
$act = isset($_POST['massaction']) ? $_POST['massaction'] : "unpublish";	
switch ($act) {
case "unpublish":
unpublish_image(intval($del));
break;
case "remove":
delete_image(intval($del));
break;
case "feature":
$db->query("UPDATE ".DB_PREFIX."images set featured = '1' where id='".intval($del)."'");
break;
case "premium":
$db->query("UPDATE ".DB_PREFIX."images set ispremium = '1' where id='".intval($del)."'");
break;	
}

}
echo '<div class="msg-info">Perfomed '.$act.' action on images #'.implode(',', $_POST['checkRow']).'</div>';
}
$order = "ORDER BY ".DB_PREFIX."images.id desc";
$where = "";
$sortA = array();
if(isset($_GET['sort']))  {
$sortA = explode(";",$_GET['sort'] );
$sortA = array_unique(array_filter($sortA));	
if(in_array("featured", $sortA )) {
$where = "and featured > 0";
}
if(in_array("premium", $sortA )) {
$where = "and ispremium > 0";
}
if(in_array("date-asc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.date asc";
}
if(in_array("date-desc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.date desc";
}
if(in_array("website-asc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.source asc";
}
if(in_array("website-desc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.source desc";
}	
if(in_array("liked-asc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.liked asc";
}
if(in_array("liked-desc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.liked desc";
}
if(in_array("views-asc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.views asc";
}
if(in_array("views-desc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.views desc";
}
if(in_array("title-asc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.title asc";
}
if(in_array("title-desc", $sortA )) {
$order = "ORDER BY ".DB_PREFIX."images.title desc";
}

/* End if */
}
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."images where pub >  0 $where ");
$images = $db->get_results("select * from ".DB_PREFIX."images where pub > 0 $where $order ".this_limit()."");

//$db->debug();
?>
<div class="row">
<h3>Images management</h3>				
</div>
<?php
if($images) {
$sort=	implode(";",$sortA );
$ps = admin_url('images').'&sort='.$sort.'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
//$a->show_pages($ps);
if(!empty($sortA)){
echo '<div class="row-fuild" style="margin-bottom:15px"> Active filters:   ';	
foreach ($sortA as $filter){
	echo '<a class="btn btn-mini mright10" href="'.remove_sort($filter).'">'.ucwords(str_replace('-',' : ',$filter)).' <i class="material-icons mleft10">&#xE15C;</i></a>';
}
echo '</div>';	
}
?>
<form class="form-horizontal styled" action="<?php echo admin_url('images');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
<div class="thefilters blc">
<h2>Order by</h2>
	<a class="tipS" title="Order by title ascending" href="<?php echo add_sort('title-asc');?>"><i class="icon-angle-up"></i> Title ascending</a>
	<a class="tipS" title="Order by title descending" href="<?php echo add_sort('title-desc');?>"><i class="icon-angle-down"></i> Title descending</a>
	<a class="tipS" title="Order by likes ascending" href="<?php echo add_sort('liked-asc');?>"><i class="icon-angle-up"></i> Likes ascending</a>
	<a class="tipS" title="Order by likes descending" href="<?php echo add_sort('liked-desc');?>"><i class="icon-angle-down"></i>Likes descending</a>
	<a class="tipS" title="Order by website ascending" href="<?php echo add_sort('website-asc');?>"><i class="icon-angle-up"></i>Website ascending</a>
	<a class="tipS" title="Order by website descending" href="<?php echo add_sort('website-desc');?>"><i class="icon-angle-down"></i>Website descending</a>
	<a class="tipS" title="Order by date ascending" href="<?php echo add_sort('date-asc');?>"><i class="icon-angle-up"></i>Date ascending</a>
	<a class="tipS" title="Order by date descending" href="<?php echo add_sort('date-desc');?>"><i class="icon-angle-down"></i>Date descending</a>
	 <a class="tipS" title="Order by views ascending" href="<?php echo add_sort('views-asc');?>"><i class="icon-angle-up"></i>Views ascending</a>
	<a class="tipS" title="Order by views descending" href="<?php echo add_sort('views-desc');?>"><i class="icon-angle-down"></i>Views descending</a>
	<a class="tipS" title="Show featured only" href="<?php echo add_sort('featured');?>"><i class="icon-star"></i> Featured only</a>
	<a class="tipS" title="Show featured only" href="<?php echo add_sort('premium');?>"><i class="icon-star"></i> Premium only</a>
</div>
<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
                                  <th>
								  <div class="checkbox-custom checkbox-danger">
									<input type="checkbox" name="checkRows" class="check-all" />
									<label for="checkRows"></label>
                                 </div>
								  
								  </th>
                                 <th width="19">
								 </th>
								 <th>
								<div class="inline inline-elements">
								  <select id="massaction" name="massaction" class="select">
								 <option value="premium" selected>Premium selected</option>
								 <option value="feature">Feature selected</option>
								  <option value="unpublish">Unpublish selected</option>
								  <option value="remove">Remove selected</option>								  
								</select>
                                 </div>
								 </th>                               
								  <th>
								  
								 <button class="btn btn-primary btn-sm tipS" type="submit" title="<?php echo _lang("Do mass action"); ?>"><i class="material-icons">&#xE877;</i></button>
								  <span class="pull-right"><?php echo _lang("Images"); ?>	</span>						  
								  </th>
								 
							  </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($images as $image) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $image->id; ?>" class="styled" /></td>
                                   <td class="bord">
								<div class="dropdown">
								<a class="dropdown-toggle" title="Options"  data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button"><i class="material-icons">&#xE164;</i></a>
									<ul class="dropdown-menu dropdown-right bullet" role="menu">
									<li role="presentation"><a title="<?php echo _lang("Edit"); ?>" href="<?php echo admin_url('edit-image');?>&vid=<?php echo $image->id;?>"><i class="icon-pencil"></i>  <?php echo _lang("Edit"); ?></a></li>
									<li class="divider" role="presentation"></li>
									<li role="presentation"><a title="<?php echo _lang("Unpublish"); ?>" href="<?php echo admin_url('images');?>&p=<?php echo this_page();?>&delete-image=<?php echo $image->id;?>"> <i class="icon icon-eraser"></i><?php echo _lang("Unpublish"); ?></a></li>
									<li role="presentation"><a target="_blank" title="<?php echo _lang("Unpublish"); ?>" href="<?php echo admin_url('unimages');?>&p=<?php echo this_page();?>&delete-image=<?php echo $image->id;?>"> <i class="icon icon-trash"></i><span style="color:#f96868; font-weight:bold">Permanently</span> Delete</a></li>
									<li class="divider" role="presentation"></li>
									<li role="presentation"><a class="confirm" target="_blank" title="<?php echo _lang("Ban user"); ?>" href="<?php echo admin_url('users');?>&p=<?php echo this_page();?>&ban=<?php echo $image->user_id;?>"><i class="icon icon-eraser"></i>Ban uploader</a></li>
									<li role="presentation"><a class="confirm" target="_blank" title="<?php echo _lang("Delete user"); ?>" href="<?php echo admin_url('users');?>&p=<?php echo this_page();?>&delete-user=<?php echo $image->user_id;?>"> <i class="icon icon-trash"></i> <span style="color:#f96868; font-weight:bold">Delete uploader </span> </a>
									</li>
									</ul>
                                </div>
								  </td>
								  <td width="164" style="width:164px">
								  <img src="<?php echo thumb_fix($image->thumb, 164,142); ?>" style="width:164px; height:142px;">
								  </td>
                                  <td class="image-td">
								  <a class="image-title-td" target="_blank" href="<?php echo image_url($image->id, $image->title);?>"><strong><?php echo _html($image->title); ?></strong> </a>
								  </td>
								  <tr class="image-dets">
								  <td>&nbsp;</td>	
                                  <td>&nbsp;</td>								  
								   <td><span class="badge"><i class="material-icons">&#xE192;</i> <?php echo time_ago($image->date); ?></span></td>
								  <td colspan="2">
								  <span class="badge">
								  <i class="material-icons">&#xE8DC;</i><?php echo intval($image->liked); ?> 
								  <i class="material-icons">&#xE8DB;</i><?php echo intval($image->disliked); ?> 
								  </span>
								  <span class="badge"><i class="material-icons">&#xE892;</i> <?php echo ucfirst(get_domain($image->source)); ?></span>
								  
								  
                                  
                                  
                                  <span class="badge"><i class="material-icons">&#xE417;</i> <?php echo _html($image->views); ?></span>
								 
								<span class="badge">
								  <?php if($image->featured < 1) { ?>
								  <a  class="tipS" title="<?php echo _lang("Not featured. Click to feature image"); ?>" href="<?php echo canonical(); ?>&feature-image=<?php echo $image->id;?>"><i class="material-icons" style="color: #76838f;">&#xE838;</i></a>
								 <?php } else { ?>
								  <a class="tipS" title="<?php echo _lang("Featured image! Click to remove"); ?>" href="<?php echo canonical(); ?>&unfeature-image=<?php echo $image->id;?>"><i class="material-icons">&#xE838;</i></a>
								 <?php } ?>
								 </span>
										  </td>
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</fieldset>					
</form>
<?php  $a->show_pages($ps); 
}else {
echo '<div class="msg-note">Nothing here yet.</div>';
}

 ?>
