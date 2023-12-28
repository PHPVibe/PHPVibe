<?php if(!is_user()) {redirect(site_url());}
$cid = token_id();
$_conv = $db->get_row("select * from ".DB_PREFIX."conversation where c_id = '".$cid."' and ((user_one='".user_id()."') OR (user_two='".user_id()."'))");
function convBuddy($other){
global $cachedb;	
$op = $cachedb->get_row("SELECT avatar,name FROM ".DB_PREFIX."users where id = '".$other ."' limit  0,1");	
if($op) {
return $op;
}
return false;
}
if($_conv) {
/* Let's get the participants */	
$us= array();
/* Set your profile */	
$us[user_id()]['avatar'] = thumb_fix(user_avatar(), true, 60,60); 
$us[user_id()]['name'] = user_name(); 
$us[user_id()]['profile'] = my_profile();
/* Set the corespondent's profile */
if($_conv->user_one == user_id()) {$other = $_conv->user_two;} else { $other = $_conv->user_one;}
$op= convBuddy($other);
if($op) {
$us[$other]['avatar'] = thumb_fix($op->avatar, true, 60,60); 
$us[$other]['name'] = $op->name; 
$us[$other]['profile'] = profile_url($other, $op->name);
} else {
//User is deleted	
$us[$other]['avatar'] = thumb_fix('storage/uploads/noimage.png', true, 60,60); 
$us[$other]['name'] = _lang('Deleted user'); 
$us[$other]['profile'] = '#';
if(is_empty($_conv->closedby)) {
//Close it automaticaly	
$db->query("Update ".DB_PREFIX."conversation set closedby = '".$other."' where c_id = '".intval($_conv->c_id)."'");	
}	
}
/* End participants */
/* Actions */
if(_get('open')) {
if($_conv->closedby == user_id()) {	
$db->query("Update ".DB_PREFIX."conversation set closedby = null where c_id = '".intval($_conv->c_id)."'");	
$_conv->closedby = '';
}
}
if(_get('close')) {
if(intval($_conv->closedby) <= 1) {	
$db->query("Update ".DB_PREFIX."conversation set closedby = '".user_id()."' where c_id = '".intval($_conv->c_id)."'");	
$_conv->closedby = user_id();
}
}
// SEO Filters
function modify_title( $text ) {
global $us,$other;
 return _lang("Chat with ").$us[$other]['name'];
}	
add_filter( 'phpvibe_title', 'modify_title' );
//Time for design
 the_header();
include_once(TPL.'/conversation.php');
the_footer();
$db->query("Update ".DB_PREFIX."con_msgs set read_at = now() where conv ='".$_conv->c_id."' and by_user <> ".user_id()."");
} else {
// SEO Filters
function modify_title( $text ) {
 return _lang("Inbox");
}	
add_filter( 'phpvibe_title', 'modify_title' );
//Time for design
 the_header();
include_once(TPL.'/conversation.php');
the_footer();
}
?>