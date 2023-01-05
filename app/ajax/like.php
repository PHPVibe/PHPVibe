<?php include_once('../../load.php');
$id = intval($_REQUEST['video_id']);
$type = intval($_REQUEST['type']);
$stype = $type + 2;
$tran = array();
$tran[1] = _lang('like');
$tran[2] = _lang('dislike');
$tran[3] = 'like';
$tran[4] = 'dislike';
if(is_user() && ($id > 0)) {
$check = $db->get_row("SELECT count(*) as nr, type FROM ".DB_PREFIX."likes WHERE vid = '".$id ."' AND uid ='".user_id()."' order by id desc");
if($check->nr > 0) {
if(($check->type == 'like') && ($type == 1)) {
//If already liked
//Remove liked
$db->query("delete from ".DB_PREFIX."likes where uid ='".user_id()."' and vid='".$id."' and type = 'like'");
$db->query("UPDATE ".DB_PREFIX."videos set liked = liked-1 where id = '".$id."'");	
echo json_encode(array("added"=>"0","title"=>_lang('Hmm!'),"text"=>_lang('Like removed!')));
unset($_SESSION['ulikes']);
//$db->debug();
$db->query("DELETE FROM ".DB_PREFIX."playlist_data WHERE `playlist` = '".likes_playlist()."' and `video_id` = '".$id."')");
//$db->debug();
} elseif(($check->type == 'like') && ($type == 2)) {	
// If disliked over existing like
//Remove liked
$db->query("delete from ".DB_PREFIX."likes where uid ='".user_id()."' and vid='".$id."' and type = 'like'");
$db->query("UPDATE ".DB_PREFIX."videos set liked = liked-1 where id = '".$id."'");
$db->query("DELETE FROM ".DB_PREFIX."playlist_data WHERE `playlist` = '".likes_playlist()."' and `video_id` = '".$id."'");
unset($_SESSION['ulikes']);
//$db->debug();
//Add dislike
$db->query("INSERT INTO ".DB_PREFIX."likes (`uid`, `vid`, `type`) VALUES ('".user_id()."', '".$id."', '".$tran[$stype]."')");
$db->query("UPDATE ".DB_PREFIX."videos set ".$tran[$stype]."d = ".$tran[$stype]."d+1 where id = '".$id."'");
echo json_encode(array("added"=>"1","title"=>_lang('Ohh!'),"text"=>_lang('You dislike this!')));
//$db->debug();
} elseif(($check->type == 'dislike') && ($type == 1)) {
//If liking a disliked video
//Remove dislike
$db->query("delete from ".DB_PREFIX."likes where uid ='".user_id()."' and vid='".$id."' and type = 'dislike'");
$db->query("UPDATE ".DB_PREFIX."videos set disliked = disliked-1 where id = '".$id."'");
//Add like
$db->query("INSERT INTO ".DB_PREFIX."likes (`uid`, `vid`, `type`) VALUES ('".user_id()."', '".$id."', '".$tran[$stype]."')");
$db->query("UPDATE ".DB_PREFIX."videos set ".$tran[$stype]."d = ".$tran[$stype]."d+1 where id = '".$id."'");
$db->query("INSERT INTO ".DB_PREFIX."playlist_data (`playlist`, `video_id`) VALUES ('".likes_playlist()."', '".$id."')");
echo json_encode(array("added"=>"1","title"=>_lang('Ohh!'),"text"=>_lang('You like this!')));
$_SESSION['ulikes'] .= $id.",";
//$db->debug();
}elseif(($check->type == 'dislike') && ($type == 2)) {
//If disliking a disliked video
//Remove dislike
$db->query("delete from ".DB_PREFIX."likes where uid ='".user_id()."' and vid='".$id."' and type = 'dislike'");
$db->query("UPDATE ".DB_PREFIX."videos set disliked = disliked-1 where id = '".$id."'");
echo json_encode(array("added"=>"2","title"=>_lang('Hooray!'),"text"=>_lang('Dislike removed!')));	
//$db->debug();
} else {
echo json_encode(array("added"=>"2","title"=>_lang('Hmm!'),"text"=>_lang('Something is wrong here!')));	
}
} else {
//Not yet rated	
$db->query("INSERT INTO ".DB_PREFIX."likes (`uid`, `vid`, `type`) VALUES ('".user_id()."', '".$id."', '".$tran[$stype]."')");
$db->query("UPDATE ".DB_PREFIX."videos set ".$tran[$stype]."d = ".$tran[$stype]."d+1 where id = '".$id."'");
if($type == 1) {
$db->query("INSERT INTO ".DB_PREFIX."playlist_data (`playlist`, `video_id`) VALUES ('".likes_playlist()."', '".$id."')");
echo json_encode(array("added"=>"1","title"=>_lang('Hooray!'),"text"=>_lang('You'). ' '.$tran[$type].' '._lang('this')));
//$db->debug();
$_SESSION['ulikes'] .= $id.",";
} else {
echo json_encode(array("added"=>"3","title"=>_lang('Oh!'),"text"=>_lang('You'). ' '.$tran[$type].' '._lang('this')));
}
add_activity('1', $id, $tran[$type]);

}

} else {
echo json_encode(array("added"=>"0","title"=>_lang('Hmm..have a name stranger?!'),"text"=>_lang('Please login in order to like a video! It\'s fast and worth it')));
}
$mediaid = get_jcm_folder($id).'video-'.$id;jc_destroy($mediaid);
//$db->debug();
?>