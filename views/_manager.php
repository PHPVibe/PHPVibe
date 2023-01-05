<?php if (is_user()) {
$profile = $db->get_row("SELECT * FROM ".DB_PREFIX."users where id = '".user_id()."' limit  0,1");
// Canonical url
$canonical = site_url().me;
if(_get('sk')) { $canonical = site_url().me.'?sk='._get('sk'); }
if(isset($_POST['edited-image']) && !is_null(intval($_POST['edited-image']))) {
$category = '';
if(isset($_POST['categ'])) {
$category = implode(',',$_POST['categ']);
}
$db->query("UPDATE  ".DB_PREFIX."images SET title='".toDb(_post('title'))."',private='".toDb(_post('priv'))."', description='".toDb(_post('description') )."', category='".toDb($category)."', tags='".toDb(_post('tags') )."', nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($_POST['edited-image'])."'");
$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Image updated.").'</div>';
} 
if(isset($_POST['edited-video']) && !is_null(intval($_POST['edited-video']))) {
if($_FILES['play-img'] && !nullval($_FILES['play-img']['name'])){
$formInputName   = 'play-img';							# This is the name given to the form's file input
	$savePath	     = ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs';								# The folder to save the image
	$saveName        = $_POST['edited-token'].'-'.user_id();									# Without ext
	$allowedExtArray = array('.jpg', '.png', '.gif');	# Set allowed file types
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
//$uploader -> resizeImage(200, 200, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$thumb  = str_replace(ABSPATH.'/' ,'',$thumb);
} else { 
$thumb  = 'storage/uploads/noimage.png'; 	
}
if(isset($_POST['categ'])) {
$category = implode(',',$_POST['categ']);
}
$db->query("UPDATE  ".DB_PREFIX."videos SET thumb='".toDb($thumb )."' WHERE user_id= '".user_id()."' and id = '".intval($_POST['edited-video'])."'");
if(isset($_POST['media-type']) && (intval($_POST['media-type']) == 3)) {
$newsource = str_replace(get_option('mediafolder'), "localimage", $thumb);
$db->query("UPDATE  ".DB_PREFIX."videos SET source='".toDb($newsource )."' WHERE user_id= '".user_id()."' and id = '".intval($_POST['edited-video'])."'");

}
} elseif(isset($_POST['remote-thumb']) && not_empty($_POST['remote-thumb']))  {
$db->query("UPDATE  ".DB_PREFIX."videos SET thumb='".toDb($_POST['remote-thumb'])."' WHERE user_id= '".user_id()."' and id = '".intval($_POST['edited-video'])."'");
}
if(!isset($category) && isset($_POST['categ'])) {
$category = implode(',',$_POST['categ']);
}
$db->query("UPDATE  ".DB_PREFIX."videos SET title='".toDb(_post('title'))."',private='".toDb(_post('priv'))."', duration='".intval(_post('duration') )."', category='".toDb($category)."',  nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($_POST['edited-video'])."'");
$db->query("UPDATE  ".DB_PREFIX."description SET description='".toDb(_post('description') )."' where vid = '".intval($_POST['edited-video'])."'");
//add tags
if(_post('tags')){
	clear_tags(intval($_POST['edited-video']));
	foreach (explode(',',_post('tags')) as $tagul){
		save_tag($tagul,intval($_POST['edited-video']));
	}
}
$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Video edited.").'</div>';
refresh_multimedia(intval($_POST['edited-video']));
if(isset($_FILES['subtitle']) && !empty($_FILES['subtitle']['name'])){
if(!is_insecure_file($_FILES['subtitle']['name'])) {	
$fp = ABSPATH.'/storage/'.get_option('mediafolder','media')."/";
$stn = $_FILES['subtitle']['name'];
$stn_ar = explode('.', $stn );
$extension = end($stn_ar);
if((strtolower($extension) == "vtt") || (strtolower($extension) == "srt")) {
$srt = 'subtitle-'.intval($_POST['edited-video']).'-'.uniqid().'.'.$extension;	
$srt_path = $fp.$srt;

if (move_uploaded_file($_FILES['subtitle']['tmp_name'], $srt_path)) {
$db->query("UPDATE  ".DB_PREFIX."videos SET srt='".toDb($srt)."' WHERE id = '".intval($_POST['edited-video'])."'");

	$msg .= '<div class="msg-win mleft20 mright20 mtop20">'._lang("New subtitle file uploaded").'</div>';
	} else {
	$msg .= '<div class="msg-warning mleft20 mright20 mtop20">'._lang("Subtitle upload failed").'</div>';
	}
	
} else {
$msg .= '<div class="msg-warning mleft20 mright20 mtop20">'._lang(".vtt or .srt subtitles only").'</div>';	
} 
}else {
$msg .='<div class="msg-warning mleft20 mright20 mtop20">'._lang("Subtitle failed security check").'</div>';	
}
}
$mediaid = get_jcm_folder(intval($_POST['edited-video'])).'video-'.intval($_POST['edited-video']);
jc_destroy($mediaid);
} 
if(isset($_POST['likesRow'])) {
foreach ($_POST['likesRow'] as $del) {
	unlike_video($del);
}
$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Videos unliked.").'</div>';
}
if(isset($_GET['delete-like'])) {
	unlike_video($_GET['delete-like']);
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Video unliked.").'</div>';

}
if(isset($_GET['delete-video'])) {
	unpublish_video(intval($_GET['delete-video']));
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Video unpublished.").'</div>';
} 
if(isset($_GET['delete-image'])) {
	$id = intval($_GET['delete-image']);
	$db->query("UPDATE ".DB_PREFIX."images SET pub = '0' where id='".$id."' and user_id ='".user_id()."'");
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Image unpublished.").'</div>';
} 
if(isset($_POST['imagesRow'])) {
	$x = implode(",",$_POST['imagesRow']);
	$db->query("UPDATE ".DB_PREFIX."images SET pub = '0' where id in (".toDb($x).") and user_id ='".user_id()."'");
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Images unpublished.").'</div>';

}
if(isset($_POST['playlistsRow'])) {
foreach ($_POST['playlistsRow'] as $del) {
	delete_playlist($del);
}
$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Playlists deleted.").'</div>';
}
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
	unpublish_video(intval($del));
}
$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Media unpublished.").'</div>';
}
if(isset($_GET['delete-playlist'])) {
	delete_playlist($_GET['delete-playlist']);
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Playlist deleted.").'</div>';
}
if(isset($_POST['play-name'])) {
	$picture ='storage/uploads/noimage.png';
	$formInputName   = 'play-img';							# This is the name given to the form's file input
		$savePath	     = ABSPATH.'/storage/uploads';								# The folder to save the image
		$saveName        = md5(time()).'-'.user_id();									# Without ext
		$allowedExtArray = array('.jpg', '.png', '.gif');	# Set allowed file types
		$imageQuality    = 100;
	$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
	if ($uploader->getIsSuccessful()) {
	//$uploader -> resizeImage(200, 200, 'crop');
	$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
	$thumb  = $uploader->getTargetPath();
	$picture  = str_replace(ABSPATH.'/' ,'',$thumb);
	} else { $picture  = ''; 	}

	$db->query("INSERT INTO ".DB_PREFIX."playlists (`owner`, `title`, `picture`, `description`) VALUES ('".user_id()."','".toDb($_POST['play-name'])."', '".toDb($picture)."' , '".toDb($_POST['play-desc'])."')");
	$msg = '<div class="msg-info mleft20 mright20 mtop20">'._lang("Playlist created.").'</div>';
}
if(isset($msg)) {
// Info filter
function modify_info( $text ) {
global $msg;
    return $text.$msg;
}
add_filter( 'the_defaults' , 'modify_info' );
}
// SEO Filters
function modify_title( $text ) {
    return strip_tags(stripslashes(user_name( )));
}
add_filter( 'phpvibe_title', 'modify_title' );
//Time for design
 the_header();
include_once(TPL.'/manager.php');
 the_footer(); 
 if(isset($_POST)) {
  $db->clean_cache();	 
 }
} else {
redirect(site_url().'login');
}
?>