<?php
if(isset($_GET['delete-page'])) {
$db->query("DELETE from ".DB_PREFIX."pages where pid = '".intval($_GET['delete-page'])."' ");
echo '<div class="msg-info">Page deleted</div>';
} 
$menux = array("0" => "No","1" => "Yes" );
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."pages");
$pages = $db->get_results("select * from ".DB_PREFIX."pages ORDER BY pid DESC ".this_limit()."");
?>
<div class="row bottom20">
<h3>Pages<a class="pull-right btn btn-success inline-block" href="<?php echo admin_url('create-page'); ?>"> New page</a></h3>				
</div>
<?php
if($pages) {
$ps = admin_url('pages').'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
?>
<div class="panel top10 multicheck">
<div class="panel-body">
 <div class="multilist">
<ul class="list-group">
						  <?php foreach ($pages as $page) { ?>
                              <li class="list-group-item">
	                         <div class="row">
							 <div class="inline-block img-hold">
							 <div class="inline-block right20 img-checker">
                              <img class="row-image" data-name="<?php echo $page->title; ?>" src="<?php echo thumb_fix($page->pic, true, get_option('thumb-width'), get_option('thumb-height')); ?>" />
                               </div> 
							   <div class="inline-block right20 img-txt">
								<h4><?php echo _html($page->title); ?></h4>
								<div class="img-det-text">
								 <i class="material-icons">timelapse</i> <?php echo time_ago($page->date); ?>
								</div>
								</div> 
								</div> 
								                                 							  
								 
								<div class="btn-group btn-group-vertical pull-right">
								   <a class="btn btn-success btn-sm btn-outline tipS" target="_blank" href="<?php echo page_url($page->pid, $page->title);?>" title="<?php echo _lang("View"); ?>">
								<i class="material-icons mright10"> pageview </i> view
								   </a>
								   <a class="btn btn-primary btn-sm btn-raised tipS" href="<?php echo admin_url('edit-page');?>&pid=<?php echo $page->pid;?>" title="<?php echo _lang("Edit"); ?>">
								<i class="material-icons mright10"> edit </i> modify
								   </a>
								   <a class="btn btn-default btn-sm btn-outline tipS" href="<?php echo $ps;?>&p=<?php echo this_page();?>&delete-page=<?php echo $page->pid;?>" title="Delete">
		                        <i class="material-icons mright10"> delete </i>  delete
								   </a>
                                 </div>
								
								</div>
                              </li>
							   <?php } ?>
						</ul>  

</div>		
				

<?php  $a->show_pages($ps); 
}else {
echo '<div class="msg-note">Nothing here yet.</div>';
}
?>
 </div>	
</div>	
