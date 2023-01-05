<?php  //Notifications
if(is_user() && (token() == "noty")) {
if(isset($_SESSION['lastNoty'])) {
if(($_SESSION['lastNoty'] - time()) < 1  ) { $continue = true;  } else { $continue = false;}
} else {
$continue = true;
}
$count = array("msg" => 0, "buzz" =>0);
if($continue){
$notif = $db->get_row("Select count(*) as nr from ".DB_PREFIX."activity where ((type not in (8,9) and ".DB_PREFIX."activity.object in (select id from ".DB_PREFIX."videos where user_id ='".user_id()."' ) ) or (type in (8,9) and ".DB_PREFIX."activity.object in (select id from ".DB_PREFIX."images where user_id ='".user_id()."' ) ) and user <> '".user_id()."') and `date` > '".user_noty()."'");
if($notif) {
$count["buzz"] = $notif->nr;	
}
$lists = $db->get_row("select count(case when read_at = 0 and (by_user <> '".user_id()."') then 1 else null end) as unread  from ".DB_PREFIX."conversation p1 INNER JOIN ( SELECT * FROM ".DB_PREFIX."con_msgs order by at_time desc ) p2 on p2.conv = p1.c_id  where ((p1.user_one='".user_id()."') OR (p1.user_two='".user_id()."'))");

if($lists) {
$count["msg"] = $lists->unread;	
}

$_SESSION['lastNoty'] = time();	
}
echo json_encode($count);
//End notifications
LastOnline();
exit();	
} elseif(!is_user() && (token() == "noty")) {
	$count["buzz"] = 0;
	$count["msg"] = 0;
echo json_encode($count);	
}
if(token() == "categories") {
$list = _get('list');
echo '
<div class="cats sidebar-nav">
<h4 class="li-heading">'._lang("Filter by category").'</h4>
'.the_nav($list).'
</div>
';	
exit();	
}
if(token() == "autoplay") {
if(isset($_SESSION['autoplayoff'])) {
unset($_SESSION['autoplayoff']);
setcookie('autoplayoff', '', -3600,'/', cookiedomain());
} else {
$_SESSION['autoplayoff'] = 1;
setcookie('autoplayoff', 1 , time() + 60 * 60 * 24 * 5,'/', cookiedomain());
}
exit();	
}
/* Playlist data */
if(token() == "playlist") {
$list = _get('list');
    if(is_empty($list)) {
	exit("Bad list id");
    }
$video = new stdClass();
$video->id = _get('videoid');
$video->user_id = _get('idowner');
$video->owner =  getUserName(_get('idowner'));
//print_r($video);
echo '<ul>';

layout('layouts/list');
echo '</ul>';
}
/* End list items */
/* Related videos */
if(token() == "relatedvids") {	
$video_id = _get('videoid');
if(is_empty($video_id)) {
	exit("Bad video id");
}
$titles = $cachedb->get_row("SELECT title FROM ".DB_PREFIX."videos where id= '".$video_id."'");
$video = new stdClass();
//Query tags
$video->tags = $cachedb->get_var("SELECT GROUP_CONCAT(`tag_name` SEPARATOR ',') FROM ".DB_PREFIX."tag_names WHERE id in (select tag_id from ".DB_PREFIX."tag_rel where media_id = '".$video_id."')");
$video->category = _get('videocategory');
$video->media = _get('videomedia');
$video->id = $video_id;
$video->title = $titles->title;
echo '<ul>';
layout('layouts/related');
echo '</ul>';
}
/* End related videos *//* Edit in place */if(token() == "xedit") {		$list = intval($_REQUEST['pk']);	$name = toDb($_REQUEST['name']);	$user = user_id();	$value = toDb($_REQUEST['value']);	if($list > 0 ) {				if($name == 'playtitle') {		$field = 'title';			}		if($name == 'playdesc') {		$field = 'description';			}					$db->query("update ".DB_PREFIX."playlists set ".$field." = '".$value."' where id = '".$list."' and owner = '".$user."'");			header( "HTTP/1.1 200 OK" );	echo $value;	} else { 	header('HTTP/1.0 400 Bad Request', true, 400);	echo "This field is required!";	}	}

//Hooks
do_action('phpvibe-api');

?>