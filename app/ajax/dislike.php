<?php include_once('../../load.php');
$id = intval($_REQUEST['video_id']);
if(is_user() && (intval($id) > 0)) {
unlike_video($id);
$_SESSION['ulikes'] = str_replace($id."," , "", $_SESSION['ulikes']);
echo json_encode(array("title"=>_lang('Removed!'),"text"=>_lang('You no longer like this.')));
} 

?>