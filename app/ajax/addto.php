<?php include_once('../../load.php');
$id = intval($_POST['video_id']);
$playlist = intval($_POST['playlist']);
if(is_user() && ($id > 0) && ($playlist > 0)) {
$check = $db->get_row("SELECT count(*) as nr FROM ".DB_PREFIX."playlist_data WHERE video_id = '".$id ."' AND playlist ='".$playlist."'");
if($check->nr > 0) {
echo json_encode(array("title"=>_lang('Already saved'),"text"=>_lang('This was already in your collection')));
} else {
$u = $db->get_row("SELECT owner FROM ".DB_PREFIX."playlists where id ='".$playlist."'");
if($u->owner <> user_id()) {
echo json_encode(array("title"=>_lang('Error'),"text"=>$u->owner._lang('You don\'t seem to own this collection.')));

} else {
$db->query("INSERT INTO ".DB_PREFIX."playlist_data (`playlist`, `video_id`) VALUES ('".intval($playlist)."', '".$id."')");
echo json_encode(array("title"=>_lang('Added to collection!'),"text"=>_lang('Media is now in your collection.')));
}	
}

} else {
echo json_encode(array("title"=>_lang('Hmm..have a name stranger?!'),"text"=>'<a href="'.site_url().login.'">'._lang('Please login in order to collect a video! It\'s fast and worth it').'</a>'));
}
?>