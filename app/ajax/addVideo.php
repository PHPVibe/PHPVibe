<?php  include_once('../../load.php');
function checkRemoteFileImage($url)
{
if((substr($url, 0, 2) == "//") || (substr($url, 0, 4) == "http") ) { 
   return $url;
} else { 
  return 'http://' . $url;
}
$pieces_array = explode('.', $url);
		$ext = end($pieces_array);
$file_supported = array("jpg", "jpeg", "png", "gif");
if(in_array($ext, $file_supported)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
	} else {
	 return false;
	}
}
if (is_user( )) {
if(_post('type') && _post('file') && (isset($_FILES['play-img']) || _post('remote-img'))) {
$sec = _tSec(_post('hours').":"._post('minutes').":"._post('seconds'));
//if is image upload
if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){
$formInputName   = 'play-img';							# This is the name given to the form's file input
	$savePath	     = ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs';								# The folder to save the image
	$saveName        = md5(time()).'-'.user_id();									# Without ext
	$allowedExtArray = array('.jpg', '.png', '.gif');	# Set allowed file types
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
$uploader -> resizeImage(380, 240, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$thumb = str_replace(ABSPATH.'/' ,'',$thumb);
} else { $thumb  = 'storage/uploads/noimage.png'; 	}
} else {
if(checkRemoteFileImage(_post('remote-img'))){
$thumb = _post('remote-img');
} else {
$thumb = 'storage/uploads/noimage.png';
}
}
//Insert video
if(_post('media')) {$mt = _post('media');} else {$mt = 1;}
$token = uniqid();
$category = '';
if(isset($_POST['categ'])) {
$category = implode(',',$_POST['categ']);
}
//print_r($_POST['categ']);
//print_r($category);
//exit();
$db->query("INSERT INTO ".DB_PREFIX."videos 
(`token`,`stayprivate`,`pub`,`source`, `user_id`, `date`, `thumb`, `title`, `duration`, `views` , `liked` , `category`, `nsfw`, `media`) VALUES 
('".$token."','".intval(_post('priv'))."','".intval(get_option('videos-initial'))."','"._post('file')."', '".user_id()."', now() , '".$thumb."', '".toDb(_post('title')) ."', '".$sec."', '0', '0','".toDb($category)."','".toDb(_post('nsfw'))."','".intval($mt)."')");	
$doit = getVideobyToken($token);
add_activity('4', $doit);
//add tags
if(_post('tags')){
	foreach (explode(',',_post('tags')) as $tagul){
		save_tag($tagul,$doit);
	}
}
//add description
save_description($doit,_post('description'));
//Inform
echo '<div class="msg-info">'._post('title').' '._lang("created successfully.").'</div>
<div class="text-center mtop20 mbot20">
<a href="'.site_url().me.'" class="btn btn-default">'._lang("Manage videos").'</a>
<a href="'.site_url().share.'" class="btn btn-primary">'._lang("Share another").'</a>
</div>

';


//remove form
echo'
 <script type="text/javascript" >
$(document).ready(function(){
 $(\'.ajax-form-video\').hide();
	 });

  </script>

';
} else {
echo '<div class="msg-warning">'._lang('Something went wrong: Missing file or thumbnail').'</div>';
}
    
} else {
echo '<div class="msg-warning">'._lang('Login first').'</div>';
}

	?>