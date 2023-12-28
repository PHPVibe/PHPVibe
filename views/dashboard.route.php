<?php // SEO Filters
function modify_title( $text ) {
    return get_option("DashboardSEO",_lang("Your Studio"));
}	
add_filter( 'phpvibe_title', 'modify_title' );
function file_up_support($text) {
$text = '<script type="text/javascript" >
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $(\'#targetImg\').attr(\'src\', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imgInp").change(function(){
        readURL(this);		
    });
  </script>
';
return $text;
}
add_filter( 'filter_extrajs', 'file_up_support');
if(is_user()){
$profile = $db->get_row("SELECT * FROM ".DB_PREFIX."users where id = '".user_id() ."' limit  0,1");	
if(isset($_POST['changeavatar'])) {
if(!is_insecure_file($_FILES['avatar']['name'])) {	
$formInputName   = 'avatar';							
	$savePath	     = ABSPATH.'/storage/uploads';								
	$saveName        = md5(time()).'-'.user_id();									
	$allowedExtArray = array('.jpg', '.png', '.gif');	
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
$uploader -> resizeImage(160, 160, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$avatar = str_replace(ABSPATH.'/' ,'',$thumb);
	user::Update('avatar',$avatar);
	user::RefreshUser(user_id());
	redirect(site_url().'dashboard/?sk=edit&msg='._lang("Avatar changed."));
} else { 
$msg = '<div class="msg-warning">'._lang("Avatar upload failed.").'</div>';
} 
}else {
$msg = '<div class="msg-warning">'._lang("Insecure file detected.").'</div>';
}
}
if(isset($_POST['changecover'])) {
if(!is_insecure_file($_FILES['cover']['name'])) {	
$formInputName   = 'cover';							
	$savePath	     = ABSPATH.'/storage/uploads';								
	$saveName        = md5(time()).'-'.user_id();									
	$allowedExtArray = array('.jpg', '.png', '.gif');	
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
$uploader -> resizeImage(1220, 320, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$cover = str_replace(ABSPATH.'/' ,'',$thumb);
	user::Update('cover',$cover);
	user::RefreshUser(user_id());
	redirect(site_url().'dashboard/?sk=edit&msg='._lang("Cover changed."));
} else {
$msg = '<div class="msg-warning">'._lang("Cover upload failed.").'</div>';

} 
}else {
$msg = '<div class="msg-warning">'._lang("Insecure file detected.").'</div>';
}
} 
//Details change
if(isset($_POST['changeuser'])) {
//var_dump($_POST);	
if(isset($_POST['name'])) { user::Update('name',$_POST['name']); }
if(isset($_POST['city'])) { user::Update('local',$_POST['city']); }
if(isset($_POST['country'])) { user::Update('country',$_POST['country']); }
if(isset($_POST['bio'])) { user::Update('bio',$_POST['bio']); }
if(isset($_POST['gender'])) { user::Update('gender',$_POST['gender']); }
if(isset($_POST['f-link'])) { user::Update('fblink',$_POST['f-link']); }
if(isset($_POST['g-link'])) { user::Update('glink',$_POST['g-link']); }
if(isset($_POST['tw-link'])) { user::Update('twlink',$_POST['tw-link']); }
if(isset($_POST['ig-link'])) { user::Update('iglink',$_POST['ig-link']); }
user::RefreshUser(user_id());
redirect(site_url().'dashboard/?sk=edit&msg='.urlencode(_lang('Channel updated')));
}	
if(isset($_POST['change-password'])) {
if(isset($_SESSION['loggedfrommail']) || ($profile->password == sha1($_POST['oldpassword']))) {	
if($_POST['pass1'] == $_POST['pass2']) {
$msg = '<div class="msg-info">'._lang("Password changed.").'</div>';
user::Update('password',sha1($_POST['pass1']));
} else {
$msg = '<div class="msg-warning">'._lang("Passwords do not match.").'</div>';
}
} else {
$msg = '<div class="msg-warning">'._lang("The old password is incorect.").'</div>';
}
}
include_once(TPL.'/dashboard.php');
the_footer();
} else {
redirect(site_url().'login/');	
}
?>