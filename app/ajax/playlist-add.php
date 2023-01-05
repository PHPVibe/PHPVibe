<?php include_once('../../load.php');
$add_video = intval($_POST['vp-id']);
if(is_user() && ($add_video > 0) && ($_SESSION['token'] == $_POST['token'])) {
if($_POST['booked']) {
foreach ($_POST['booked'] as $play){
if($play && intval($play) > 0) {
$db->query("INSERT INTO ".DB_PREFIX."playlist_data (`playlist`, `video_id`) VALUES ('".intval($play)."', '".$add_video."')");
add_activity('2', $add_video, $play);
}
}
echo '<div class="msg-info">'._lang('All done!').'</div>';
} else {
echo '<div class="msg-info">'._lang('No playlist selected').'</div>';
}
} 
?>