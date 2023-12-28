<?php $receiver =  intval(token());
if(!is_user()) {redirect(site_url().'login/');}
if($receiver > 1) {
$q=$db->get_row("SELECT c_id FROM ".DB_PREFIX."conversation WHERE (user_one='".user_id()."' AND user_two='$receiver') OR (user_one='$receiver' AND user_two='".user_id()."')");
if($q && isset($q->c_id)){
/* We have a conversation  */	
redirect(site_url().'conversation/'.$q->c_id);		
} else {
/* No conversation, let's start one */
$q=$db->query("INSERT INTO ".DB_PREFIX."conversation (user_one,user_two) VALUES ('$receiver','".user_id()."')");	
$last = $db->insert_id;
/* Sometimes this EzSql method fails, so...fail safe */
if(nullval($last) || (intval($last) < 1)) {
$q=$db->get_row("SELECT c_id FROM ".DB_PREFIX."conversation WHERE (user_one='".user_id()."' AND user_two='$receiver') OR (user_one='$receiver' AND user_two='".user_id()."')");
if($q) { $last = $q->c_id; }	
}
redirect(site_url().'conversation/'.$last);
}
} else {
/* No conversation token */	
redirect(site_url());	
}
exit();
?>