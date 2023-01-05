<div class="row">
<section class="panel">
<div class="panel-heading">Website activity</div>
<div class="panel-body" >
<?php 
$pagestructurex = admin_url()."?sk=activity&p=";
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."activity ");
//Latest notifications
$avq = "Select ".DB_PREFIX."activity.*, ".DB_PREFIX."users.avatar,".DB_PREFIX."users.id as pid, ".DB_PREFIX."users.name from ".DB_PREFIX."activity left join ".DB_PREFIX."users on ".DB_PREFIX."activity.user=".DB_PREFIX."users.id ORDER BY ".DB_PREFIX."activity.id DESC ".this_limit();
$notif = $db->get_results($avq);
$note = array();
if($notif) {
foreach ($notif as $buzz) {
$did = get_activity($buzz);		//print_R($did);
$note[$buzz->id]['image'] = thumb_fix($buzz->avatar, true, 15, 15);
$note[$buzz->id]['text'] = '<div class="aBody"><a target="_blank" href="'.profile_url($buzz->pid, $buzz->name).'">'.$buzz->name.'</a> '.$did["what"].' '.$did["content"].'</div><div class="aTime">'.time_ago($buzz->date).'</div>';
} 	
}
if(isset($note) && !nullval($note)) {
	echo '<ul id="notes">';
	foreach ($note as $n) {
		echo '<li><div class="aInner"><img src="'.$n['image'].'" style="width:19px; height:19px;"/>'.$n['text'].' <br style="clear:both"/></div></li>';
		
	}
	echo '</ul>';
}
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($pagestructurex);

?>
</div>
</div>
