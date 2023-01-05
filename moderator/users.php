<?php 
if(isset($_GET['delete-user'])) {
delete_user(intval($_GET['delete-user']));
} 
if(isset($_GET['ban'])) {
user::BanUser(intval($_GET['ban']));
}
if(isset($_GET['removeban'])) {
user::ChangePass(intval($_GET['removeban']) , md5(intval($_GET['removeban']).time().date()));
}
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
delete_user(intval($del));
}
}
if(isset($_GET['term'])) {
$term = toDb($_GET['term']);
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users where name like '".$term."' or name like '%".$term."' or name like '%".$term."&' or email like '".$term."' ");
$users = $db->get_results("select * from ".DB_PREFIX."users where name like '".$term."' or name like '%".$term."' or name like '%".$term."%' or email like '%".$term."%' order by id DESC ".this_limit()."");
//active
}elseif(isset($_GET['group'])) {
$g = toDb($_GET['group']);
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users where group_id = '".$g."' ");
$users = $db->get_results("select * from ".DB_PREFIX."users where group_id = '".$g."' order by lastNoty DESC ".this_limit()."");
//active
} elseif(isset($_GET['sort'])) {
if($_GET['sort'] == "active") {
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users where id in (SELECT DISTINCT user FROM ".DB_PREFIX."activity)");
$users = $db->get_results("select * from ".DB_PREFIX."users where id in (SELECT DISTINCT user FROM ".DB_PREFIX."activity) order by id DESC ".this_limit()."");
//active
} else {
//inactive
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users where id not in (SELECT DISTINCT user FROM ".DB_PREFIX."activity)");
$users = $db->get_results("select * from ".DB_PREFIX."users where id not in (SELECT DISTINCT user FROM ".DB_PREFIX."activity) order by id DESC ".this_limit()."");

}
} else {
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users");
$users = $db->get_results("select * from ".DB_PREFIX."users order by id DESC ".this_limit()."");
}
?>
<div class="row">
		    	<form class="search widget" action="" method="get" onsubmit="location.href='<?php echo admin_url('users'); ?>&term=' + encodeURIComponent(this.key.value); return false;">
		    		<div class="autocomplete-append">			   
			    		<input type="text" name="key" placeholder="Search user..." id="key" />
			    		<input type="submit" class="btn btn-primary" value="Search" />
			    	</div>
		    	</form>
</div>
<div class="row">
<div class="thefilters blc">
<h2>Filter</h2>
	<?php
	$groups = $db->get_results("select * from ".DB_PREFIX."users_groups order by id ASC ".this_limit()."");
	if($groups) {
	$pp = admin_url('users').'&group=XXX&p=1';	
	foreach ($groups as $group) {
		echo '<a href="'.str_replace("XXX", $group->id,$pp).'"><i class="material-icons">&#xE152;</i> '.$group->name.'</a>';
		
	}
	}
	
	?>
	
	</div>
</div>
<?php
if($users) {
if(isset($term)){
$ps = admin_url('users').'&term='.$term.'&p=';
}elseif(isset($_GET['sort'])) {
$ps = admin_url('users').'&sort='.$_GET['sort'].'&p=';
}elseif(isset($_GET['group'])) {
$ps = admin_url('users').'&group='.$_GET['group'].'&p=';
} else {
$ps = admin_url('users').'&p=';
}
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
//$a->show_pages($ps);
?>

<form class="form-horizontal styled" action="<?php echo $ps;?><?php echo this_page();?>" enctype="multipart/form-data" method="post">
<div class="panel top10 multicheck">
<fieldset>
<div class="panel-heading">
<h3 class="panel-title">
<i class="material-icons">
supervised_user_circle
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
						  <?php foreach ($users as $video) { ?>
                             <li class="list-group-item">
	                         <div class="row">
							  <div class="inline-block img-hold">
							  <div class="inline-block right20 img-checker">
							  <span class="pull-left mg-t-xs mg-r-md top20">
                                 <input type="checkbox" name="checkRow[]" value="<?php echo $video->id; ?>" class="styled" />
								</span> 
                                 <img class="row-image" src="<?php echo thumb_fix($video->avatar); ?>">
								</div>
								<div class="inline-block right20 img-txt">
								<h4><?php echo _html($video->name); ?></h4> 
								<div class="img-det-text">
								<i class="material-icons"> video_call </i> <?php echo count_uvid($video->id); ?>
								<i class="material-icons"> camera_alt </i> <?php echo count_uimgs($video->id); ?>
								<i class="material-icons"> show_chart </i> <?php echo count_uact($video->id); ?>
								</div> 
								</div>
								 <?php echo _html($video->bio); ?>
								 </div>
								 <div class="btn-group btn-group-vertical pull-right">
								  <a class="btn btn-default btn-sm btn-outline" href="<?php echo profile_url($video->id, $video->name); ?>" target="_blank">
								<i class="material-icons mright20"> supervised_user_circle </i> view
								  </a>

								  <a class="btn btn-primary btn-sm btn-raised" href="<?php echo admin_url('edit-user');?>&id=<?php echo $video->id;?>">
								<i class="material-icons mright10"> edit </i> modify
								  </a>
								 <?php 
							if(!_contains($video->pass, 'banned')) { ?>
								 <a class="tipS btn btn-primary btn-sm btn-danger" title="Ban user" href="<?php echo admin_url('users');?>&p=<?php echo this_page();?>&ban=<?php echo $video->id;?>">
								 <i class="material-icons mright20">&#xE14B;</i> ban
								 </a>
							<?php } else { ?>  
							<a class="btn btn-primary btn-sm btn-danger tipS  title="Remove ban on user" href="<?php echo admin_url('users');?>&p=<?php echo this_page();?>&removeban=<?php echo $video->id;?>">
							<i class="material-icons mright20">&#xE15D;</i> unban
							</a>
							<?php } ?>  
								  <a class="btn btn-default btn-sm btn-outline tipS confirm" title="Remove user account" href="<?php echo admin_url('users');?>&p=<?php echo this_page();?>&delete-user=<?php echo $video->id;?>">
								  
								 <i class="material-icons mright10"> delete </i>  delete
								  </a>

								  </div>
								  
								  </div>	
								  
                              </li>
							  <?php } ?>
						</div>	 
</div>	
</div>						
</fieldset>					
</form>
<?php  $a->show_pages($ps); } else { echo "No user found."; } ?>
