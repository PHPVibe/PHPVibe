<?php $killcache = true;
include_once('../../load.php');
if(isset($_REQUEST['video_id'])){
$id = intval($_REQUEST['video_id']);
if($id > 0 ) {
//Track this view
watched($id);	
 //if(!is_watched($id)) {
$db->query("UPDATE ".DB_PREFIX."videos SET views = views+1 WHERE id = '".$id."'");
 //}
//End tracking
}
}
?>