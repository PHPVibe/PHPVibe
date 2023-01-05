<?php include_once('../../load.php');
$id = intval($_POST['video_id']);
$type = intval($_POST['type']);
$stype = $type + 2;
$tran = array();
$tran[1] = _lang('like');
$tran[2] = _lang('dislike');
$tran[3] = 'like';
$tran[4] = 'dislike';
if(is_user() && ($id > 0)) {
$check = $db->get_row("SELECT count(*) as nr, type FROM ".DB_PREFIX."hearts WHERE vid = '".$id ."' AND uid ='".user_id()."'");
if($check->nr > 0) {
$db->query("DELETE FROM ".DB_PREFIX."hearts WHERE vid = '".$id ."' AND uid ='".user_id()."'");
$db->query("UPDATE ".DB_PREFIX."images set liked = liked-1 where id = '".$id."'");
echo json_encode(array("type"=>2,"title"=>_lang('Ohhh!'),"text"=>_lang('Like removed')));
} else {

$db->query("INSERT INTO ".DB_PREFIX."hearts (`uid`, `vid`, `type`) VALUES ('".user_id()."', '".$id."', '".$tran[$stype]."')");
$db->query("UPDATE ".DB_PREFIX."images set liked = liked+1 where id = '".$id."'");
echo json_encode(array("type"=>1,"title"=>_lang('Success!'),"text"=>_lang('You'). ' '.$tran[$type].' '._lang('this')));
add_activity('9', $id, $tran[$type]);
}

} else {
echo json_encode(array("title"=>_lang('Hmm..have a name stranger?!'),"text"=>'<a href="'.site_url().login.'">'._lang('Please login in order to like like this! It\'s fast and worth it').'</a>'));
}
?>