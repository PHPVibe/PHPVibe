<?php //echo token();
$id = toDb(token());
if(is_numeric($id)) {
$db->query("update ".DB_PREFIX."playlists set views = views+1 where id ='".$id ."'");
}
redirect(start_playlist()); 
exit();
?>