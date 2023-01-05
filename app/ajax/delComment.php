<?php  error_reporting(E_ALL);
include_once('../../load.php');
if (is_user( ) && isset($_REQUEST['key']) && isset($_REQUEST['id'])) {
   
   if(is_moderator()) {
	$db->query("DELETE FROM ".DB_PREFIX."em_comments where `access_key` = '".toDb($_REQUEST['key'])."'");  
   } else {
	$db->query("DELETE FROM ".DB_PREFIX."em_comments where `access_key` = '".toDb($_REQUEST['key'])."' and `id` = '".toDb($_REQUEST['id'])."' and `sender_id` = '".toDb(user_id())."'");   
   }
   if($db->rows_affected > 1) {
	$db->query("DELETE FROM ".DB_PREFIX."em_comments where `reply` = '".toDb($_REQUEST['id'])."'");   
   }
  echo json_encode(array('text'  => _lang('Done'))); 
}

	?>