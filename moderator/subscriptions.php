<div class="row">
<div class="panel panel-bordered">
<div class="panel-heading">
<h3 class="panel-title">
Premium subscriptions
</h3>
</div>
<div class="panel-body nopad">
<?php 
$pagestructurex = admin_url()."?sk=subscriptions&p=";
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."user_subscriptions ");
$payments = $db->get_results("select a.*, b.name from ".DB_PREFIX."user_subscriptions a left join ".DB_PREFIX."users b on a.user_id = b.id order by id desc ".this_limit()); 
?>
<ul class="list-group iconed-xlist">
<?php
if($payments) {
	foreach ($payments as $paid) {
echo '<li class="list-group-item">
 <strong>'.$paid->name.'</strong> <a href="'.admin_url('edit-user').'&id='.$paid->user_id.'">[edit]</a> <a href="'.profile_url($paid->user_id, $paid->name).'" target="_blank">[profile]</a> got premium <strong>'.time_ago($paid->valid_from).'</strong> '._lang('with').' '.$paid->payment_method.' '.$paid->payer_email.'  valid until '.$paid->valid_to.' <span class="badge">'.$paid->payment_gross.' '.$paid->currency_code.' </span>
     </li>';
	}
	
} else {
echo '<div class="block isBoxed msg-content msg-note mbot20">'._lang("No past subscriptions").'</div>';

}

?>
</ul>
</div>
</div>
<?php
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($pagestructurex);

?>
</div>