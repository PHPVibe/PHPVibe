<?php $killcache = true;
include_once('../../load.php');
if(isset($_REQUEST['video_id'])){
$id = intval($_REQUEST['video_id']);
if($id > 0 ) {
$db->query("UPDATE ".DB_PREFIX."images SET views=views+1 WHERE id = '".$id."'");
//$db->debug();
//End tracking
} else {
echo "failed for ".$id;
}
}
?>